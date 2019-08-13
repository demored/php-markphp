<?php
if (!defined('IS_MARKPHP')) exit('Access Denied!');
require_once("Smarty/Smarty.class.php");

class viewMark extends Smarty {

	public function init_view(){
		$markphp_conf = MarkPHP::getConfig();
		$this->setTemplateDir($markphp_conf['template']['template_path']);
		$this->setCompileDir($markphp_conf['template']['template_c_path']);
		$this->setConfigDir($markphp_conf['template']['template_config']);
		$this->setCacheDir($markphp_conf['template']['template_cache']);
		$this->caching = Smarty::CACHING_LIFETIME_CURRENT;
	}
}
