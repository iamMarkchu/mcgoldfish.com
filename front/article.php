<?php
defined('IN_DS')  or die('Hacking attempt');
define('D_PAGE_NAME', 'ARTICLE');
if (!(int)$_opt_data_id)    goto_404();
$canonical_uri = $_rewriteUrlInfo['RequestPath'];
define("D_PAGE_VALUE",	$canonical_uri);
//include_once 'verify.php';
include_once 'tracking_block.php';
//include_once INCLUDE_ROOT . 'etc/term_meta.php';
// set cache
$cacheFileName = MEM_PREX . 'article_' . $_opt_data_id;
$objCache = new Cache($cacheFileName);
if (DEBUG_MODE || isset($_GET['forcerefresh']))    $mainContent = '';
    else    $mainContent = $objCache->getCache();

if (!$mainContent) {
	$objCache->initialCache();
	/**
	 * article
	 */
	$article = new Article();
	$articleInfo = $article->getArticleInfoById($_opt_data_id);
	$articleInfo['content'] = nl2br($articleInfo['content']);
	$tpl->assign('articleInfo',$articleInfo);
	/**
	 * comment
	 */
	$comment = new Comment();
	$commentList = $comment->getCommentList($_opt_data_id);
	$tpl->assign('commentList',$commentList);
	/**
	 * breadcrumb
	 * 获取类别信息
	 */
	$category = new Category();
	$home = array("url"=>"/","title"=>"首页");
	$breadcrumb[] = $home;
	if(!empty($articleInfo['categoryInfo'])){
		$cateInfo = $articleInfo['categoryInfo'][0];
		$parentCateInfo = $category->getCategorybyIdAndType($cateInfo['parentcategoryid']);
		$breadcrumb[] = array('url'=>$parentCateInfo['requestpath'],"title"=>$parentCateInfo['displayname']);
		$breadcrumb[] = array("url"=>$cateInfo['requestpath'],"title"=>$cateInfo['displayname']);
	}else{
		$breadcrumb[] = array("url"=>'/all-articles.html',"title"=>'所有文章');
	}
	$breadcrumb[] = array("url"=>'',"title"=>$articleInfo['title']);
	$tpl->assign('breadcrumb',$breadcrumb);
	$tpl->display('article.html');
	$mainContent = $objCache->endCache();
}
echo $mainContent;




