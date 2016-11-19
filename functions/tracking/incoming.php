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
}
catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}


