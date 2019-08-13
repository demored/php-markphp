<?php
// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | 框架核心父类，所有类运行的基础
// +----------------------------------------------------------------------

if (!defined('IS_MARKPHP')) exit('Access Denied!');

class coreMark {

	//单例容器
	protected static $instance = array();
	private $mark_path = array(
				'd' => '/core/dao/',
				's' => '/core/service/',
				'c' => '/core/controller/',
				'v' => '/core/view/',
				'u' => '/core/util/',
				'l' => '/core/library/',
			);

	public function __construct() {
		$this->run_register_global(); //注册全局变量
	}

	//框架核心加载-框架的所有类都需要通过该函数
	public function load($class_name, $type) {
		$class_path = $this->get_class_path($class_name, $type);
		$class_name = $this->get_class_name($class_name);
		if (!file_exists($class_path))
			markphp::markError('file '. $class_name . '.php is not exist!');

		if (!isset(self::$instance['markphp'][$type][$class_name])) {
			require_once($class_path);	// 引入该文件
			if (!class_exists($class_name))
				markphp::markError('class' . $class_name . ' is not exist!');

			self::$instance['markphp'][$type][$class_name] = new $class_name;
		}
		return self::$instance['markphp'][$type][$class_name];
	}

	public function getLibrary($class) {
		return $this->load($class, 'l');
	}

	public function getUtil($class) {
		return $this->load($class, 'u');
	}

	public function getCache() {
		if (self::$instance['markphp_cache'] == NULL) {
			$dao = $this->load('dao', 'd');
			self::$instance['markphp_cache'] = $dao->run_cache(); //初始化cahce
		}
		return self::$instance['markphp_cache'];
	}

	public function getNosql() {
		if (self::$instance['markphp_nosql'] == NULL) {
			$dao = $this->load('dao', 'd');
			self::$instance['markphp_nosql'] = $dao->run_nosql(); //初始化nosql
		}
		return self::$instance['markphp_nosql'];
	}

	public function getMongo($server = 'default') {
		$instance_name = 'markphp_mongo_' . $server;
		if (self::$instance[$instance_name] == NULL) {
			self::$instance[$instance_name] = $this->getNosql()->init('MONGO', $server);
		}
		return self::$instance[$instance_name];
	}

	public function getRedis($server = 'default') {
		$instance_name = 'markphp_redis_' . $server;
		if (self::$instance[$instance_name] == NULL) {
			self::$instance[$instance_name] = $this->getNosql()->init('REDIS', $server);
		}
		return self::$instance[$instance_name];
	}

	public static function getM() {
		$markphp_conf = markphp::getConfig();
		if ($markphp_conf['ismodule'] === false) return '';
		if ($_GET['m'] == '')
			return $markphp_conf['controller']['default_module'];
		return $_GET['m'];
	}

	public static function getC() {
		$markphp_conf = markphp::getConfig();
		if ($_GET['c'] == '') return $markphp_conf['controller']['default_controller'];
		return $_GET['c'];
	}

	public static function getA() {
		$markphp_conf = markphp::getConfig();
		if ($_GET['a'] == '') return $markphp_conf['controller']['default_action'];
		return $_GET['a'];
	}

	//注册到框架全局可用变量
	public function register_global($name, $value) {
		self::$instance['global'][$name] = $value;
		$this->$name = $value;
	}

	//运行全局变量
	private function run_register_global() {
		if (isset(self::$instance['global']) && !empty(self::$instance['global'])) {
			foreach (self::$instance['global'] as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	//获取系统类文件路径
	private function get_class_path($class_name, $type) {
		$class_path = $this->mark_path[$type] . $class_name . '.mark.php';
		$class_path = MARKPHP_PATH . $class_path;
		return $class_path;
	}

	//获取类名称
	private function get_class_name($class_name) {
		return $class_name . 'Mark';
	}

}