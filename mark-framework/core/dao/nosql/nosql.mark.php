<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-Nosql
// +----------------------------------------------------------------------

class nosqlMark {
	
	private static $instance = array();  //单例模式获取nosql类
	public function init($type = 'REDIS', $server = 'default') {
		$MarkPHP_conf = MarkPHP::getConfig(); //需要设置文件缓存目录
		$type = strtoupper($type); 
		switch ($type) {
			case 'REDIS' :
				$instance_name = 'redis_' . $server;
				if (isset(nosqlMark::$instance[$instance_name]))
					return nosqlMark::$instance[$instance_name];

				$redis = $this->load_nosql('redis.mark.php', 'redisMark', $server);
				$redis->init($MarkPHP_conf['redis'][$server]);
				nosqlMark::$instance[$instance_name] = $redis;
				return $redis;
				break;
		}
	}

	//加载nosql文件和单例nosql对象
	private function load_nosql($file, $class, $server) {
		if (nosqlMark::$instance['require'][$file] != TRUE) {
			require('driver/' . $file);
			nosqlMark::$instance['require'][$file] = TRUE;
		}
		$tag = $class . "_" . $server;
		if (!nosqlMark::$instance['class'][$tag]) {
			nosqlMark::$instance['class'][$tag] = new $class;
			return nosqlMark::$instance['class'][$tag];
		} else {
			return nosqlMark::$instance['class'][$tag];
		}
	}
}