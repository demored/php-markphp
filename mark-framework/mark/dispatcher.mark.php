<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// |- 路由分发核心类，将m-c-a URL重写
// +----------------------------------------------------------------------

class dispatcherMark {

	public function dispatcher() {
		$markphp_conf = MarkPHP::getConfig();

		switch ($markphp_conf['isuri']) {
			case 'path' :
				$request = $this->getRequest();
				$this->parsePathUri($request);
				break;
			case 'rewrite' :
				$request = $this->getRequest();
				$this->parseRewriteUri($request);
				break;
			case 'html' :
				$request = $this->getRequest();
				$this->parseHtmlUri($request);
				break;
			default :
				return false;
				break;
		}
		return true;
	}

	private function getRequest() {
		$markphp_conf = MarkPHP::getConfig();
		$filter_param = array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e');
		$uri = str_replace($filter_param, '', $_SERVER['REQUEST_URI']);
	    $posi = strpos($uri, '?');
    	if ($posi) $uri = substr($uri,0,$posi);
    	$urlArr = parse_url($markphp_conf['url']);
		$request = str_replace(trim($urlArr['path'], '/'),'', $uri);
		if (strpos($request, '.php')) {
			$request = explode('.php', $request);
			$request = $request[1];
		}
		return $request;
	}

	private function parsePathUri($request) {
		$markphp_conf = MarkPHP::getConfig();
		if (!$request) return false;
		$request =  trim($request, '/');
		if ($request == '') return false;
		$request =  explode('/', $request);
		if (!is_array($request) || count($request) == 0) return false;
		if ($markphp_conf['ismodule'] == true) { //是否开启模型模式
			if (isset($request[0])) $_GET['m'] = $request[0];
			if (isset($request[1])) $_GET['c'] = $request[1];
			if (isset($request[2])) $_GET['a'] = $request[2];
			unset($request[0], $request[1], $request[2]);
		} else {
			if (isset($request[0])) $_GET['c'] = $request[0];
			if (isset($request[1])) $_GET['a'] = $request[1];
			unset($request[0], $request[1]);
		}
		if (count($request) > 1) {
			$mark = 0;
			$val = $key = array();
			foreach($request as $value){
				$mark++;
				if ($mark % 2 == 0) {
					$val[] = $value;
				} else {
					$key[] = $value;
				}
			}
			if(count($key) !== count($val)) $val[] = NULL;
			$get = array_combine($key,$val);
			foreach($get as $key=>$value) $_GET[$key] = $value;
		}
		return $request;
	}

	private function parseRewriteUri($request) {
		$markphp_conf = MarkPHP::getConfig();
		if (!$request) return false;
		$request =  trim($request, '/');
		if ($request == '') return false;
		$request =  explode('/', $request);
		if (!is_array($request) || count($request) == 0) return false;
		if ($markphp_conf['ismodule'] == true) { //是否开启模型模式
			if (isset($request[0])) $_GET['m'] = $request[0];
			if (isset($request[1])) $_GET['c'] = $request[1];
			if (isset($request[2])) $_GET['a'] = $request[2];
		} else {
			if (isset($request[0])) $_GET['c'] = $request[0];
			if (isset($request[1])) $_GET['a'] = $request[1];
		}
		return $request;
	}

	private function parseHtmlUri($request) {
		$markphp_conf = MarkPHP::getConfig();
		if (!$request) return false;
		$request = trim($request, '/');
		$request = str_replace('.htm', '', $request);
		if ($request == '') return false;
		$request = explode('-', $request);
		if (!is_array($request) || count($request) == 0) return false;
		if ($markphp_conf['ismodule'] == true) { //是否开启模型模式
			if (isset($request[0])) $_GET['m'] = $request[0];
			if (isset($request[1])) $_GET['c'] = $request[1];
			if (isset($request[2])) $_GET['a'] = $request[2];
		} else {
			if (isset($request[0])) $_GET['c'] = $request[0];
			if (isset($request[1])) $_GET['a'] = $request[1];
		}
		return $request;		
	}
}