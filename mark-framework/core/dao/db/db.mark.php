<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-db 常用SQL方法封装
// +----------------------------------------------------------------------
//引入sql制造类
require_once("sqlbuild.mark.php");

class dbMark extends sqlbuildMark {

	//执行sql语句
	public function query($sql, $is_set_default = true) {
		$this->get_link_id($sql);
		$query = $this->db->query($sql);
	    if ($this->db->error()) {
            MarkPHP::markError($this->db->error());
        }
		if ($is_set_default)
			$this->set_default_link_id(); //设置默认的link_id

		return $query;
	}

	// 结果集中的行数
	public function result($result, $num=1) {
		return $this->db->result($result, $num);
	}
	
	//从结果集中取得一行作为关联数组
	public function fetch_assoc($result) {
		return $this->db->fetch_assoc($result);
	}
	
	//从结果集中取得列信息并作为对象返回
	public function fetch_fields($result) {
		return $this->db->fetch_fields($result);
	}
	
	//结果集中的行数
	public function num_rows($result) {
		return $this->db->num_rows($result);
	}

	//结果集中的字段数量
	public function num_fields($result) {
		return $this->db->num_fields($result);
	}

	//释放结果内存
	public function free_result($result) {
		return $this->db->free_result($result);
	}

	public function insert_id($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->insert_id();
	}
	
	//影响结果集行数
	public function affected_rows($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->affected_rows();
	}

	//关闭连接
	public function close($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->close();
	}
	
	//错误信息
	public function error($db = "") {
		if ($db != "") {
			$this->get_link_id();
		}
		return $this->db->error();
	}
	
	//开启事务
	public function transaction_start() {
		$this->query("START TRANSACTION");
		return true;
	}

	//提交事务
	public function transaction_commit() {
		$this->query("COMMIT");
		return true;
	}
	
	//回滚事务
	public function transaction_rollback() {
		$this->query("ROLLBACK"); 
		return true;
	}

	/** 
	 * 插入一条数据
	 * @param array  $data array('key值'=>'值')
	 * @param string $table_name 表名
	 * @return id
	 */
	public function insert($table_name ,$data) {
		if (!is_array($data) || empty($data)) return 0;
		$data = $this->build_insert($data);
		$sql = sprintf("INSERT INTO %s %s", $table_name, $data);
		$result = $this->query($sql, false);
		if (!$result) return 0;
		$id = $this->insert_id();
		$this->set_default_link_id();
		return $id;
	}

	/**
	 * 插入多条数据
	 * @param string $table_name 表名
	 * @param array data 数据
	 * @return id
	 */

	public function insert_more($table_name ,$data) {
		if (!is_array($data) || empty($data)) return false;
		$sql = $this->build_insertmore($data);
		$sql = sprintf("INSERT INTO %s %s", $table_name, $sql);
		return $this->query($sql);
	}


	/**
	 * 根据单个条件更新数据
	 * @param  int    $id 主键ID
	 * @param  array  $data 参数
	 * @param  string $table_name 表名
	 * @param  string $id_key 主键名
	 * @return bool
	 */

	public function update($table_name,$data,$field_key = 'id' ,$id) {
		$id = (int) $id;
		if ($id < 1) return false;
		$data = $this->build_update($data);
		$where = $this->build_where(array($field_key=>$id));
		$sql = sprintf("UPDATE %s %s %s", $table_name, $data, $where);
		return $this->query($sql);
	}

	/**
	 * 根据字段条件更新数据
	 * @param  string $table_name 表名
	 * @param  array  $data 参数
	 * @param  array  $where  条件
	 * @return bool
	 */
	public function update_field($table_name ,$data, $where) {
		if (!is_array($data) || empty($data)) return false;
		if (!is_array($where) || empty($where)) return false;
		$data = $this->build_update($data);
		$where = $this->build_where($where);
		$sql = sprintf("UPDATE %s %s %s", $table_name, $data, $where);
		return $this->query($sql);
	}

	/**
	 * 删除数据
	 * @param  int|array $ids 单个id或者多个id
	 * @param  string $table_name 表名
	 * @param  string $delete_field 要删除的键名
	 * @return bool
	 */
	public function delete($table_name, $delete_field = 'id' ,$ids) {
		if (is_array($ids)) {
			$ids = $this->build_in($ids);
			$sql = sprintf("DELETE FROM %s WHERE %s %s", $table_name, $delete_field, $ids);
		} else {
			$where = $this->build_where(array($delete_field=>$ids));
			$sql = sprintf("DELETE FROM %s %s", $table_name, $where);
		}
		return $this->query($sql);
	}

	/**
	 * 通过条件语句删除数据
	 * @param  array  $field 条件数组 ['id' => 1]
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function delete_field($table_name ,$field) {
		if (!is_array($field) || empty($field)) return false;
		$where = $this->build_where($field);
		$sql = sprintf("DELETE FROM %s %s", $table_name, $where);
		return $this->query($sql);
	}

	/**
	 * 通过拼接获取一条数据
	 * @param string $id_key 字段
	 * @param $id	字段值
	 * @param $table_name 表名称
	 * @return array|bool
	 */

	public function get_row($table_name ,$id_key = 'id',$id) {
		$id = (int) $id;
		if ($id < 1) return array(); 
		$where = $this->build_where(array($id_key=>$id));
		$sql = sprintf("SELECT * FROM %s %s LIMIT 1", $table_name, $where);
		$result = $this->query($sql, false);
		if (!$result) return false;
		$r = $this->fetch_assoc($result);
		$this->set_default_link_id();
		return $r;
	}

	/**
	 * 通过条件语句获取一条信息
	 * @param  array  $field 条件数组 array('username' => 'username')
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function get_row_field($table_name,$field) {
		if (!is_array($field) || empty($field))
			return array();
		$where = $this->build_where($field);
		$sql = sprintf("SELECT * FROM %s %s LIMIT 1", $table_name, $where);
		$result = $this->query($sql, false);
		if (!$result) return false;
		$r = $this->fetch_assoc($result);
		$this->set_default_link_id();
		return $r;
	}

	//通过SQL语句取一条数据
	public function get_row_sql($sql) {
		$sql = trim($sql . ' ' .$this->build_limit(1));
		$result = $this->query($sql, false);
		if (!$result) return false;
		$r = $this->fetch_assoc($result);
		$this->set_default_link_id();
		return $r;
	}

	//拼接获取数据
	public function get_all($table_name,$fields ='',$where =[],$num = 20, $offest = 0, $order_field = 'id', $sort = 'DESC') {

		$where = $this->build_where($where);
		$limit = $this->build_limit($offest, $num);
		if(empty($fields)){
			$fields = '*';
		}
		if(is_string($fields)){
			$fields = rtrim($fields , ',');
		}

		if(is_array($fields)){
			$fields = implode(',' , $fields);
		}
		$sql = sprintf("SELECT %s FROM %s %s ORDER BY %s %s %s",$fields, $table_name, $where, $order_field, $sort, $limit);
		$result = $this->query($sql, false);
		if (!$result) return false;
		$temp = array();
		while ($row = $this->fetch_assoc($result)) {
			$temp[] = $row;
		}
		$count = $this->get_count($table_name, $where);
		$this->set_default_link_id();
		return array($temp, $count);
	}

	//sql 语句获取所有结果
	public function get_all_sql($sql) {
		$sql = trim($sql);
		$result = $this->query($sql, false);
		if (!$result) return false;
		while ($row = $this->fetch_assoc($result)) {
			$temp[] = $row;
		}

		$this->set_default_link_id(); //设置默认的link_id
		return $temp;
	}

	//获取总数
	public function get_count($table_name, $field = array()) {
		$where = $this->build_where($field);
		$sql = sprintf("SELECT COUNT(*) as count FROM %s %s LIMIT 1", $table_name, $where);
		$result = $this->query($sql, false);
		$result =  $this->fetch_assoc($result);
		$this->set_default_link_id(); //设置默认的link_id
		return $result['count'];
	}
	
}
