<?php
//function
define('TRACKING_FUNC_DAO', 'dao.php');
define('TRACKING_FUNC_DB', 'db.pdo.php');
define('TRACKING_FUNC_BASE', 'base.php');


//cooike
define('COOKIE_SEPERATOR', '|');
define('TRACKING_COOKIE', '_trk');
define('RETENTION_COOKIE', '_reu');
define('GA_COOKIE', '_gasrc');

//log
define('TRACKING_LOG_TYPE', 3);


//black list
define('TRACKING_FORBIT_IPS', TRACKING_PATH.'/forbit/t_ip.txt');
define('TRACKING_FORBIT_UAS', TRACKING_PATH.'/forbit/t_robots.txt');



//if (!preg_match('/(.*)(\.[^\.]+(\.[^\.]+|\.[^\.]+\.[^\.]+))$/U', $_SERVER['SERVER_NAME'], $m))
//    die("No server name!");


//include site config
define('DOMAIN_ENV', strtolower($m[1]));
require_once 'define_'.strtolower(str_replace('.','',(defined('SITE_DOMAIN')? SITE_DOMAIN : $m[2]))).'.php';










