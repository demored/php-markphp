<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-Nosql-Redis
// +----------------------------------------------------------------------


class redisMark {

	private $redis;
	public function init($config = array()) {
		if ($config['server'] == '')  $config['server'] = '127.0.0.1';
		if ($config['port'] == '')  $config['port'] = '6379';
		$this->redis = new Redis();
		$this->redis->connect($config['server'], $config['port']);
		return $this->redis;
	}
	
	public function set($key, $value, $timeOut = 0) {
		$value = json_encode($value, TRUE);
		$retRes = $this->redis->set($key, $value);
		if ($timeOut > 0)
			$this->redis->setTimeout($key, $timeOut);

		return $retRes;
	}

	public function get($key) {
		$result = $this->redis->get($key);
		return json_decode($result, TRUE);
	}

	public function delete($key) {
		return $this->redis->delete($key);
	}

	public function flushAll() {
		return $this->redis->flushAll();
	}

	public function push($key, $value ,$right = true) {
		$value = json_encode($value);
		return $right ? $this->redis->rPush($key, $value) : $this->redis->lPush($key, $value);
	}

	public function pop($key , $left = true) {
		$val = $left ? $this->redis->lPop($key) : $this->redis->rPop($key);
		return json_decode($val);
	}

	public function increment($key) {
		return $this->redis->incr($key);
	}

	public function decrement($key) {
		return $this->redis->decr($key);
	}

	public function exists($key) {
		return $this->redis->exists($key);
	}

	public function redis() {
		return $this->redis;
	}
}