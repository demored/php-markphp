<?php

//数据库配置
$markphp_conf['db']['driver']   = 'mysqli'; //选择不同的数据库DB 引擎，一般默认mysqli,或者mysql
//default数据库配置 一般使用中 $this->init_db('default')-> 或者 $this->init_db()-> 为默认的模型
$markphp_conf['db']['default']['db_type']                   = 0; //0-单个服务器，1-读写分离，2-主-主随机模型
$markphp_conf['db']['default'][0]['host']                   = 'localhost'; //master
$markphp_conf['db']['default'][0]['username']               = 'root';
$markphp_conf['db']['default'][0]['password']               = 'root';
$markphp_conf['db']['default'][0]['database']               = 'test';
$markphp_conf['db']['default'][0]['charset']                = 'utf8';
$markphp_conf['db']['default'][0]['pconnect']               = 0;
$markphp_conf['db']['default'][1]['host']                   = 'localhost'; //slave
$markphp_conf['db']['default'][1]['username']               = 'root';
$markphp_conf['db']['default'][1]['password']               = 'root';
$markphp_conf['db']['default'][1]['database']               = 'test';
$markphp_conf['db']['default'][1]['charset']                = 'utf8';
$markphp_conf['db']['default'][1]['pconnect']               = 0;
