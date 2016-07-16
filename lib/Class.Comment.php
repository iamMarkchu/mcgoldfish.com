<?php
defined('IN_DS') or die('Hacking attempt');
/**
* 评论类
*/
class Comment{
	public function addComment($data){
		if(empty($data)) return "0";
		$key = array();
		$value = array();
		foreach ($data as $k => $v) {
			$key[] = "`".$k."`";
			$value[] = "'".$v."'";
		}
		$sql = "insert into comment (".implode(",", $key).") VALUES (".implode(",",$value).")";
		$flag = $GLOBALS['db']->query($sql);
		return $flag;
	}
}