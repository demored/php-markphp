<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// |工具库-日志类
// +----------------------------------------------------------------------

class logMark {

	private $default_file_size = '1024000'; //默认日志文件大小

	public function write($message, $log_type = 'DEBUG') {
		$log_path = $this->get_file_log_name();
		if(is_file($log_path) && ($this->default_file_size < filesize($log_path)) ) {
			rename($log_path, dirname($log_path).'/'.time().'-Bak-'.basename($log_path));
		}
		$message = $this->get_message($message, $log_type);
		error_log($message, 3, $log_path, '');
	}

	private function get_file_log_name() {
		$config = MarkPHP::getConfig();
		return $config['log_dir'] .  $this->_errorLogFileName();
	}

	private function get_message($message, $log_type) {
		return  date("Y-m-d H:i:s") . " [{$log_type}] : {$message}\r\n";
	}

	private function _errorLogFileName(){
		return "MarkPHP_log_" . date('Y-m-d').'.log';
	}
}
