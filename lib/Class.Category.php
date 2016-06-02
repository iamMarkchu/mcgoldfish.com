<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}

class Category{
	public function getAllCategory(){
		$sql = "select c.*,ru.requestpath from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = 'category' and ru.status = 'yes' and isjump = 'NO' and c.parentcategoryid = 0 order by displayorder ";
		$executeResult = $GLOBALS['db']->getRows($sql);
		$categoryInfo = $executeResult;
		foreach ($categoryInfo as $k => $v) {
			$sql = "select c.*,ru.requestpath from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = 'category' and ru.status = 'yes' and isjump = 'NO' and c.parentcategoryid = {$v['id']} order by displayorder ";
			$executeResult = $GLOBALS['db']->getRows($sql);
			$categoryInfo[$k]['child'] = $executeResult;
		}
		return $categoryInfo;
	}
}