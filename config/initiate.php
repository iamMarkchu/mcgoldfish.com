<?php
include_once(dirname(__FILE__) . '/const.php');
include_once(INCLUDE_ROOT . 'functions/front.func.php');

$db = new MysqlEx(DB_NAME_SLAVE, DB_HOST_SLAVE, DB_USER_SLAVE, DB_PASS_SLAVE);

$db_master = new MysqlEx(DB_NAME_MASTER, DB_HOST_MASTER, DB_USER_MASTER, DB_PASS_MASTER);

$tpl = new TemplateSmarty();
$tpl->registerPlugin("function","format_date_V2","format_date_V2");
$tpl->registerPlugin("function","rand_one_img","rand_one_img");
$default_js = array(
	'header' => array(),
	'footer' => array('http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js',
					 ),
);

$default_css = [
				'/css/normalize.css',
				'/css/main_v2.css?ver'.VER,
				'/css/font-awesome/css/font-awesome.min.css',
				];

$img = ['0954501O2-11.jpg','0954503C7-13.jpg','0954504293-12.jpg','0954505F5-6.jpg','095450F07-9.jpg','0954503595-8.jpg','0954503U4-18.jpg','0954505051-0.jpg','0954505I5-7.jpg','095450I18-2.jpg','0954503602-14.jpg','0954504054-19.jpg','09545050c-4.jpg','09545063H-3.jpg'];
$navList = [
    			'/code.html' => [
                      'displayname' => '代码',
                      'bindCategoryList' => [10304,10300,10299,10298],
                      'description' => '写代码是一件快乐的事情，因为写代码的时候什么都不会去想，烦恼自然也丢到脑后去了，非常的开心'
                    ],
    			'/work.html' =>	[
    				  'displayname' => '工作',
                      'bindCategoryList' => [],
                      'description' => ''
    				],
    			'/game.html' =>	[
    				  'displayname' => '游戏',
                      'bindCategoryList' => [10302],
                      'description' => ''
    				],
    			'/movie.html' =>	[
    				  'displayname' => '电影',
                      'bindCategoryList' => [],
                      'description' => ''
    				],
    			'/music.html' =>	[
    				  'displayname' => '音乐',
                      'bindCategoryList' => [],
                      'description' => ''
    				],
    			   ];
$default_lang = 'zh-CN';
$site_url = 'http://mcgoldfish.com';
$site_url_normal = 'Mcgoldfish.com';
$site_url_short = 'Mcgoldfish';

$year = Date("Y");
