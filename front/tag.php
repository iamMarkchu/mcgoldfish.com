<?php
defined('IN_DS') or die('Hacking attempt');
define('D_PAGE_NAME', 'Tag');
$canonical_uri = $_rewriteUrlInfo['RequestPath'];
define("D_PAGE_VALUE",	$canonical_uri);

include_once 'tracking_block.php';

$cacheFileName = MEM_PREX . 'category_'.$_rewriteUrlInfo['OptDataId'];
$objCache = new Cache($cacheFileName);
$objCache->getCacheTime();
if (DEBUG_MODE || isset($_GET['forcerefresh']))    $mainContent = '';
   else    $mainContent = $objCache->getCache();
if (!$mainContent) {
	$objCache->initialCache();
    $tpl->display('tag.html');
	$mainContent = $objCache->endCache();
}
echo $mainContent;
