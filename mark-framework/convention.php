<?php

/* 框架全局配置惯例配置 */
$markphp_conf = [];
$markphp_conf['url'] = 'http://www.markphp.com/';
$markphp_conf['is_debug'] = true;
$markphp_conf['show_all_error'] = false;
$markphp_conf['frame_name'] = 'MarkPHP';
$markphp_conf['version'] = '1.1.0';
$markphp_conf['log_dir'] = APP_PATH.'data/logs/';
$markphp_conf['isuri'] = 'default';

//输出自动过滤
$markphp_conf['isviewfilter'] = false;
$markphp_conf['dao']['dao_postfix']  = 'Dao'; //后缀
$markphp_conf['dao']['path']  = 'library/dao/'; //后缀

$markphp_conf['db']['default']['db_type']                   = 0; //0-单个服务器，1-读写分离，2-主-主随机模型
$markphp_conf['db']['default'][0]['host']                   = '192.168.80.131'; //master
$markphp_conf['db']['default'][0]['username']               = 'root';
$markphp_conf['db']['default'][0]['password']               = '';
$markphp_conf['db']['default'][0]['database']               = 'test';
$markphp_conf['db']['default'][0]['charset']                = 'utf8';
$markphp_conf['db']['default'][0]['pconnect']               = 0;

$markphp_conf['db']['default'][1]['host']                   = '192.168.80.134'; //slave
$markphp_conf['db']['default'][1]['username']               = 'root';
$markphp_conf['db']['default'][1]['password']               = '';
$markphp_conf['db']['default'][1]['database']               = 'test';
$markphp_conf['db']['default'][1]['charset']                = 'utf8';
$markphp_conf['db']['default'][1]['pconnect']               = 0;

$markphp_conf['service']['service_postfix']  = 'Service';
$markphp_conf['service']['path'] = 'library/service/';

$markphp_conf['ismodule'] = false;
$markphp_conf['controller']['path']                  = 'web/controller/';
$markphp_conf['controller']['controller_postfix']    = 'Controller';
$markphp_conf['controller']['action_postfix']        = '';
$markphp_conf['controller']['default_controller']    = 'index';
$markphp_conf['controller']['default_action']        = 'run';
$markphp_conf['controller']['module_list']           = array('test', 'index');
$markphp_conf['controller']['default_module']        = 'index';
$markphp_conf['controller']['default_before_action'] = 'before';
$markphp_conf['controller']['default_after_action']  = 'after';

$markphp_conf['template']['template_path']      = '../App/web/template';
$markphp_conf['template']['template_c_path']    = '../App/data/temp/template_c';
$markphp_conf['template']['template_config']    = '../App/data/temp/template_config';
$markphp_conf['template']['template_cache']     = '../App/data/temp/template_cache';

$markphp_conf['memcache'][0]   = array('127.0.0.1', '11211');
$markphp_conf['cache']['filepath'] = 'data/filecache';

$markphp_conf['mongo']['default']['server']     = '127.0.0.1';
$markphp_conf['mongo']['default']['port']       = '27017';
$markphp_conf['mongo']['default']['option']     = array('connect' => true);
$markphp_conf['mongo']['default']['db_name']    = 'test';
$markphp_conf['mongo']['default']['username']   = '';
$markphp_conf['mongo']['default']['password']   = '';
$markphp_conf['redis']['default']['server']     = '127.0.0.1';
$markphp_conf['redis']['default']['port']       = '6379';