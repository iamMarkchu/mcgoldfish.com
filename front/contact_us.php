<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}

/*
* page type
*/
define("D_PAGE_NAME",	"CONTACTUS");
define("D_PAGE_VALUE",	"/countact-us/");
include_once 'tracking_block.php';
/*
* block
*/
$objTopic = new Topic();
$list_data = $objTopic->get_home_page_data(9);
$tpl->assign('featured_stores', $list_data['featuredstores']);

$limit=3*6;
$objTerm = new Term();

$recent_launched_data = $objTerm->get_home_page_new_term($limit);
$tpl->assign('recent_launched_data', $recent_launched_data);


$objCategory = new Category();
$category_data = $objCategory->get_top_category();
$tpl->assign('category_data', $category_data);

	
/*
 * meta
 */
$meta = array();
$meta["MetaTitle"] = "Kontakt und Impressum - " . TOP_LEVEL_DOMAIN_NAME;
$meta['MetaDesc'] = SITE_FULL_NAME . " Alle neuen Gutscheine und Sonderaktionen findest du auf Saving Story. Günstig online shoppen war noch nie so leicht. Jetzt Gutscheincode holen und sparen.";
$meta['MetaKeyword'] = 'Gutscheine 2015, Aktuelle Rabatte und Sonderaktionen';

/*
 * paths
 */
$paths = array();
$paths[] = array("name"=>"Home", "url"=>"/");
$paths[] = array("name"=>"Kontakt und Impressum", "url"=>"");
$tpl->assign("paths", $paths);

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
$tpl->assign('_page_lang', 'en');

/*
* display
*/
$tpl->display("de_contact_us.html");
?>