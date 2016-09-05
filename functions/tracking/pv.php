<?php
require_once 'init.php';
try {
    global $T_WWW;

    global $T_SESSION;
    init_tracking();
       
    if (!($T_SESSION > 0)) {
        $T_SESSION = set_incomings();
        set_tracking_cookies();
    }
    set_pagevisits(defined('D_PAGE_NAME')? D_PAGE_NAME:'', defined('D_PAGE_VALUE')? D_PAGE_VALUE:'');

}
catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}


