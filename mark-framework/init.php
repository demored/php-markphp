<?php
// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------

/************系统初始化配置*************/
define('MARKPHP_PATH', dirname(__FILE__));
define('CORE_PATH' , MARKPHP_PATH.'/core');
define('CORE_VIEW_PATH' , CORE_PATH.'/view');
define('IS_MARKPHP', 1);
define('DS' , DIRECTORY_SEPARATOR);
error_reporting(E_ERROR | E_PARSE);
include(APP_PATH.'common.php');
//自动加载APP配置和Common
autoload_function(APP_PATH.'conf'.DS);
function autoload_function($path){
    global $markphp_conf;
    $dir  = [];
    $file = [];
    recursion_dir($path,$dir,$file);
    foreach ($file as $key => $value) {
        if (file_exists($value)) {
            require_once($value);
        }
    }
    unset($markphp_conf);
}

/*
* 获取文件&文件夹列表(支持文件夹层级)
* path : 文件夹 $dir ——返回的文件夹array files ——返回的文件array
* $deepest 是否完整递归；$deep 递归层级
*/

function recursion_dir($path,&$dir,&$file,$deepest=-1,$deep=0){
    $path = rtrim($path,DS).DS;
    if (!is_array($file)) $file= [];
    if (!is_array($dir)) $dir= [];
    if (!$dh = opendir($path)) return false;
    while(($val=readdir($dh)) !== false){
        if ($val == '.' || $val == '..') continue;
        $value = strval($path.$val);
        if (is_file($value)){
            $file[] = $value;
        }else if(is_dir($value)){
            $dir[]=$value;
            if ($deepest==-1 || $deep<$deepest){
                recursion_dir($value.DS,$dir,$file,$deepest,$deep+1);
            }
        }
    }
    closedir($dh);
    return true;
}