<?php
require_once 'init.php';
require_once TRACKING_CONFIG_UI;
require_once TRACKING_FUNC_MOBILE;

try {
    global $T_WWW;
    //mobile traffic will redirect to m site
	$detect = new Mobile_Detect;
	$device = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
    if($device == 'phone' && defined('MOBILE_URL') && MOBILE_URL != '' && !$T_WWW) {
        redirect(MOBILE_URL, 302);
        exit;
    }
        
    global $T_SESSION, $T_TPLID;    
    init_tracking();
       
    $T_TPLID = get_tplid();

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


