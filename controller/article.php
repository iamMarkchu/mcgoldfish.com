<?php
define('D_PAGE_NAME', 'ARTICLE');
if (!(int)$optDataId)    die('404!');
$canonicalUri = $urlInfo['requestpath'];
define("D_PAGE_VALUE",	$canonicalUri);

/**
 * article
 */
$article = new Article();
$articleInfo = $article->getArticleInfoById($optDataId);
//$articleInfo['content'] = nl2br($articleInfo['content']);
$tpl->assign('articleInfo',$articleInfo);

/**
 * comment
 */
$comment = new Comment();
$commentList = $comment->getCommentList($optDataId);
$tpl->assign('commentCount',count($commentList));
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

$pageMeta = new PageMeta();
$meta = $pageMeta->get_article_meta($articleInfo);
if(empty($meta))
	$meta['MetaTitle'] = $articleInfo['title'];

$isNeedHighLight = true;
if($isNeedHighLight){
	$HighLightCss = array('/third-party/bower_components/highlightjs/styles/monokai-sublime.css');
	$default_css = array_merge($default_css,$HighLightCss);
	$HighLightJs = array('/third-party/bower_components/highlightjs/highlight.pack.min.js');
	$default_js['footer'] = array_merge($default_js['footer'],$HighLightJs);
}

$page_header = array(
	'meta' => $meta,
	'css' => $default_css,
	'js' => $default_js,
);
$tpl->assign('page_header',$page_header);

$tpl->display('article.html');



