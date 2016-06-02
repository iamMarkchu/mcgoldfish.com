<?php
$env = 'bendi';
switch (DOMAIN_ENV) {
        case 'www':
                //db
                define('TRACKING_DB_NAME', 'www_savingstory_tracking');
                define('TRACKING_DB_HOST', 'localhost');//db01
                define('TRACKING_DB_PORT', '3306');
                define("TRACKING_DB_USER", "hdsite");
                define("TRACKING_DB_PASS", "ytf4shyAppa");
                define('TRACKING_DEBUG', false);
                break;
    default:
                define("TRACKING_DB_HOST", "localhost");
                define('TRACKING_DB_PORT', '3306');
                define("TRACKING_DB_USER", "root");
                define("TRACKING_DB_PASS", "chukui");
                define("TRACKING_DB_NAME", "chukui_tracking");
                define('TRACKING_DEBUG', true);
        //die('No db config');
        break;
}
?>
