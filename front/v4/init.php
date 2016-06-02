<?php
define('TRACKING_PATH', dirname(__FILE__));
set_include_path(get_include_path() . PATH_SEPARATOR . TRACKING_PATH.'/lib'. PATH_SEPARATOR . TRACKING_PATH.'/black'. PATH_SEPARATOR . TRACKING_PATH.'/config'.PATH_SEPARATOR . dirname(TRACKING_PATH));

require_once 'define.php';
require_once 'ss_func.php';
require_once TRACKING_FUNC_DB;
require_once TRACKING_FUNC_DAO;
require_once TRACKING_FUNC_BASE;

define('TRACKING_IMPR_LOG', TRACKING_PATH_LOG.'/'.'impression_'.TRACKING_SITEID.'_'.get_current_server_id().'.'.date('Ymd'));
define('TRACKING_IMPR_RELATED_LOG', TRACKING_PATH_LOG.'/'.'relatedimpr_'.TRACKING_SITEID.'_'.get_current_server_id().'.'.date('Ymd'));

//date_default_timezone_set('America/Los_Angeles');
$arr = get_login(true, true);
$T_USERID = isset($arr['ID'])? $arr['ID'] : 0;

//get retention userid
$T_CLIENT_ID = get_clientid();

$CONN = open_connect();
if (!$CONN)
        throw new Exception ("Connect DB error");

$uri = get_request();
if ($uri == '')
    exit(1);       

