<?php
defined('IN_DS') or die('Hacking attempt');
define('D_PAGE_NAME', 'HOMEPAGE');
define("D_PAGE_VALUE",	'/');

include_once 'manager.php';
include_once 'tracking_block.php';

$cacheFileName = MEM_PREX . 'index';
$objCache = new Cache($cacheFileName);

if (DEBUG_MODE || isset($_GET['forcerefresh']))    $mainContent = '';
    else    $mainContent = $objCache->getCache();

if (!$mainContent) {
	$objCache->initialCache();
	$article = new Article();
	$recommandArticleList = $article->getRecommandArticleList();
	$tpl->assign('recommandArticleList',$recommandArticleList);
	$newestArticleList = $article->getNewArticeList();
	$tpl->assign('newestArticleList',$newestArticleList);
	$tag = new Tag();
	$hotTagList = $tag->getHotTag();
	$tpl->assign('hotTagList',$hotTagList);
	//加载页面特殊js,css文件以及meta信息
	$meta = '';
	$page_header = array(
		'meta' => $meta,
		'css' => $default_css,
		'js' => $default_js,
	);
	$tpl->assign('page_header'.$page_header);
	$tpl->display("index.html");
	$mainContent = $objCache->endCache();
}
echo $mainContent;
