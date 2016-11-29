<?php
/**
* 类别类
*/
class Category{
	public function getPrimaryCategory($limit=''){
		if(!empty($limit)) $limit = 'limit '.$limit;
		$sql = "select c.*,ru.requestpath from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = 'category' and ru.status = 'yes' and isjump = 'NO' and c.parentcategoryid = 0 order by displayorder {$limit}";
		$executeResult = $GLOBALS['db']->getRows($sql);
		$categoryInfo = $executeResult;
		return $categoryInfo;
	}

	public function getAllCategory($limit=''){
		$categoryInfo = $this->getPrimaryCategory($limit);
		foreach ($categoryInfo as $k => $v) {
			$sql = "select c.*,ru.requestpath from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = 'category' and ru.status = 'yes' and isjump = 'NO' and c.parentcategoryid = {$v['id']} order by displayorder ";
			$executeResult = $GLOBALS['db']->getRows($sql);
			$categoryInfo[$k]['child'] = $executeResult;
		}
		return $categoryInfo;
	}
	public function getCategorybyIdAndType($dataid, $datatype='category', $isParent=false){
		if(empty($dataid)) return array();
		if(!$isParent) $whereId = 'c.id = '.$dataid;
		else $whereId = 'c.parentcategoryid = '.$dataid;
		$sql = "select c.id,c.displayname,c.parentcategoryid,ru.`requestpath` from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = '{$datatype}' and ru.`status` = 'yes' and {$whereId} and ru.isjump='NO'";
		if(!$isParent){
			$tmpResource = $GLOBALS['db']->query($sql);
			$tmpResult = $GLOBALS['db']->getRow($tmpResource);
		}else{
			$tmpResult = $GLOBALS['db']->getRows($sql);
		}
		return $tmpResult;
	}
	public function getRelatedCategoryByParent($parentCateIds ,$datatype='category'){
		if(empty($parentCateIds)) return array();
		$sql = "select c.id,c.displayname,c.parentcategoryid,c.articlecount,c.aliasesname,ru.`requestpath` from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = '{$datatype}' and ru.`status` = 'yes' and c.parentcategoryid in (".implode(",", $parentCateIds).") and ru.isjump='NO'";
		$tmpResult = $GLOBALS['db']->getRows($sql);
		return $tmpResult;
	}

	public function getCategoryForStatic($ids){
		$sql = "select c.id,c.displayname,c.parentcategoryid,c.articlecount,c.aliasesname,ru.`requestpath` from category as c left join rewrite_url as ru on c.id = ru.optdataid where ru.modeltype = 'category' and ru.`status` = 'yes' and c.id in (".implode(",", $ids).") and ru.isjump='NO'";
		$tmpResult = $GLOBALS['db']->getRows($sql);
		return $tmpResult;
	}
}