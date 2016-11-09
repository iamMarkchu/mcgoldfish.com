<?php 
define('D_PAGE_NAME', 'MESSAGE');
define("D_PAGE_VALUE",	'/message.html');
include_once INCLUDE_ROOT."functions/tracking/index.php";

$sql = "SELECT * FROM `comment` WHERE `optdataid` = 0 ORDER BY `addtime` desc";
$result = $GLOBALS['db']->getRows($sql);
$tpl->assign('messageList', $result);



$tpl->assign('imgList', $img);
$page_header = array(
	'meta' => $meta,
	'css' => array_merge($default_css, ['/css/main_v2_blog.css',]),
	'js' => $default_js,
);
$tpl->assign('page_header',$page_header);

$tpl->template_dir = INCLUDE_ROOT. "view_v2";
$tpl->display('message.html');