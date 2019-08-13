<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-ddb 多库-主从-分表解决方案
// +----------------------------------------------------------------------

require_once("driver/dbbase.mark.php");

class dbhandlerMark {
	
	protected static $dbArr = []; 		// 存储 driver，db对象
	protected $db = NULL; 				//DB引擎对象
	protected $driverArr = [];
	protected $dbModel = NULL; 			//DB配置模型，默认为default

	/**
	 * 数据库初始化，DB切换入口  
	 * 1. 可以在使用中通过$this->init_db('test')来切换数据库
	 * 2. 该函数是DB默认初始化入口
	 * 3. 支持多数据库链接，主从，随机分布式数据库
	 * @param obj $db
	 */
	public function init_db($db = '') {
		$markphp_conf = MarkPHP::getConfig();
		$this->dbModel = ($db == '') ? 'default' : $db;  //Db模型
		$driver  = $markphp_conf['db']['driver']; //Db引擎
		if (isset(self::$dbArr[$this->dbModel])) {
			return true;
		}
		if (!isset($markphp_conf['db'][$this->dbModel])) {
			MarkPHP::markError('database confing model {'.$this->dbModel.'} is error!');
		}

		$db_type 	= $markphp_conf['db'][$this->dbModel]['db_type'];
		$config 	= $markphp_conf['db'][$this->dbModel];
		switch ($db_type) {
			case 1: //主从模型 
				$key = floor(mt_rand(1,(count($config) - 2)));
				self::$dbArr[$this->dbModel]['master']['link_id'] = $this->db_connect($config[0], $driver);
				self::$dbArr[$this->dbModel]['slaver']['link_id'] = $this->db_connect($config[$key], $driver);
				break;
			case 2: //主主-随机模型
				$key = floor(mt_rand(0,count($config) - 2));
				self::$dbArr[$this->dbModel]['link_id'] = $this->db_connect($config[$key], $driver);
				break;
				
			default: //默认单机模型
				self::$dbArr[$this->dbModel]['link_id'] = $this->db_connect($config[0], $driver);
				break;
		}
		return true;
	}

	/**
	 * 获取link_id 数据库链接资源符
	 * @param string $sql SQL语句进行分析
	 * @return object
	 */
	protected function get_link_id($sql = "") {
		$markphp_conf = MarkPHP::getConfig();
		$db_type = $markphp_conf['db'][$this->dbModel]['db_type'];

		//如果sql语句为空，则直接返回link_id
		if ($sql == "") {
			$this->db->link_id = self::$dbArr[$this->dbModel]['link_id'];
			return $this->db->link_id;
		}
		if ($db_type == 1) { //主从
			if ($this->is_insert($sql)) {
				$this->db->link_id = self::$dbArr[$this->dbModel]['master']['link_id'];
			} else {
				$this->db->link_id = self::$dbArr[$this->dbModel]['slaver']['link_id'];
			}
		} else {
			$this->db->link_id = self::$dbArr[$this->dbModel]['link_id'];
		}
		return $this->db->link_id;
	}

	//每次query执行完毕后，都会将默认的link_id指向默认数据库链接地址 , 加快执行执行性能，无需查找
	protected function set_default_link_id() {
		if (isset(self::$dbArr['default'])) {
			$this->dbModel = 'default';
			$this->db->link_id = self::$dbArr[$this->dbModel]['link_id'];
			return true;
		}
		return false;
	}

	private function db_connect($config, $driver) {
		$host      = $config['host'];
		$user      = $config['username'];
		$password  = $config['password'];
		$database  = $config['database'];
		$charset   = $config['charset'];
		$pconnect  = $config['pconnect'];
		$driver    = (!isset($driver)) ? 'mysql' : $driver;
		if ($this->db == NULL) {
			$this->db  = $this->get_db_driver($driver); //DB对象
		}
		return $this->db->connect($host, $user, $password, $database, $charset, $pconnect);
	}

	//获取mysql 引擎
	private function get_db_driver($driver) {
		$file  = $driver . '.mark.php';
		$class = $driver . 'Mark';
		require(MARKPHP_PATH . '/core/dao/db/driver/' . $file);
		return MarkPHP::loadclass($class);
	}

	//sql 分析是否写
	private function is_insert($sql) {
		$sql = trim($sql);
		$sql_temp = strtoupper(substr($sql, 0, 6));
		if ($sql_temp == 'SELECT') return false;
		return true;
	}

}
