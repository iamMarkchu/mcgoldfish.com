<?php
defined('IN_DS') or die('Hacking attempt');
define('D_PAGE_NAME', 'CATAGORY');
$canonical_uri = $_rewriteUrlInfo['RequestPath'];
define("D_PAGE_VALUE",	$canonical_uri);

$category = new Category();
$categoryId = $_rewriteUrlInfo['optdataid'];
$cateInfo = $category->getCategorybyIdAndType($categoryId);
$tpl->assign('cateInfo',$cateInfo);
if(empty($cateInfo['parentcategoryid'])){
	$subCateList = $category->getCategorybyIdAndType($categoryId,'category',true);
}
$tpl->assign('subCateList',$subCateList);
$article = new Article();
$articleList = array();
if(!isset($subCateList)) $cateList[] = $cateInfo;
else $cateList = $subCateList;
foreach ($cateList as $k => $v) {
	$articleList = array_merge($articleList,$article->getArticleByCategory($v['id']));
}
$tpl->assign('articleList',$articleList);
$pageMeta = new PageMeta();
$meta = $pageMeta->get_article_meta($articleInfo);
if(empty($meta))
	$meta['MetaTitle'] = $articleInfo['title'];
$page_header = array(
	'meta' => $meta,
	'css' => $default_css,
	'js' => $default_js,
);
$tpl->assign('page_header',$page_header);
$tpl->display('category.html');
