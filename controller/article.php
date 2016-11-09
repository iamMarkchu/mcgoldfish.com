<?php
define('D_PAGE_NAME', 'ARTICLE');
if (!(int)$optDataId)    die('404!');
$canonicalUri = $urlInfo['requestpath'];
define("D_PAGE_VALUE",	$canonicalUri);

include_once INCLUDE_ROOT."functions/tracking/index.php";


/**
 * article
 */
$article = new Article();
$articleInfo = $article->getArticleInfoById($optDataId);
$tpl->assign('articleInfo',$articleInfo);

/**
 * 相关分类
 */


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
	$HighLightCss = [
					  '/css/main_v2_blog.css',
					  'http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.8.0/styles/monokai-sublime.min.css'
					];
	$default_css = array_merge($default_css,$HighLightCss);
	$HighLightJs = [
					  'http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.8.0/highlight.min.js',
					  '/js/app.js'
				   ];
	$default_js['footer'] = array_merge($default_js['footer'],$HighLightJs);
}

$page_header = array(
	'meta' => $meta,
	'css' => $default_css,
	'js' => $default_js,
);
$tpl->assign('page_header',$page_header);

$tpl->template_dir = INCLUDE_ROOT. "view_v2";
$tpl->display('article.html');



