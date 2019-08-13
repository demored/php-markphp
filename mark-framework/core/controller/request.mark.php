<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');
// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// |Controller-validate 数据基础验证类
// +----------------------------------------------------------------------

class requestMark {

	public function get_post($name = '') {
		if (empty($name)) return $_POST;
		return (isset($_POST[$name])) ? $_POST[$name] : '';
	}

	public function get_get($name = '') {
		if (empty($name)) return $_GET;
		return (isset($_GET[$name])) ? $_GET[$name] : '';
	}

	public function get_cookie($name = '') {
		if ($name == '') return $_COOKIE;
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : '';
	}

	public function get_session($name = '') {
		if ($name == '') return $_SESSION;
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : '';
	}

	public function get_env($name = '') {
		if ($name == '') return $_ENV;
		return (isset($_ENV[$name])) ? $_ENV[$name] : '';
	}

	public function get_service($name = '') {
		if ($name == '') return $_SERVER;
		return (isset($_SERVER[$name])) ? $_SERVER[$name] : '';
	}

	public function get_php_self() {
		return $this->get_service('PHP_SELF');
	}

	public function get_service_name() {
		return $this->get_service('SERVER_NAME');
	}

	public function get_request_time() {
		return $this->get_service('REQUEST_TIME');
	}
	

	public function get_useragent() {
		return $this->get_service('HTTP_USER_AGENT');	
	}	

	public function get_uri() {
		return $this->get_service('REQUEST_URI');
	}

	public function is_post() {
		return (strtolower($this->get_service('REQUEST_METHOD')) == 'post') ? true : false;
	}

	public function is_get() {
		return (strtolower($this->get_service('REQUEST_METHOD')) == 'get') ? true : false;
	}

	public function is_ajax() {
		if ($this->get_service('HTTP_X_REQUESTED_WITH') && strtolower($this->get_service('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') return true;
		if ($this->get_post('initphp_ajax') || $this->get_get('initphp_ajax')) return true; //程序中自定义AJAX标识
		return false;
	}

    public function get_ip() {
        static $realip = null;
        if (null !== $realip) {
            return $realip;
        }
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        // 处理多层代理的情况
        if (false !== strpos($realip, ',')) {
            $realip = reset(explode(',', $realip));
        }
        // IP地址合法验证
        $realip = filter_var($realip, FILTER_VALIDATE_IP, null);
        if (false === $realip) {
            return '0.0.0.0';   // unknown
        }
        return $realip;
    }
}
