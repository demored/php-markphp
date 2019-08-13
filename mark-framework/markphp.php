<?php
// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
require_once('convention.php');                     //导入框架惯例配置
require_once('init.php');                           //加载初始化文件
require_once('mark/core.mark.php');                 //导入核心类文件
require_once('mark/exception.mark.php');            //异常核心类
class MarkPHP extends coreMark {

    public static $time;
    private static function isDebug() {
        $markphp_conf = MarkPHP::getConfig();
        if (isset($markphp_conf['is_debug']) && $markphp_conf['is_debug'] == true && isset($markphp_conf['show_all_error']) && $markphp_conf['show_all_error'] == true) {
            error_reporting(E_ALL^E_NOTICE);
        }
    }

    public static function run() {
        self::isDebug();
        try {
            require(MARKPHP_PATH . '/mark/dispatcher.mark.php'); //路由
            require(MARKPHP_PATH . '/mark/run.mark.php');
            $dispacher = MarkPHP::loadclass('dispatcherMark');  //路由
            $dispacher->dispatcher();
            $run = MarkPHP::loadclass('runMark');
            $run->run();
        } catch (MarkException $e) {
            MarkException::errorTpl($e);
        } catch (Exception $e) {        //默认异常
            MarkException::errorTpl($e);
        }
    }

    /**
     * 加载文件
     * 优化外部批量加载文件
     * [path1 => file1 , path2 => file2]
     */
    public static function import($filename_, array $pathArr = array()) {
        $filename = MarkPHP::getAppPath($filename_);
        $temp_name = md5($filename);
        if (isset(parent::$instance['importfile'][$temp_name])) return true; //已经加载该文件，则不重复加载
        if (@is_readable($filename) == true && empty($pathArr)) {
            require($filename);
            parent::$instance['importfile'][$temp_name] = true;             //设置已加载
            return true;
        } else {
            /* 自动搜索文件夹 */
            foreach ($pathArr as $dir => $file) {
                if(!is_dir($dir)){
                    MarkPHP::markError($dir.' is not a dir');
                }
                $new_filename = rtrim($dir, '/') . '/' . $file;
                if(!file_exists($new_filename)){
                    MarkPHP::markError($new_filename.' not exists');
                }
                if (isset(parent::$instance['importfile'][$new_filename]))
                    return true;
                if (is_file($new_filename)) {
                    require_once($new_filename);                    // 载入文件
                    parent::$instance['importfile'][$new_filename] = true;
                    return true;
                }
            }
        }
        return false;
    }

    //返回类实例
    public static function loadclass($classname, $force = false) {
        if (preg_match('/[^a-z0-9\-_.]/i', $classname))
            MarkPHP::markError('invalid classname');
        if ($force == true)
            unset(parent::$instance['loadclass'][$classname]);
        if (!isset(parent::$instance['loadclass'][$classname])) {
            if (!class_exists($classname)){
                MarkPHP::markError($classname . ' is not exist!');
                exit;
            }
            parent::$instance['loadclass'][$classname] = new $classname;
        }
        return parent::$instance['loadclass'][$classname];
    }

    /**
     * XSS过滤，输出内容过滤
     * 1. 框架支持全局XSS过滤机制-全局开启将消耗PHP运行
     * 2. 手动添加XSS过滤函数，在模板页面中直接调用
     * 全局使用方法：MarkPHP::output($string, $type = 'encode');
     * @param string $string  需要过滤的字符串
     * @param string $type    encode HTML处理 | decode 反处理
     * @return string
     */

    public static function output($string, $type = 'encode') {
        $html = array("&", '"', "'", "<", ">", "%3C", "%3E");
        $html_code = array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;", "&lt;", "&gt;");
        if ($type == 'encode') {
            if (function_exists('htmlspecialchars'))
                return htmlspecialchars($string);

            $str = str_replace($html, $html_code, $string);
        } else {
            if (function_exists('htmlspecialchars_decode'))
                return htmlspecialchars_decode($string);

            $str = str_replace($html_code, $html, $string);
        }
        return $str;
    }

    //获取Dao
    public static function getDao($daoname, $path = '') {
        $markphp_conf = MarkPHP::getConfig();
        $path  = ($path == '') ? '' : $path . '/';
        $class = $daoname . $markphp_conf['dao']['dao_postfix'];
        $file  = rtrim($markphp_conf['dao']['path'], '/') . '/' . $path . $class . '.php';
        if (!MarkPHP::import($file))
            return false;

        $obj = MarkPHP::loadclass($class);
        return $obj;
    }

    //静态方式调用扩展库中的类
    public static function getService($servicename, $path = '') {
        $markphp_conf = MarkPHP::getConfig();
        $path  = ($path == '') ? '' : $path . '/';
        $class = $servicename . $markphp_conf['service']['service_postfix'];
        $file  = rtrim($markphp_conf['service']['path'], '/') . '/' . $path . $class . '.php';
        if (!MarkPHP::import($file)) return false;
        return MarkPHP::loadclass($class);
    }

    //静态方式调用扩展库中的类
    public static function getLibrarys($className) {
        $classPath = MARKPHP_PATH . "/core/library/" . $className . '.php';
        $classFullName = $className;
        if (!file_exists($classPath)) MarkPHP::markError('file '. $className . '.php is not exist!');
        if (!isset(parent::$instance['MarkPHP']['l'][$classFullName])) {
            require_once($classPath);
            if (!class_exists($classFullName)) MarkPHP::markError('class' . $classFullName . ' is not exist!');
            parent::$instance['MarkPHP']['l'][$classFullName] =  new $classFullName;
        }
        return parent::$instance['MarkPHP']['l'][$classFullName];
    }

    //静态方式调用工具库中的类
    public static function getUtils($className) {
        $classPath = MARKPHP_PATH . "/core/util/" . $className . '.mark.php';
        $classFullName = $className . "Mark";
        if (!file_exists($classPath)) MarkPHP::markError('file '. $className . '.php is not exist!');
        if (!isset(parent::$instance['MarkPHP']['u'][$classFullName])) {
            require_once($classPath);
            if (!class_exists($classFullName)) MarkPHP::markError('class' . $classFullName . ' is not exist!');
            parent::$instance['MarkPHP']['u'][$classFullName] = new $classFullName;;
        }
        return parent::$instance['MarkPHP']['u'][$classFullName];
    }

    public static function log($message, $log_type = 'DEBUG') {
        $log = MarkPHP::getUtils("log");
        $log->write($message, $log_type);
    }

    //组装URL
    public static function url($action, $params = array(), $baseUrl = '') {
        $markphp_conf = MarkPHP::getConfig();
        $action = explode("|", $action);
        $baseUrl = ($baseUrl == '') ? rtrim($markphp_conf['url'], "/") . "/" : $baseUrl;
        $ismodule = $markphp_conf['ismodule'];
        switch ($markphp_conf['isuri']) {
            case 'rewrite' :
                $actionStr = implode('/', $action);
                $paramsStr = '';
                if ($params) {
                    $paramsStr = '?' . http_build_query($params);
                }
                return $baseUrl . $actionStr . $paramsStr;
                break;

            case 'path' :
                $actionStr = implode('/', $action);
                $paramsStr = '';
                if ($params) {
                    foreach ($params as $k => $v) {
                        $paramsStr .= $k . '/' . $v . '/';
                    }
                    $paramsStr = '/' . $paramsStr;
                }
                return $baseUrl . $actionStr . $paramsStr;
                break;

            case 'html' :
                $actionStr = implode('-', $action);
                $actionStr = $actionStr . '.htm';
                $paramsStr = '';
                if ($params) {
                    $paramsStr = '?' . http_build_query($params);
                }
                return $baseUrl . $actionStr . $paramsStr;
                break;

            default:
                $actionStr = '';
                if ($ismodule === true) {
                    if(count($action) == 2){
                        $actionStr .= 'c=' . $action[0];
                        $actionStr .= '&a=' . $action[1] . '&';
                    }else{
                        $actionStr .= 'm=' . $action[0];
                        $actionStr .= '&c=' . $action[1];
                        $actionStr .= '&a=' . $action[2] . '&';
                    }
                   
                } else {
                    $actionStr .= 'c=' . $action[0];
                    $actionStr .= '&a=' . $action[1] . '&';
                }
                $actionStr = '?' . $actionStr;
                $paramsStr = '';
                if ($params) {
                    $paramsStr = http_build_query($params);
                }
                return $baseUrl . $actionStr . $paramsStr;
                break;
        }
    }
    
    //获取时间戳
    public static function getTime() {
        if (self::$time > 0)
            return self::$time;
        self::$time = time();
        return self::$time;
    }

    /**
     * 获取全局配置文件
     * 全局使用方法：MarkPHP::getConfig('controller.path')
     * @param string $path 获取的配置路径 多级用点号分隔
     * @return mixed
     */
    public static function getConfig($path='') {
        global $markphp_conf;
        if (empty($path)) return $markphp_conf;
        $tmp = $markphp_conf;
        $paths = explode('.', $path);
        foreach ($paths as $item) {
            $tmp = $tmp[$item];
        }
        return $tmp;
    }

    //设置全局配置文件
    public static function setConfig($key, $value) {
        global $markphp_conf;
        $markphp_conf[$key] = $value;
        return $markphp_conf;
    }

    //加上APP_PATH 部署站点自由
    public static function getAppPath($path = '') {
        $tag = "MarkPHP_OUT_PATH:";
        $ret = strstr($path, $tag);
        if ($ret != false) {
            return ltrim($path, $tag);
        }
        if (!defined('APP_PATH')) return $path;
        return rtrim(APP_PATH, '/') . '/' . $path;
    }

    // 异常错误输出函数
    public static function markError($msg, $code = 10000) {
        throw new MarkException($msg, $code);
    }

    /**
     *
     * @param $controllerName 控制器名称
     * @param $functionName   方法名称
     * @param $params         方法参数
     * @param $controllerPath 控制器文件夹名称,例如在控制器文件夹目录中，还有一层目录，user/则，该参数需要填写
     * @return
     */

    public static function getController($controllerName, $functionName, $params = array(), $controllerPath = '') {
        $markphp_conf = MarkPHP::getConfig();
        $controllerPath = ($controllerPath == '') ? '' : rtrim($controllerPath, '/') . '/';
        $path = rtrim($markphp_conf['controller']['path'], '/') . '/' . $controllerPath . $controllerName . '.php';

        if (!MarkPHP::import($path)) {
            $controllerName = ucfirst($controllerName);
            $path = rtrim($markphp_conf['controller']['path'], '/') . '/' . $controllerPath . $controllerName . '.php';
            MarkPHP::import($path);
        }
        $controller = MarkPHP::loadclass($controllerName);
        if (!$controller)
            return MarkPHP::markError('can not loadclass : ' . $controllerName);
        if (!method_exists($controller, $functionName))
            return MarkPHP::markError('function is not exists : ' . $controllerName);
        if (!$params) {
            $controller->$functionName();
        } else {
            call_user_func_array(array($controller, $functionName), $params);
        }
    }

    //返回404错误页面
    public static function return404() {
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        self::_error_page("404 Not Found");
        exit;
    }

    //返回405错误页面
    public static function return405() {
        header('HTTP/1.1 405 Method not allowed');
        header("status: 405 Method not allowed");
        self::_error_page("405 Method not allowed");
        exit;
    }

    //返回500错误页面
    public static function return500() {
        header('HTTP/1.1 500 Internal Server Error');
        header("status: 500 Internal Server Error");
        self::_error_page("500 Internal Server Error");
        exit;
    }
    //错误提示页面
    private static function _error_page($msg) {
		die("<h1>".$msg."</h1>");
        exit;
    }
    public function __destruct(){
        unset($markphp_conf);
    }

}

// 控制器Controller基类
class Controller extends coreMark {

    protected $controller;
    protected $view;

    public function __construct() {
        parent::__construct();
        $this->controller = $this->load('controller', 'c');
        $this->view = $this->load('view', 'v');
        $this ->view ->init_view();
        $this->view->assign('mark_token', $this->controller->get_token());
        //注册全局变量，这样在Service和Dao中通过$this->common也能调用Controller中的类
        $this->register_global('common', $this->controller);
    }
}

//服务Service基类
class Service extends coreMark {

    protected $service;
    public function __construct() {

        parent::__construct();
        $this->service = $this->load('service', 's');
    }
}

/**
 * 数据层Dao基类
 * $this->dao->db DB方法库
 * $this->dao->cache Cache方法库
 */

class Dao extends coreMark {
    protected $dao;
    protected $cache;
    public function __construct() {
        $this->dao = $this->load('dao', 'd');
        $this->dao->run_db();//初始化db
    }

    /**
     * 分库初始化DB
     * 如果有多数据库链接的情况下，会调用该函数来自动切换DB link
     * @param string $db
     * @return db
     */
    public function init_db($db = 'default') {
        $this->dao->db->init_db($db);
        return $this->dao->db;
    }


}

