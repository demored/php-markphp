<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');
//文件缓存类
class filecacheMark {
	
	private $cache_path = '.'; //缓存路径
	public function set_cache($filename, $data, $time = 0) {
		 $filename = $this->get_cache_filename($filename);
		 @file_put_contents($filename, '<?php exit;?>' . time() .'('.$time.')' .  serialize($data));
		 clearstatcache();
		 return true;
	}

	public function get_cache($filename) {
		$filename = $this->get_cache_filename($filename);
		if (!file_exists($filename)) return false;
		$data = file_get_contents($filename); //获取缓存
		/* 缓存过期的情况 */
		$filetime = substr($data, 13, 10);
		$pos = strpos($data, ')');
		$cachetime = substr($data, 24, $pos - 24);
		$data  = substr($data, $pos +1);
		if ($cachetime == 0) return @unserialize($data);
		if (time() > ($filetime + $cachetime)) {
			@unlink($filename);
			return false; //缓存过期
		}
        return @unserialize($data);
	}

	public function clear($filename) {
		$filename = $this->get_cache_filename($filename);
		if (!file_exists($filename)) return true;
		@unlink($filename);
		return true;
	}

	public function clear_all() {
		@set_time_limit(3600);
		$path = opendir($this->cache_path);		
		while (false !== ($filename = readdir($path))) {
			if ($filename !== '.' && $filename !== '..') {
   				@unlink($this->cache_path . '/' .$filename);
			}
		}
		closedir($path);
		return true;
	}

	public function set_cache_path($path) {
		return $this->cache_path = $path;
	}

	private function get_cache_filename($filename) {
		$filename = md5($filename); //文件名MD5加密
		$filename = $this->cache_path .'/'. $filename . '.php';
		return $filename;
	}
}
