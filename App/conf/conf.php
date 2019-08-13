<?php
header("Content-Type:text/html; charset=utf-8");
ini_set('date.timezone','Asia/Shanghai');
$markphp_conf = [];

/*********************************基础配置*****************************************/

$markphp_conf['url'] = 'http://www.markphp1.com/';
$markphp_conf['is_debug'] = true;
$markphp_conf['show_all_error'] = true;
$markphp_conf['log_dir'] = APP_PATH.'data/logs/';
$markphp_conf['isuri'] = 'default';
$markphp_conf['frame_name'] = 'MarkPHP';
$markphp_conf['version'] = '1.1.0';

/*********************************DAO数据层配置*****************************************/

$markphp_conf['dao']['dao_postfix']  = 'Dao'; //后缀
$markphp_conf['dao']['path']  = 'library/dao/'; //后缀

/*********************************Service配置*****************************************/

$markphp_conf['service']['service_postfix']  = 'Service'; 	//后缀
$markphp_conf['service']['path'] = 'library/service/'; 		//service路径

/*********************************Controller配置*****************************************/

$markphp_conf['ismodule'] = true; //开启module方式
$markphp_conf['controller']['path']                  = 'web/controller/';
$markphp_conf['controller']['controller_postfix']    = 'Controller'; //控制器文件后缀名
$markphp_conf['controller']['action_postfix']        = '';          //Action函数名称后缀
$markphp_conf['controller']['default_controller']    = 'index';     //默认执行的控制器名称
$markphp_conf['controller']['default_action']        = 'run';
$markphp_conf['controller']['module_list']           = array('index', 'demo','user');
$markphp_conf['controller']['default_module']        = 'index';
$markphp_conf['controller']['default_before_action'] = 'before';
$markphp_conf['controller']['default_after_action']  = 'after';

/*********************************View配置*****************************************/

//模板引擎 smarty

$markphp_conf['template']['template_path']      = '../App/web/template';		//模板路径
$markphp_conf['template']['template_c_path']    = '../App/data/temp/template_c';
$markphp_conf['template']['template_config']    = '../App/data/temp/template_config';
$markphp_conf['template']['template_cache']     = '../App/data/temp/template_cache';

/*********************************缓存，Nosql配置*****************************************/
//mc配置
$markphp_conf['memcache'][0]   = array('127.0.0.1', '11211');

//文件缓存配置
$markphp_conf['cache']['filepath'] = 'data/temp/filecache';

//mongo配置
$markphp_conf['mongo']['default']['server']     = '127.0.0.1';
$markphp_conf['mongo']['default']['port']       = '27017';
$markphp_conf['mongo']['default']['option']     = array('connect' => true);
$markphp_conf['mongo']['default']['db_name']    = 'test';
$markphp_conf['mongo']['default']['username']   = '';
$markphp_conf['mongo']['default']['password']   = '';
//redis配置
$markphp_conf['redis']['default']['server']     = '127.0.0.1';
$markphp_conf['redis']['default']['port']       = '6379';