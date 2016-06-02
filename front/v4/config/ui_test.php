<?php
/*
 * User Case
 * 
 * google => A,B 
 * $UI_TEST['google_'] = '0,1';
 * 
 * google => A,B,C
 * $UI_TEST['google_'] = '0,2';
 *
 * msn_002 => A,B
 * $UI_TEST['msn_002'] = '0,1';
 *
 */

$UI_TEST['google_'] = '0,1';


function check_uitest() {
    global $UI_TEST;
    $keys = array_keys($UI_TEST);        
    foreach ($UI_TEST as $k => $v) {
        $n = explode(',', $v);
        if (!isset($n[1]) || $n[0] > $n[1]) {
            echo "Error format UI TESTING case!\n";
            exit(1);        
        }

        foreach ($keys as $v2) {
            if ($v2 != $k && stripos($v2, $k) !== false) {    
                echo "Duplicated UI TESTING case!\n";
                exit(1);
            }
        }
    }
}

check_uitest();

