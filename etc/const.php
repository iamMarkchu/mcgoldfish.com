<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}
//config
define('VER', '2016041501');
define('MEM_LIFT_TIME', 3600 * 24 * 30);
define('MEM_PREX', 'chukui520_');

define('DEBUG_MODE', false);

define('SITE_DOMAIN', '.chukui520.com');
include_once dirname(__FILE__) . '/db_www.php';
define('INCLUDE_ROOT', dirname(dirname(__FILE__)).'/');

define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('SITE_URL', 'http://' . HTTP_HOST);
define('FRONT_DIR', INCLUDE_ROOT . 'front/');
define('TOP_LEVEL_DOMAIN_NAME', 'Chukui520.com');
define('TOP_HTTP_LEVEL_DOMAIN_NAME', 'http://www.chukui520.com');

define('CSS_SUB_PATH', '/css/mini/');
define('JS_SUB_PATH', '/js/mini/');
define('IMG_ROOT_PATH', 'http://ss.mgcdn.com/image');
define('DECDN_ROOT_PATH', '/public');

define('ERROR_LOG_FILE', 'userError.log');
define('MAIL_SEND_ERROR_LOG_FILE', 'userMailError.log');

define('TRACKING_ROBOTS_FILE_PATH', INCLUDE_ROOT . 'etc/t_robots.txt');
define('TRACKING_IGNOREDIP_FILE_PATH', INCLUDE_ROOT . 'etc/t_ip.txt');

define("SITE_NAME", 'chukui');
define('SITE_FULL_NAME', 'ChuKui520.com');
define('SITE_CONTACT_EMAIL', 'support@chukui520.com');
define("SID_PREFIX", 's01');
define('DATA_ROOT', INCLUDE_ROOT . 'data/log/');
define('LOG_LOCATION', INCLUDE_ROOT . 'data/log/');
define('MEM_CACHE_LOG', INCLUDE_ROOT . 'data/');
define('TIME_ZONE', 'Asia/Shanghai');
date_default_timezone_set(TIME_ZONE);

define('CACHE_EXP_TIME', 3600 * 24 * 3);
define("CACHE_FUNC_DEBUG_MODE", true);

define("EMIAL_HOST", "");
define("SMTP_HOST", "");
define("SMTP_PORT", "25");
define("SMTP_USER", "");
define("SMTP_PASS", "");

function __autoload($class)
{
	$class_file = INCLUDE_ROOT . 'lib/Class.' . $class . '.php';
	if(file_exists($class_file)) return include_once($class_file);
}
