<?php
defined('IN_DS')    or die('Hacking attempt');
define('D_PAGE_NAME', 'ARTICLE');
if (!(int)$_opt_data_id)    goto_404();
$canonical_uri = $_rewriteUrlInfo['RequestPath'];
define("D_PAGE_VALUE",	$canonical_uri);
//include_once 'verify.php';
//include_once 'tracking_block.php';
//include_once INCLUDE_ROOT . 'etc/term_meta.php';
// set cache
$cacheFileName = MEM_PREX . 'article_' . $_opt_data_id;
$objCache = new Cache($cacheFileName);
if (DEBUG_MODE || isset($_GET['forcerefresh'])){
	$mainContent = '';
}else{
	$mainContent = $objCache->getCache();
}
if (!$mainContent) {
	$objCache->initialCache();
	$article = new Article();
	$articleInfo = $article->getArticleInfoById($_opt_data_id);
	$articleInfo['content'] = nl2br($articleInfo['content']);
	$tpl->assign('articleInfo',$articleInfo);
	$tpl->display('article.html');
	$mainContent = $objCache->endCache();
}
echo $mainContent;




