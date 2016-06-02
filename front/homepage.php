<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}
define('D_PAGE_NAME', 'HOMEPAGE');
define("D_PAGE_VALUE",	'/');
include_once 'tracking_block.php';
$cacheFileName = MEM_PREX . 'index';
$objCache = new Cache($cacheFileName);
if (DEBUG_MODE || isset($_GET['forcerefresh'])) //refresh the cache by force
    $mainContent = '';
else
    $mainContent = $objCache->getCache();
if (!$mainContent) {
	$objCache->initialCache();
	$tpl->display("index.html");
	$mainContent = $objCache->endCache();
}
echo $mainContent;
