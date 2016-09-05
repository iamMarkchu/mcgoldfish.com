<?php
require_once 'init.php';

try {
    global $T_SESSION, $T_WWW, $T_LANDING;
    init_tracking();
    $T_LANDING = false;

    if (!($T_SESSION > 0)) {
        $T_SESSION = set_incomings();
        $T_WWW = 0;            
        $T_LANDING = true;  
        set_tracking_cookies();        
    }
    
//  set_pagevisits(defined('PAGE_TYPE')? PAGE_TYPE:'', defined('PAGE_VALUE')? PAGE_VALUE:'');
    $url = '';
    $src = get_source();
    if ($src !== '') {            
        global $DIVIDER_SEM;    
        foreach ($DIVIDER_SEM as $s => $v) {
            if (stripos($src, $s) !== false) {
                $url = $v;
                break;
            }
        }    

        if ($url == '') {
            $url = preg_replace('/[\?&]*mktsrc=([^&]+)/', '', get_request());
            if ($src == 'vcs') {
                $T_WWW = 1;
                set_tracking_cookies();
            }
        }
    }


    if (preg_match('/(.*)[\?&]*ca=[^&]+(.*)/', ($url != ''? $url : get_request()), $m) && stripos($m[1], '/redirect-') === false && stripos($m[1], '/tracking/rd.php') === false) {
        $url = $m[1].$m[2];        
    }

    if ($url != '') {
        $url = rtrim($url, '?');
        $url = rtrim($url, '&');        
        redirect($url, '301'); 
        exit;   
    }
}
catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}


