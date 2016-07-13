<?php
defined('IN_DS') or die('Hacking attempt');
/**
* 类别类
*/
class Tag{
	public function getHotTag(){
		$sql = "select * from tag as t left join rewrite_url as ru on t.id = ru.optdataid where ru.modeltype = 'tag' and ru.isjump='NO' and ru.`status` = 'yes' order by displayorder limit 30";
		$result = $GLOBALS['db']->getRows($sql);
		$hotTagList = $result;
		return $hotTagList;
	}
}