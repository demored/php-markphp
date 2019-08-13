<?php

class download {
	
	private $allow = array(".jpg", ".txt", ".gif", ".png", ".rar"); //允许下载的文件类型


	public function downCloud($file_name , $file){
		$this->check_file_ext($file_name);
		$ext = $this -> get_extension($file);
		$file_info = get_headers($file);
		$size = $file_info[4];
		$file = fopen($file,"r");
		//返回的文件类型
		Header("Content-type: application/octet-stream");
		//按照字节大小返回
		Header("Accept-Ranges: bytes");
		//返回文件的大小
		Header("Accept-Length: ".$size);
		//这里对客户端的弹出对话框，对应的文件名
		Header("Content-Disposition: attachment; filename=".$file_name);
		//修改之前，一次性将数据传输给客户端
//            echo fread($file, $size);
		//修改之后，一次只传输1024个字节的数据给客户端
		//向客户端回送数据
		$buffer=1024;//
		//判断文件是否读完
		while (!feof($file)) {
			//将文件读入内存
			$file_data=fread($file,$buffer);
			//每次向客户端回送1024个字节的数据
			echo $file_data;
		}

		fclose($file);
	}

	//获取文件后缀名
	function get_extension($file)
	{
		return substr(strrchr($file, '.'), 1);
	}
	public function down($file_name = '', $file = '' ,$mime_type = 'application/octet-stream') {
		$this->check_file_ext($file_name);
		header("Content-Type: {$mime_type}");
		$file = '"' . htmlspecialchars($file_name) . '"';
        $file_size = filesize($file);
        header("Content-Disposition: attachment; filename={$file_name}; charset=utf-8");
        header("Content-Length: {$file_size}"); 
        readfile($file);
        exit;  
	}
	private function check_file_ext($file) {

		$file_ext = strtolower(substr($file, -4));
		if (!in_array($file_ext, $this->allow)) exit('this file is deny!');
		return true;
	}

} 