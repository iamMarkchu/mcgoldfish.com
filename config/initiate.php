<?php
include_once(dirname(__FILE__) . '/const.php');
include_once(INCLUDE_ROOT . 'functions/front.func.php');

$db = new MysqlEx(DB_NAME_SLAVE, DB_HOST_SLAVE, DB_USER_SLAVE, DB_PASS_SLAVE);
$db_master = new MysqlEx(DB_NAME_MASTER, DB_HOST_MASTER, DB_USER_MASTER, DB_PASS_MASTER);
$tpl = new TemplateSmarty();
$tpl->registerPlugin("function","format_date_V2","format_date_V2");
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

$year = Date("Y");
