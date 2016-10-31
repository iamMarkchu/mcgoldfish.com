<?php
define('D_PAGE_NAME', 'HOMEPAGE');
define("D_PAGE_VALUE", '/');

include_once INCLUDE_ROOT."functions/tracking/index.php";
$article = new Article();
$hasUsedIdList = array();
$recommandArticleList = $article->getArticleList(array(),'maintainorder',8,true);
list($recommandArticleList , $usedIds) = $recommandArticleList;
$tpl->assign('articleList',$recommandArticleList);
$hasUsedIdList = array_merge($hasUsedIdList , $usedIds);

$highClickArticleList = $article->getArticleList($hasUsedIdList,'clickcount desc',5);
list($highClickArticleList , $usedIds) = $highClickArticleList;
$tpl->assign('highClickArticleList' , $highClickArticleList);
$hasUsedIdList = array_merge($hasUsedIdList , $usedIds);

$comment = new Comment();
$newestCommentList = $comment->getNewstCommentList();
$tpl->assign('newestCommentList',$newestCommentList);

$newestArticleList = $article->getArticleList($hasUsedIdList);
list($newestArticleList , $usedIds) = $newestArticleList;
$tpl->assign('newestArticleList',$newestArticleList);
$hasUsedIdList = array_merge($hasUsedIdList , $usedIds);

$tag = new Tag();
$hotTagList = $tag->getHotTag();
$tpl->assign('hotTagList',$hotTagList);

//处理meta,js,css
$pageMeta = new PageMeta();
$meta = $pageMeta->get_home_meta();
$isNeedGlide = true;
if($isNeedGlide){
	$glideCss = array('/third-party/bower_components/glidejs/dist/css/glide.core.css','/third-party/bower_components/glidejs/dist/css/glide.theme.css');
	$default_css = array_merge($default_css,$glideCss);
	$glideJs = array('/third-party/bower_components/glidejs/dist/glide.js');
	$default_js['footer'] = array_merge($default_js['footer'],$glideJs);
}
$page_header = array(
	'meta' => $meta,
	'css' => $default_css,
	'js' => $default_js,
);
$tpl->assign('page_header',$page_header);
$tpl->display("index.html");
