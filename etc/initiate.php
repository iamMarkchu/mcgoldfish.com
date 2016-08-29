<?php
include_once(dirname(__FILE__) . '/const.php');
include_once(INCLUDE_ROOT . 'func/front.func.php');
include_once(INCLUDE_ROOT . 'func/string.func.php');

$db = new MysqlEx(DB_NAME_SLAVE, DB_HOST_SLAVE, DB_USER_SLAVE, DB_PASS_SLAVE);
$db_master = new MysqlEx(DB_NAME_MASTER, DB_HOST_MASTER, DB_USER_MASTER, DB_PASS_MASTER);
$tpl = new TemplateSmarty();

$default_js = array(
	'header' => array(),
	'footer' => array('http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js',
					  'http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js',
					  '/origin/js/offcanvas.js?'.VER,
					 )
);
$default_css = array('http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css','/origin/css/offcanvas.css?'. VER);
$default_lang = 'zh-CN';
$site_url = 'http://mcgoldfish.com';
$site_url_normal = 'Mcgoldfish.com';
$site_url_short = 'Mcgoldfish';
$year = date('Y');
$month = date('F');
$next3days_timestamp = strtotime("+4 day");
$next3days_month = date("F",$next3days_timestamp);
$next3days_year = date('Y',$next3days_timestamp);
$year = $next3days_year;
$month = $next3days_month;

define("_YEAR_", $year);
define("_MONTH_", $month);
define("_DAY_", date('d'));

$global_word_tab = array(
	'new_site_url'=>$new_site_url,
	'year'=>$year,
	'month'=>$month,
);
foreach($global_word_tab as $k=>$v){
	$tpl->assign($k, $v);
}

if ($next3days_month == 'January' && $next3days_month != date('F')) {
	$next3days_year += 1;
}
$global_word_tab['month'] = $next3days_month;
