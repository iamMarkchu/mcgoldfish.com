<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}

define('D_PAGE_NAME', 'CATAGORY');
$canonical_uri = $_rewriteUrlInfo['RequestPath'];
define("D_PAGE_VALUE",	$canonical_uri);

include_once 'tracking_block.php';

// set cache
$cacheFileName = MEM_PREX . 'category_'.$_rewriteUrlInfo['OptDataId'];
$objCache = new Cache($cacheFileName);
$objCache->getCacheTime();
if (DEBUG_MODE || isset($_GET['forcerefresh'])) //refresh the cache by force
    $mainContent = '';
else
    $mainContent = $objCache->getCache();
if (!$mainContent) {
	$objCache->initialCache();
    $tpl->display('category.html');
	$mainContent = $objCache->endCache();
}
echo $mainContent;
