<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}


define('D_PAGE_NAME', 'SEARCH');
define("D_PAGE_VALUE",	$script_uri);  //暂时放当前链接

include_once 'tracking_block.php';

$patt = "/\/search\/(.*)/";
preg_match($patt,$script_uri,$res);
$keyword = trim($res[1],"/");
if(empty($keyword)){
	$keyword = $_REQUEST['keyword'];
}else{
    $keyword=urldecode(urldecode($keyword));
}
$keyword = trim(str_replace("+"," ",$keyword));

$term_obj = new Term();

$r =$term_obj->get_term_by_name($keyword);

if($r){
	permanent_header($r);
}else{
	include_once FRONT_DIR . '404_func.php';
}

