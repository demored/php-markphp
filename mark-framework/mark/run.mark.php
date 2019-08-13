<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');
// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | 框架运行核心文件
// +----------------------------------------------------------------------
class runMark {

	private $controller_postfix     = 'Controller';
	private $action_postfix         = '';
	private $default_controller     = 'index';
	private $default_action         = 'run';
	private $default_module         = 'index';
	private $module_list            = array('index');
	private $default_before_action  = 'before';//默认的前置Action
	private $default_after_action   = 'after'; //默认的后置Action

	public function run() {
		//全局配置
		$markphp_conf = MarkPHP::getConfig();
		$this->init2Ehandle();
		$this->filter();
		//加载配置文件设置相关属性
		$this->set_params($markphp_conf['controller']);
		//验证方法是否合法，如果请求参数不正确，则直接返回404
		$controllerObj = $this->checkRequest();
		//前置Action
		$this->run_before_action($controllerObj);
		//正常流程Action
		$this->run_action($controllerObj);
		//后置Action
		$this->run_after_action($controllerObj);
	}

	/**
	 * 验证请求是否合法
	 * 如果请求参数m,c,a都为空，则走默认的
	 */
	private function checkRequest() {
		$markphp_conf = MarkPHP::getConfig();
		$controller  = isset($_GET['c']) ? $_GET['c'] : '';
		$action = isset($_GET['a']) ? $_GET['a'] : '';
		if ($markphp_conf['ismodule'] == true) {
			$module  = isset($_GET['m']) ? $_GET['m'] : $this -> default_module;
			if ($controller == "" && $action == "") {
				$controller = $_GET['c'] = $this->default_controller;
				$action = $_GET['a'] = $this->default_action;
			}
			//如果module不在白名单中，则直接返回404
			if (!in_array($module, $this->module_list) || empty($module)) {
				return MarkPHP::return404();
			}
			$module = $module . '/';

		} else {
			if ($controller == "" && $action == "") {
				$controller = $_GET['c'] = $this->default_controller;
				$action = $_GET['a'] = $this->default_action;
			}
			$module = '';
		}

		//controller处理，如果导入Controller文件失败，则返回404
		$path = rtrim($markphp_conf['controller']['path'], '/') . '/';
		$controllerClass = $controller . $this->controller_postfix;
		$controllerFilePath = $path . $module . $controllerClass . '.php';
		//controller名称可以不区分大小写
		if (!MarkPHP::import($controllerFilePath)) {
			$controllerClass = ucfirst($controller) . $this->controller_postfix; //改成大写
			$controllerFilePath = $path . $module . $controllerClass . '.php';
			if (!MarkPHP::import($controllerFilePath)) {
				return MarkPHP::return404();
			}
		}
		$controllerObj = MarkPHP::loadclass($controllerClass);
		//处理Action，如果方法不存在，则直接返回404
		list($whiteList, $methodList) = $this->parseWhiteList($controllerObj->markphp_list);
		//除去默认的action
		if ($action != $this->default_action) {
			if (!in_array($action, $whiteList)) {
				return MarkPHP::return404();
			} else {
				if ($methodList[$action]) {
					$method = strtolower($_SERVER['REQUEST_METHOD']);
					if (!in_array($method, $methodList[$action])) { //检查提交的HTTP METHOD
						return MarkPHP::return405(); 				//如果请求Method不正确，则返回405
					}
				}
			}
		}
		return $controllerObj;
	}

	//框架运行控制器中的Action函数
	private function run_action($controller) {
		$action = trim($_GET['a']);
		$action = $action . $this->action_postfix;

		if (!method_exists($controller, $action)) {
			MarkPHP::markError('Can not find default method : ' . $action);
		}
		$controller->$action();
	}

	/**
	 * 解析白名单
	 * 白名单参数支持指定GET POST PUT DEL 等HTTP METHOD操作
	 * 白名单参数：array('test', 'user|post-get-put')
	 * @param object $controller 控制器对象
	 * @return file
	 */
	private function parseWhiteList($MarkPHP_list) {

		$whiteList = $methodList = array();
		if(is_array($MarkPHP_list) && $MarkPHP_list){
			foreach ($MarkPHP_list as  $value) {
				if (strpos($value, "|") == false) {
					$whiteList[] = $value;
				} else {
					$temp = explode('|', $value);
					$whiteList[] = $temp[0];
					$methodTemp = explode('-', $temp[1]);
					foreach ($methodTemp as $v) {
						$methodList[$temp[0]][] = $v;
					}
				}
			}
		}
		return array($whiteList, $methodList);
	}

	private function run_before_action($controller) {
		$before_action = $this->default_before_action . $this->action_postfix;
		if (!method_exists($controller, $before_action))
			return false;
		$controller->$before_action();
	}

	private function run_after_action($controller) {
		$after_action = $this->default_after_action . $this->action_postfix;
		if (!method_exists($controller, $after_action)) return false;
		$controller->$after_action();
	}

	/**
	 *	设置框架运行参数
	 *  @param  string  $params
	 *  @return string
	 */
	private function set_params($params) {
		if (isset($params['controller_postfix']))
			$this->controller_postfix = $params['controller_postfix'];
		if (isset($params['action_postfix']))
			$this->action_postfix = $params['action_postfix'];
		if (isset($params['default_controller']))
			$this->default_controller = $params['default_controller'];
		if (isset($params['default_module']))
			$this->default_module = $params['default_module'];
		if (isset($params['module_list']))
			$this->module_list = $params['module_list'];
		if (isset($params['default_action']))
			$this->default_action = $params['default_action'];
		if (isset($params['default_before_action']))
			$this->default_before_action = $params['default_before_action'];
		if (isset($params['default_after_action']))
			$this->default_after_action = $params['default_after_action'];
	}

	/**
	 *	m-c-a数据处理
	 *  @return string
	 */
	private function filter() {
		if (isset($_GET['m'])) {
			if (!$this->_filter($_GET['m'])) unset($_GET['m']);
		}
		if (isset($_GET['c'])) {
			if (!$this->_filter($_GET['c'])) unset($_GET['c']);
		}
		if (isset($_GET['a'])) {
			if (!$this->_filter($_GET['a'])) unset($_GET['a']);
		}
	}

	//过滤url
	private function _filter($str) {
		return preg_match('/^[A-Za-z0-9_]+$/', trim($str));
	}

	/*
	 * 初始化异常和错误处理函数
	 * */
	public function init2Ehandle(){
		set_exception_handler(array($this,'handleException'));
		set_error_handler(array($this,'handleError'),error_reporting());
	}

	/*
	 *设置异常处理函数,写入日志文件
	 */
	public function handleException($exception){
		restore_exception_handler();
		MarkException::errorTpl($exception);
	}

	/*
	 *设置PHP错误处理回调函数,写入日志文件
	 */
	public function handleError($errorCode, $msg = '', $errorFile = 'unkwon', $errorLine = 0){
		$markphp_conf = MarkPHP::getConfig();
		restore_error_handler();
		if($errorCode & error_reporting()){
			MarkPHP::log("[error_code]:" . $errorCode . " [msg]:" . $msg, ERROR);
		}
		if($markphp_conf['is_debug'] == true) {
			var_dump($msg);
		} else {
			return MarkPHP::return500();
		}
	}
}