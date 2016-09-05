<?php
define('TRACKING_PATH', dirname(__FILE__));
set_include_path(get_include_path() . PATH_SEPARATOR . TRACKING_PATH.'/lib'. PATH_SEPARATOR . TRACKING_PATH.'/black'. PATH_SEPARATOR . TRACKING_PATH.'/config'.PATH_SEPARATOR . dirname(TRACKING_PATH));

require_once 'define.php';
require_once TRACKING_FUNC_DB;
require_once TRACKING_FUNC_DAO;
require_once TRACKING_FUNC_BASE;

//get retention userid
$T_CLIENT_ID = get_clientid();
$CONN = open_connect();
if (!$CONN)
        throw new Exception ("Connect DB error");

$uri = get_request();
if ($uri == '')
    exit(1);       

