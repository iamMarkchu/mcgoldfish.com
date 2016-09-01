<?php
define('D_PAGE_NAME', 'HOMEPAGE');
define("D_PAGE_VALUE", '/');

$article = new Article();
$recommandArticleList = $article->getArticleList('maintainorder',8,true);
$tpl->assign('articleList',$recommandArticleList);

$newestArticleList = $article->getArticleList();
$tpl->assign('newestArticleList',$newestArticleList);

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
