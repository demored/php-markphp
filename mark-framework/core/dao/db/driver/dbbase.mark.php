<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-dbbase Driver DB基类
// +----------------------------------------------------------------------


abstract class dbbaseMark{

	/**
	 * 抽象数据库链接
	 * @param  string $host sql服务器
	 * @param  string $user 数据库用户名
	 * @param  string $password 数据库登录密码
	 * @param  string $database 数据库
	 * @param  string $charset 编码
	 * @param  string $pconnect 是否持久链接
	 */
	abstract protected function connect($host, $user, $password, $database, $charset = 'utf8', $pconnect = 0);
	
	/**
	 * 抽象数据库执行语句
	 * @param  string $sql SQL语句
	 * @return obj
	 */
	abstract protected function query($sql);
	
	/**
	 * 抽象数据库-结果集中的行数
	 * @param $result 结果集
	 * @return array
	 */
	abstract protected function result($result, $num=1);
	
	/**
	 * 抽象数据库-从结果集中取得一行作为关联数组
	 * @param $result 结果集
	 * @return array
	 */
	abstract protected function fetch_assoc($result);
	
	/**
	 * 抽象数据库-从结果集中取得列信息并作为对象返回
	 * @param  $result 结果集
	 * @return array
	 */
	abstract protected function fetch_fields($result);
	
	/**
	 * 抽象数据库-前一次操作影响的记录数
	 * @return int
	 */
	abstract protected function affected_rows();
	
	/**
	 * 抽象数据库-结果集中的行数
	 * @param $result 结果集
	 * @return int
	 */
	abstract protected function num_rows($result);
	
	/**
	 * 抽象数据库-结果集中的字段数量
	 * @param $result 结果集
	 * @return int
	 */
	abstract protected function num_fields($result);
	
	/**
	 * 抽象数据库-获取上一INSERT的ID值
	 * @return Int
	 */
	abstract protected function insert_id();
	
	/**
	 * 抽象数据库-释放结果内存
	 * @param obj $result 需要释放的对象
	 */
	abstract protected function free_result($result);

	/**
	 * 抽象数据库链接关闭
	 * @param  string $sql SQL语句
	 * @return obj
	 */
	abstract protected function close();

	/**
	 * 错误信息
	 * @return string
	 */
	abstract protected function error();
}
