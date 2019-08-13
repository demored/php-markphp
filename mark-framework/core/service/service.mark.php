<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');

// +----------------------------------------------------------------------
// | MARKPHP 1.0.1 [高性能、便捷、安全的PHP开发框架]
// +----------------------------------------------------------------------
// |【此框架只为证客内部系统开放，注意保密】
// +----------------------------------------------------------------------
// | Copyright (c) 2017 demored All rights reserved.
// +----------------------------------------------------------------------
// |Service服务类基类
// +----------------------------------------------------------------------

class serviceMark {
	//字段校验-用于进入数据库的字段映射
	public function parse_data($field, $data) {
		$field = (array) $field;
		$temp = array();
		foreach ($field as $val) {
			if (isset($data[$val[0]])) {
				if ($val[1] == 'int') {
					$data[$val[0]] = (int) $data[$val[0]];
				} elseif ($val[1] == 'obj') {
					$data[$val[0]] = serialize($data[$val[0]]);
				}
				$temp[$val[0]] = $data[$val[0]];
			}	
		}
		return $temp;
	}
	
}
