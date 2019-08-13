<?php
class indexController extends Controller {
	//白名单兼容接口访问
	public $markphp_list = array('test');
	public function run() {
		echo 'hello';
	}
	public function before(){
		echo 'before';
	}
	public function after(){
		echo 'after';
	}

}

