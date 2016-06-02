<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}
global $g_homepage_feature_merchant,$tpl,$default_css,$default_js;

define('D_PAGE_NAME', '404');
$objTerm = new Term();
$all_recommand_ids = array("35302","35271","35381","35383","35451","35530","35157","35326","35440","143116","58438","35312","35455","35236","35448","35510","83634","83633","49374","83625","35718","40518","58408","35704","35692");
shuffle($all_recommand_ids);
$recommand_ids = array_slice($all_recommand_ids,0,18);
$recommand_term = $objTerm->get_term_for_404($recommand_ids);
$tpl->assign('recommand_term',$recommand_term);

/*$recommand_coupon_ids = array("4444649","4399382","3075908","4377145","4487940");
$couponlist = $objTerm->get_coupons_by_ids($recommand_coupon_ids);
//die(dump($couponlist));
$tpl->assign('couponlist',$couponlist);*/
/*
 * meta
 */
$meta = array();
$meta['MetaTitle'] = '404 - ' . TOP_LEVEL_DOMAIN_NAME;
$meta['MetaDesc'] = '';
$meta['MetaKeyword'] = '';

/*
 * paths
 */
$paths = array();
$paths[] = array('name' => 'Home', 'url' => '/');
$paths[] = array('name' => '404', 'url' => '');
$tpl->assign('paths', $paths);

$page_header = array(
	'meta' => $meta,
	'css' => $default_css, 
	'js' => $default_js,
);
	#get block category data
	$objCategory = new Category();
	//modify the function that can get limited number of categrories
	$category_data = $objCategory->get_top_category('de');
	$tpl->assign('category_data', $category_data);

	$limit=18;
	$recent_launched_data = $objTerm->get_home_page_new_term($limit);
	//left related stores
	$homepage_id = array("35111","35139","81717","83539","35109","35258","35487");
	shuffle($homepage_id);
	$re_term_id = $homepage_id[0];
	$relatedStores = $objTerm->get_related_term($re_term_id);
	$relatedStores = array_slice($relatedStores, 0, 9);
	$tpl->assign('featured_stores', $relatedStores);
	$tpl->assign('recent_launched_data', $recent_launched_data);
	$tpl->assign('page_header', $page_header);


$tpl->display('de_404.html');
?>