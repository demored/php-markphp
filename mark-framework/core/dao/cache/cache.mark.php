<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-cache 数据缓存工厂类
// +----------------------------------------------------------------------

class cacheMark{

	//缓存类型 FILE-文件缓存类型 MEM-内存缓存类型
	private static $instance = array();  //单例模式获取缓存类
	public $db_handle;
	private $page_cache_key, $page_cache_time, $page_cache_type;
	public $cache;

	public function set($key, $value, $time = 0) {
		return $this -> cache->set_cache($key, $value, $time);
	}

	public function get($key) {
		return $this -> cache->get_cache($key);
	}

	public function clear($key) {
		return $this -> cache->clear($key);
	}

	public function clear_all() {
		return $this -> cache->clear_all();
	}

	//获取类实例
	public function get_cache() {
		return $this->get_cache_handle($this -> type);
	}

	public function page_cache_start($key, $time = 0, $type = 'FILE') {
		$this->page_cache_key 	= 'MarkPHP_page_cache_' . $key;
		$this->page_cache_time 	= $time;
		$this->page_cache_type 	= $type;
		$page = $this->get($this->page_cache_key, $this->page_cache_type);
		if (!$page) {
			ob_start();
		} else {
			echo $page;
			exit;
		}
	}
	
	public function page_cache_end() {
		$this->set($this->page_cache_key, ob_get_contents(), $this->page_cache_time, $this->page_cache_type);
		$page = $this->get($this->page_cache_key, $this->page_cache_type);
		ob_end_clean(); //清空缓冲
		echo $page;
	}

	//简单工厂实现缓存类
	public function get_cache_handle($type) {
		$markphp_conf = MarkPHP::getConfig(); //需要设置文件缓存目录
		$type = strtoupper($type);
		switch ($type) {
			case 'FILE' :
				if (isset(cacheMark::$instance['filecache']))
					return cacheMark::$instance['filecache'];
				$filecache = $this->load_cache('filecache.mark.php', 'filecacheMark');
				$filepath = MarkPHP::getAppPath($markphp_conf['cache']['filepath']);
				$filecache->set_cache_path($filepath);
				cacheMark::$instance['filecache'] = $filecache;
				return $filecache;
				break;

			case 'MEM' :
				if (isset(cacheMark::$instance['memcache'])) return cacheMark::$instance['memcache'];
				$mem = $this->load_cache('memcached.mark.php', 'memcachedMark');
				$mem->add_server($markphp_conf['memcache']); //添加服务器
				cacheMark::$instance['memcache'] = $mem;
				return $mem;
				break;
		}
	}

	private function load_cache($file, $class) {
		if (cacheMark::$instance['require'][$file] !== TRUE) {
			require('driver/' . $file);
			cacheMark::$instance['require'][$file] = TRUE;
		}
		if (cacheMark::$instance['class'][$class] !== TRUE) {
			cacheMark::$instance['class'][$class] = TRUE;
			return new $class;
		} else {
			return cacheMark::$instance['class'][$class];
		}
	}
}
