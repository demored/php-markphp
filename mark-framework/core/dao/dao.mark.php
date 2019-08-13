<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-dao Dao基类包含 数据库、缓存、nosql
// +----------------------------------------------------------------------

class daoMark{
	
	public $db = NULL;
	public $cache_ins = NULL;
	public $nosql_ins = NULL;

	//实例化数据库
	public function run_db() {
		if ($this->db == NULL) {
			require(MARKPHP_PATH . "/core/dao/db/db.mark.php");
			$this->db = MarkPHP::loadclass('dbMark');
			$this->db->init_db('');
		}
		return $this->db;
	}

	public function cache($type = 'file'){
		if ($this->cache == NULL) {
			require(MARKPHP_PATH . "/core/dao/cache/cache.mark.php");
			$this->cache_ins = MarkPHP::loadclass('cacheMark');
			$this->cache_ins->db_handle = $this->db;
		}
		$this->cache_ins->cache = $this->cache->get_cache_handle($type);
		return $this->cache_ins->cache;
	}
	public function nosql($type = 'redis'){
		if ($this->nosql_ins == NULL) {
			require(MARKPHP_PATH . "/core/dao/nosql/nosql.mark.php");
			$this->nosql_ins = MarkPHP::loadclass('nosqlMark');
		}
		return $this->nosql_ins -> init($type);
	}

}
