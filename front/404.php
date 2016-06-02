<?php
define('IN_DS', true);

include_once dirname(dirname(__FILE__)) . '/initiate.php';

define('D_PAGE_NAME', '404');
$termObj = new Term();
header("HTTP/1.0 404 Not Found");
/*
* block
*/
$termObj = new Term();
$relatedStores = $termObj->get_related_term_by_term_ids($g_homepage_feature_merchant);
$tpl->assign('featured_stores', $relatedStores);

$category_data = $termObj->get_first_level_category_by_country();
$tpl->assign('category_data', $category_data);
//get info for shops
	$term_shops = $objTerm->get_term_for_shops();
	$tpl->assign('shops',$term_shops);

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

/*
* sidebar first char link
*/
$rewriteUrlObj = new RewriteUrl();
$store_letters = $rewriteUrlObj->get_letters_url();
$tpl->assign('store_letters', $store_letters);

$page_header = array(
	'meta' => $meta,
	'css' => $default_css, 
	'js' => $default_js,
);

$tpl->assign('page_header', $page_header);


$tpl->display('de_404.html');
?>