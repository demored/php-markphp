<?php
class indexController extends Controller {

	//白名单兼容接口访问
	public $markphp_list = array('test','testUpload','testDown');
	public function run() {
//		$this ->view->display('index.tpl');
		MarkPHP::getDao('user')->test();
//		print_r($this->view);
//		$this->view->assign('aaa' , 'bbbb');
//		$this ->view->display('index.tpl');
//		$this ->controller->ajax_return();
//	    echo MarkPHP::url('index|index', array('id' => 100,'name'=>'张三'));
//	    MarkPHP::redirect('index|test', array('id' => 100,'name'=>'张三'));
//		MarkPHP::getDao('user')->test_cache();
//		MarkPHP::getService('user')->index();
	}

	public function test(){
		echo 111;
		exit;
	}

	public function testUpload(){
        MarkPHP::import(null , [CORE_PATH.'/library'=>'Qiniu.php']);
        $qiniu = new qiniu([
				'secretKey' => 'vE4BaHJKm2Clg7kCHH5k_K3c-lkhk42M6l5w_YBB',
				'accessKey' => 'VuBe6WZnwKllbG3UhoUbElGyUF4h3uROxdg7By5o',
				'domain'    => 'ov81z8f2h.bkt.clouddn.com',
				'bucket'    => 'cert',
				'timeout'   => 300]);
        var_dump($qiniu->save($_FILES['test']));
	}

	public function testDown(){
		$down = MarkPHP::getLibrarys('download');
		$down ->downCloud('2.jpg' , "http://ov81z8f2h.bkt.clouddn.com/2");
	}

}

