<?php
defined('IN_DS') or die('Hacking attempt');
/**
* 类别类
*/
class Category{
	public function getPrimaryCategory(){
		$sql = "select c.*,ru.requestpath from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = 'category' and ru.status = 'yes' and isjump = 'NO' and c.parentcategoryid = 0 order by displayorder ";
		$executeResult = $GLOBALS['db']->getRows($sql);
		$categoryInfo = $executeResult;
		return $categoryInfo;
	}
	public function getAllCategory(){
		$categoryInfo = $this->getPrimaryCategory();
		foreach ($categoryInfo as $k => $v) {
			$sql = "select c.*,ru.requestpath from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = 'category' and ru.status = 'yes' and isjump = 'NO' and c.parentcategoryid = {$v['id']} order by displayorder ";
			$executeResult = $GLOBALS['db']->getRows($sql);
			$categoryInfo[$k]['child'] = $executeResult;
		}
		return $categoryInfo;
	}
	public function getCategorybyIdAndType($dataid,$datatype='category'){
		if(empty($dataid)) return array();
		$sql = "select c.id,c.displayname,ru.`requestpath` from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = '{$datatype}' and ru.`status` = 'yes' and ru.optdataid = {$dataid} and ru.isjump='NO'";
		$tmpResource = $GLOBALS['db']->query($sql);
		$tmpResult = $GLOBALS['db']->getRow($tmpResource);
		return $tmpResult;
	}
}