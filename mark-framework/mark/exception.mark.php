<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');
//mark异常处理类
class MarkException extends Exception{

	//异常模板
	public static function errorTpl($e) {
		$MarkPHP_conf = MarkPHP::getConfig();
		$msg = $e->message;
		$mainErrorCode = self::getLineCode($e->getFile(), $e->getLine());
		self::_recordError($msg, $e->getFile(), $e->getLine(), trim($mainErrorCode));
		if (!$MarkPHP_conf['is_debug'] && $e->code == 10000) {
			$msg = '系统繁忙，请稍后再试';
		}

		if (self::is_ajax()) {
			$arr = array('status' => 0, 'message' => $msg, 'data' => array('code' => $e->code));
			echo json_encode($arr);
		} else {
			//如果debug关闭，则不显示debug错误信息
			//if (!$MarkPHP_conf['is_debug']) {
			//return MarkPHP::return500();
			//}
			//网页500
			header('HTTP/1.1 500 Internal Server Error');
			header("status: 500 Internal Server Error");
			$trace = $e->getTrace();
			$runTrace = $e->getTrace();
			echo "<h2>".$e->getMessage()."</h2>";
			echo "<br/>";
			echo 'NB '.$MarkPHP_conf['frame_name'].$MarkPHP_conf['version'];
			exit;
//			print_r($trace);
//			print_r($runTrace);
			echo '系统调试中!!!!';
		}
	}

	private static function is_ajax() {
		if ($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
		if ($_POST['MarkPHP_ajax'] || $_GET['MarkPHP_ajax']) return true; //程序中自定义AJAX标识
		return false;
	}

	static function getLineCode($file,$line) {
		$fp = fopen($file,'r');
		$i = 0;
		while(!feof($fp)) {
			$i++;
			$c = fgets($fp);
			if($i==$line) {
				return $c;
				break;
			}
		}
	}


	//记录错误日志
	private static function _recordError($msg, $file, $line, $code){
		$string = '';
		$string.='['.date('Y-m-d H:i:s').']msg:'.$msg.';file:'.$file.';line:'.$line.';code:'.$code.'';
		MarkPHP::log($string, ERROR);
	}

}