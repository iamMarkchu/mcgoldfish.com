<?php
define('D_PAGE_NAME', 'CATAGORY');
$canonicalUri = isset($urlInfo['requestpath'])? $urlInfo['requestpath'] : $_SERVER['SCRIPT_URL'];
define("D_PAGE_VALUE",	$canonicalUri);

global $navList;
$staticFlag = false;
foreach ($navList as $k => $nav) {
    if($canonicalUri == $k){
    	$staticFlag = true;
    	break;
    }
}
$tpl->assign('staticFlag', $staticFlag);

$category = new Category;
$article = new Article;
if($staticFlag){
    $bindCategoryList = $navList[$canonicalUri]['bindCategoryList'];
    if(!empty($bindCategoryList)){
        $categoryList = $category->getCategoryForStatic($bindCategoryList);
        $articleList = $article->getArticleForCategories($bindCategoryList);
    }else{
        $categoryList = [];
    }
    $tpl->assign('pageh1', $navList[$canonicalUri]['icon'].$navList[$canonicalUri]['displayname']);
    $tpl->assign('categoryList', $categoryList);
    $tpl->assign('articleList', $articleList);
}else{

}






$hotCategory = $category->getPrimaryCategory(4);
$tpl->assign('hotCategory', $hotCategory);

$article = new Article;
$newestArticleList = $article->getArticleList();
list($newestArticleList , $usedIds) = $newestArticleList;
$tpl->assign('newestArticleList',$newestArticleList);
$hasUsedIdList = array_merge($hasUsedIdList , $usedIds);

$tag = new Tag;
$hotTagList = $tag->getHotTag();
$tpl->assign('hotTagList',$hotTagList);
$default_css[] = '/css/main_v2_category.css?ver='.VER;
$page_header = array(
	'meta' => $meta,
	'css' => $default_css,
	'js' => $default_js,
);
$tpl->assign('page_header',$page_header);
$tpl->template_dir = INCLUDE_ROOT. "view_v2";
$tpl->display('category.html');
