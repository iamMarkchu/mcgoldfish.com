<?php 
defined('IN_DS') or die ('Hacking attempt');

/**
* 助手类
*/
class Manager 
{
	public static function clearSmartyCache(){
		$cmd = "rm -rf ".INCLUDE_ROOT . 'cache/smarty_cache/*';
		$flag = system($cmd);
	}	
}