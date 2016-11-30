<?php

if (!(int)$optDataId)    die('404!');
$canonicalUri = $urlInfo['requestpath'];
define('D_PAGE_NAME', 'ARTICLE');
define("D_PAGE_VALUE",	$canonicalUri);


//include_once INCLUDE_ROOT."functions/tracking/index.php";

/**
 * article
 */
$article = new Article;
$articleInfo = $article->getArticleInfoById($optDataId);

$tpl->assign('articleInfo',$articleInfo);

/**
 * 上一个 下一个
 */

$nextPreArticleList = $article->getArticleForNextPre($articleInfo['id']);
if(count($nextPreArticleList) == 1){
	$tmp['requestpath'] = 'javascript:void(0)';
	$tmp['title'] = '没有了';
	$tmp['id'] = 0;
	if($nextPreArticleList[0]['id'] > $articleInfo['id']){
		array_unshift($nextPreArticleList, $tmp);
	}else{
		$nextPreArticleList[] = $tmp;
	}
}
$tpl->assign('nextPreArticleList', $nextPreArticleList);

/**
 * comment
 */
$comment = new Comment;
$commentList = $comment->getCommentList($optDataId);
$tpl->assign('showComment', true);
$tpl->assign('commentCount',count($commentList));
$tpl->assign('commentList',$commentList);

/**
 * 相关分类
 */
$category = new Category;
if(isset($articleInfo['categoryInfo'])){
	$parentCateIds = [];
	foreach ($articleInfo['categoryInfo'] as $cate) {
		if(intval($cate['parentcategoryid']) != 0){
			$parentCateIds[] = intval($cate['parentcategoryid']);
		}
	}
	if(!empty($parentCateIds)){
		$relatedCategoryList = $category->getRelatedCategoryByParent($parentCateIds);
		//随机选四个
		if(count($relatedCategoryList) > 4){
			shuffle($relatedCategoryList);
			$relatedCategoryList = array_slice($relatedCategoryList, 0, 4);
		}
		$tpl->assign('hotCategory', $relatedCategoryList);
	}

}

/**
 * breadcrumb
 * 获取类别信息
 */
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

$tpl->assign('breadcrumb',$breadcrumb);

$pageMeta = new PageMeta();
$meta = $pageMeta->get_article_meta($articleInfo);
if(empty($meta))
	$meta['MetaTitle'] = $articleInfo['title'];

$isNeedHighLight = true;
if($isNeedHighLight){
	$HighLightCss = [
					  '/css/main_v2_blog.css?ver='. VER,
					  'http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.8.0/styles/monokai-sublime.min.css'
					];
	$default_css = array_merge($default_css,$HighLightCss);
	$HighLightJs = [
					  'http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.8.0/highlight.min.js',
					  '/js/app.js'
				   ];
	$default_js['footer'] = array_merge($default_js['footer'],$HighLightJs);
}
$isNeedDuoShuo = true;
if($isNeedDuoShuo){
	$duoShuoJs = ['/js/duoshuo.js'];
	$default_js['footer'] = array_merge($default_js['footer'],$duoShuoJs);	
}

$page_header = array(
	'meta' => $meta,
	'css' => $default_css,
	'js' => $default_js,
);
$tpl->assign('page_header',$page_header);

$tpl->template_dir = INCLUDE_ROOT. "view_v2";
ob_start();
$tpl->display('article.html');
$content = ob_get_contents();
//$order   = array("\r\n", "\n", "\r");
//$content=str_replace($order, "", $content);
//$content = preg_replace("/[\s]+/is"," ",$content);
ob_end_clean();
$modtimestamp = "<!-- last mod time:".date("Y-m-d H:i:s")." -->\n";
echo  $content.$modtimestamp;



