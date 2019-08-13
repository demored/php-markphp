<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');
// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// | Dao-sqlbuild 数据字段安全封装类
// +----------------------------------------------------------------------

require_once("dbhandler.mark.php");
class sqlbuildMark extends dbhandlerMark {

	/**
	 * 组装INSERT语句 
	 * 返回：('key') VALUES ('value')
	 * @param  array $val 参数  array('key' => 'value')
	 * @return string
	 */
	public function build_insert($val) {
		if (!is_array($val) || empty($val)) return '';
		$temp_v = '(' . $this->build_implode($val). ')';
		$val = array_keys($val);
		$temp_k = '(' . $this->build_implode($val, 1). ')';
		return $temp_k . ' VALUES ' . $temp_v;
	}

	/**
	 * 组装多条语句插入
	 * @param array $field 字段
	 * @param array $data
	 * @return string
	 */
	public function build_insertmore($data) {
		$field = array_keys($data[0]);
		$field = ' (' . $this->build_implode($field, 1) . ') '; //字段组装
		$temp_data = array();
		$data = (array) $data;
		foreach ($data as $val) {
			$temp_data[] = '(' . $this->build_implode($val) . ')';
		}
		$temp_data = implode(',', $temp_data);
		return $field . ' VALUES ' . $temp_data;
	}

	/**
	 * 组装UPDATE语句 
	 * 返回：SET name = 'aaaaa'
	 * @param  array $val  array('key' => 'value')
	 * @return string `key` = 'value' 
	 */
	public function build_update($val) {
		if (!is_array($val) || empty($val)) return '';
		$temp = array();
		foreach ($val as $k => $v) {
			$temp[] = $this->build_kv($k, $v);
		}
		return 'SET ' . implode(',', $temp);
	}
	
	/**
	 * 组装LIMIT语句
	 * 返回：LIMIT 0,10 
	 * @param  int $start 开始
	 * @param  int $num   条数
	 * @return string
	 */
	public function build_limit($start, $num = NULL) {
		$start = (int) $start;
		$start = ($start < 0) ? 0 : $start;
		if ($num === NULL) {
			return 'LIMIT ' . $start;
		} else {
			$num = abs((int) $num);
			return 'LIMIT ' . $start .' ,'. $num;
		}
	}
	
	/**
	 * 组装IN语句
	 * 返回：('1','2','3')
	 * DAO中使用方法：$this->dao->db->build_in($val)
	 * @param  array $val 数组值  例如：ID:array(1,2,3)
	 * @return string
	 */
	public function build_in($val) {
		$val = $this->build_implode($val);
		return ' IN (' . $val . ')';
	}

	/**
	 * 组装AND符号的WHERE语句
	 * 支持复杂类型：例如:$val = array("name" => array("like" => "sdasd"));
	 * 返回：WHERE a = 'a' AND b = 'b'
	 * DAO中使用方法：$this->dao->db->build_where($val)
	 * @param array $val array('key' => 'val')
	 * @return string
	 */
	public function build_where($val) {
		if (!is_array($val) || empty($val)) return '';
		$temp = array();
		foreach ($val as $k => $v) {
			if (is_array($v)) {
				$ktmp = $this->build_escape($k, 1);
				if (array_keys($v) !== range(0, count($v) - 1)) {
					foreach($v as $op => $value) {
						$temp[] = $ktmp .' '. $op .' '. $this->build_escape($value);
					}	
				} else {
					$temp[] = $ktmp . $this->build_in($v);
				}
			}else{
				$temp[] = $this->build_kv($k, $v);
			}
		}
		return ' WHERE ' . implode(' AND ', $temp);
	}
	
	/**
	 * 单个或数组参数过滤
	 * DAO中使用方法：$this->dao->db->build_escape($val, $iskey = 0)
	 * @param  string|array $val
	 * @param  int          $iskey 0-过滤value值，1-过滤字段
	 * @return string
	 */
	public function build_escape($val, $iskey = 0) {
		if (is_array($val)) {
			foreach ($val as $k => $v) {
				$val[$k] = trim($this->build_escape_single($v, $iskey));
			}
			return $val;
		} 
		return $this->build_escape_single($val, $iskey);
	}
	
	/**
	 * 组装KEY=VALUE形式
	 * 返回：a = 'a'
	 * DAO中使用方法：$this->dao->db->build_kv($k, $v)
	 * @param  string $k KEY值
	 * @param  string $v VALUE值
	 * @return string
	 */
	public function build_kv($k, $v) {
		return $this->build_escape($k, 1) . ' = ' . $this->build_escape($v);
	}
	
	/**
	 * 将数组值通过，隔开
	 * 返回：'1','2','3'
     * DAO中使用方法：$this->dao->db->build_implode($val, $iskey = 0)
	 * @param  array $val   值
	 * @param  int   $iskey 0-过滤value值，1-过滤字段
	 * @return string 
	 */
	public function build_implode($val, $iskey = 0) {
		if (!is_array($val) || empty($val)) return '';
		return implode(',', $this->build_escape($val, $iskey));
	}
	
	/**
	 * 检查DAO中进来的数组参数是否key键存在
	 * DAO中使用方法：$this->dao->db->build_key($data, $fields)
	 * @param array $data  例如：array("username" => 'asdasd')
	 * @param string $fields  例如："username,password"
	 */
	public function build_key($data, $fields) {
		$fields = explode(',', $fields);
		$temp = array();
		foreach ($data as $key => $value) {
			if (in_array($key, $fields)) {
				$temp[$key] = $value;
			}
		}
		return $temp;
	}

	/**
	 *私有SQL过滤
	 * 
	 * @param  string $val 过滤的值
	 * @param  int    $iskey 0-过滤value值，1-过滤字段
	 * @return string
	 */
	private function build_escape_single($val, $iskey = 0) {
		if ($iskey === 0) {
			if (is_numeric($val)) {
				return " '" . $val . "' ";
			} else {
				return " '" . addslashes(stripslashes($val)) . "' ";
			}
		} else {
			$val = str_replace(array('`', ' '), '', $val);
			return ' `'.addslashes(stripslashes($val)).'` ';
		}
	}
}
