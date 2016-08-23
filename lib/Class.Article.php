<?php 
defined('IN_DS') or die ('Hacking attempt');
/**
* 文章类
*/
class Article 
{
	public function getArticleInfoById($articleid){
		if(empty($articleid)) return array();
		$sql = "select ru.`requestpath`,a.* from article as a left join rewrite_url as ru on a.id = ru.optdataid where a.`status` = 'active' and ru.modeltype = 'article' and ru.isjump='NO' and ru.`status` = 'yes' and a.id = {$articleid}";
		$f = $GLOBALS['db']->query($sql); 
		$articleInfo = $GLOBALS['db']->getRow($f);
		$articleInfo['tagInfo'] = $this->getArticleTagList($articleid);
		$articleInfo['categoryInfo'] = $this->getArticleCategoryList($articleid);
		return $articleInfo;
	}
	public function getArticleList($orderby='addtime',$limit=8,$isNeedDetail=false){
		$sql = "select ru.`requestpath`,a.* from article as a left join rewrite_url as ru on a.id = ru.optdataid where a.`status` = 'active' and ru.modeltype = 'article' and ru.isjump='NO' and ru.`status` = 'yes'  order by {$orderby} limit 8";
		$result = $GLOBALS['db']->getRows($sql);
		$articleList = array();
		if($isNeedDetail){
			foreach ($result as $k => $v) {
				$articleid = $v['id'];
				$result[$k] = $this->checkArticleImage($result[$k]);
				$result[$k]['shortDesc'] = strip_tags($v['content']);
				$result[$k]['tagInfo'] = $this->getArticleTagList($articleid);
				$result[$k]['categoryInfo'] = $this->getArticleCategoryList($articleid);
			}
		}
		$articleList = $result;
		return $articleList;
	}
	// public function getNewArticeList(){
	// 	$sql = "select ru.`requestpath`,a.* from article as a left join rewrite_url as ru on a.id = ru.optdataid where a.`status` = 'active' and ru.modeltype = 'article' and ru.isjump='NO' and ru.`status` = 'yes'  order by addtime limit 8";
	// 	$result = $GLOBALS['db']->getRows($sql);
	// 	$newArticleList = $result;
	// 	return $newArticleList;
	// }
	public function checkArticleImage($articleInfo){
		if(empty($articleInfo['image'])){
			$articleInfo['image'] = "/images/1.jpg";
		}
		return $articleInfo;
	}
	public function getArticleTagList($articleid){
		if(empty($articleid)) return array();
		$sql = "select t.displayname,ru.`requestpath` from tag_mapping as tm left join tag as t on tm.tagid = t.id left join rewrite_url as ru on t.id = ru.optdataid where tm.datatype = 'article' and tm.`status` = 'active' and tm.optdataid = {$articleid} and ru.isjump='NO' and ru.`status` = 'yes'";
		$tmpResult = $GLOBALS['db']->getRows($sql);
		return $tmpResult;
	}
	public function getArticleCategoryList($articleid){
		if(empty($articleid)) return array();
		$sql = "select c.id,c.parentcategoryid,c.displayname,ru.`requestpath` from category_mapping as cm left join category as c on cm.categoryid = c.id left join rewrite_url as ru on c.id = ru.optdataid where cm.datatype = 'article' and cm.`status` = 'active' and cm.optdataid = {$articleid} and ru.isjump='NO' and ru.`status` = 'yes'";
		//echo $sql;die;
		$tmpResult = $GLOBALS['db']->getRows($sql);
		return $tmpResult;
	}
}