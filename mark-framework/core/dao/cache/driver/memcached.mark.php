<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');
//mc 缓存类
class memcachedMark {

	private $memcache;

	public function set_cache($key, $value, $time = 0) { 
		return $this->memcache->set($key, $value, false, $time);
	}

	public function get_cache($key) {
		return $this->memcache->get($key);
	}

	public function clear($key) {
		return $this->memcache->delete($key);
	}

	public function clear_all() {
		return $this->memcache->flush();
	}

	public function  increment($key, $step = 1) {
		return $this->memcache->increment($key, (int) $step);
	}

	public function decrement($key, $step = 1) {
		return $this->memcache->decrement($key, (int) $step);
	}

	public function close() {
		return $this->memcache->close();
	}

	public function replace($key, $value, $time = 0, $flag = false) {
		return $this->memcache->replace($key, $value, false, $time);
	}

	public function getVersion() {
		return $this->memcache->getVersion();
	}

	public function getStats() {
		return $this->memcache->getStats();
	}

	public function add_server($servers) {
		$this->memcache = new Memcache;
		if (!is_array($servers) || empty($servers)) exit('memcache server is null!');
		foreach ($servers as $val) {
			$this->memcache->addServer($val[0], $val[1]);
		}
	}

}
