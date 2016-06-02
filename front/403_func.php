<?php
global $g_homepage_feature_merchant,$tpl,$default_css,$default_js;

define('D_PAGE_NAME', '403');

/*
 * meta
 */
$meta = array();
$meta['MetaTitle'] = '403 - '.TOP_LEVEL_DOMAIN_NAME;
$meta['MetaDesc'] = '';
$meta['MetaKeyword'] = '';

$page_header = array(
	'meta' => $meta,
	'css' => $default_css, 
	'js' => $default_js,
);
$tpl->assign('useip',$clientip);
$tpl->assign('url',$_SERVER['REQUEST_URI']);
$tpl->assign('page_header', $page_header);

$tpl->display('de_403.html');
?>