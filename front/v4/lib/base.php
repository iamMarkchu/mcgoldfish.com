<?php

function create_clientid($length = 32, $prefix = ''){
    return substr($prefix . sha1(uniqid(mt_rand() . php_uname('a') . microtime(true), true)), 0, $length);
}


function get_clientid() {
    if (isset($_COOKIE[RETENTION_COOKIE]) && $_COOKIE[RETENTION_COOKIE] != '')
        return $_COOKIE[RETENTION_COOKIE];

    $id = create_clientid();
    setcookie(RETENTION_COOKIE, $id, time()+3600*24*365, '/');
    return $id;
}

function init_tracking() {
    global $T_BLACK_IPS, $T_BLACK_UAS, $T_TRAFFICTYPE;
    
    $T_BLACK_IPS = load_files(TRACKING_FORBIT_IPS, 'KEY');
    $T_BLACK_UAS = load_files(TRACKING_FORBIT_UAS, 'KEY');

    get_tracking_cookies();

    if (!isset($T_TRAFFICTYPE) || $T_TRAFFICTYPE == '')    
        $T_TRAFFICTYPE = get_traffic_type();
}

function load_files($file, $k_v='') {
    if (!file_exists($file)) 
        return false;            
    
    $arr = array();
    $fp = fopen($file, 'r');
    while (!feof($fp)) {
        $lr = trim(fgets($fp));
        if ($lr == '' || stripos($lr, '#') === 0) 
            continue;
    
        if ($k_v == 'KEY') 
            $arr[$lr] = $lr;
        else
            array_push($arr, $lr);
    }
    fclose($fp);

    return $arr;
}

function get_clickarea($ca) {
    if ($ca == '')
        return $ca;         

    list($col, $id, $area) = explode(COOKIE_SEPERATOR, $ca);
    
    $row_no = intval($id/$col);
    $row_no++;
    $col_no = $id % $col;
    $col_no++;

    return ($row_no.'_'.$col_no.'_'.$area);
}

function get_target_queries($td) {
    if ($td == '')
        return $td;             

    $arr = explode(COOKIE_SEPERATOR, $td); 
    $rs  = array();
    $total = count($arr);
    for ($i = 0; $i<$total; $i++) {
        $rs[$arr[$i]] = $arr[++$i];
    }

    return $rs;
}


function get_tracking_cookies() {
    if (!isset($_COOKIE[TRACKING_COOKIE]))
        return false;

    global $T_SESSION, $T_SERVERID, $T_TRAFFICTYPE, $T_SOURCE, $T_TPLID, $T_WWW, $T_SEO, $T_LANDING;
    list($T_SESSION, $T_SERVERID, $T_TRAFFICTYPE, $T_SOURCE, $T_TPLID, $T_WWW, $T_SEO, $T_LANDING) = explode(COOKIE_SEPERATOR, base64_decode($_COOKIE[TRACKING_COOKIE]));
    return true;
}

function get_useragent() {
    return isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT'] : '';
}

function get_referrer() {
    return isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '';
}

function get_request() {
    return isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI'] : '';
}

function get_ip() {
    return isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"]? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER['REMOTE_ADDR'];    
}


function get_source() {
    if (preg_match('/mktsrc=([^&]+)/', get_request(), $m)) {
        return $m[1];
    }
    else {
        return '';
    }    
}


function get_source_group() {
    if (preg_match('/mktsrc=([^_]+)/', get_request(), $m)) {
        return $m[1];
    }
    else {
        return '';
    }
}


function get_traffic_type() {
    global $T_TRAFFICTYPE;

        $T_TRAFFICTYPE = 0;
        
        if (TRACKING_DEBUG)
            return $T_TRAFFICTYPE;

        $ua = get_useragent();
        $ip = get_ip();
        if (stripos($ua, 'googlebot') !== false) {
            $T_TRAFFICTYPE = -1; //google bot
        }
        elseif (stripos($ua, 'msnbot') !== false) {
            $T_TRAFFICTYPE = -2; //msn bot
        }
        elseif (stripos($ua, 'Yahoo! Slurp;') !== false) {
            $T_TRAFFICTYPE = -3; //yahoo bot
        }
        elseif(stripos($ua, 'Baiduspider') !== false) {
            $T_TRAFFICTYPE = -4; //baidu bot
        }
        elseif(trim($ua) == '') {
            $T_TRAFFICTYPE = -8; //empty useragent
        }
        elseif(check_fraud_ip($ip) == true) {
            $T_TRAFFICTYPE = -6; //black IP        
        }
        elseif(check_robot_ua($ua) == true) {
            $T_TRAFFICTYPE = -5; //other robots        
        }
//        $T_TRAFFICTYPE = 0;
//        var_dump($ip, $ua, $T_TRAFFICTYPE);
        return $T_TRAFFICTYPE;
}


function get_current_server_id() {
 
    $server_name = php_uname("n");
    list($server_id) = explode(".", $server_name);
    if (preg_match("/(web|admin|backup)([0-9]+)/", $server_id, $matches)) {
        $g = $matches[1];
        $s = $matches[2];
        if ($g == "web")
            $server_id = intval($s);
        elseif ($g == "admin")
            $server_id = intval($s) + 100;
        elseif ($g == "backup")
            $server_id = intval($s) + 200;
     }
     return $server_id;
}


function get_refkwd() {
    $kwd = "";
    $referer = trim(get_referrer());
    if (!$referer)
        return $kwd;

    $seos = array('google' => array('google.', '/([&?]+)q=([^&]*)/i'),
                        'yahoo' => array('yahoo.', '/([&?]+)p=([^&]*)/i'),
                        'msn' => array('msn.', '/([&?]+)q=([^&]*)/i'),
                        'bing' => array('bing.', '/([&?]+)q=([^&]*)/i'),
                        'ask' => array('.ask.', '/([&?]+)q=([^&]*)/i'),
                        'aol' => array('.aol.', '/([&?]+)q=([^&]*)/i'),
                        'comcast' => array('.comcast.', '/([&?]+)q=([^&]*)/i'),
                        'live' => array('live.', '/([&?]+)q=([^&]*)/i')
                    );
    foreach ($seos as $k => $v) {
        if (stripos($referer, $v[0]) !== false) {
            if (preg_match($v[1], $referer, $m)) {
                $kwd = $k . "_" . urldecode(trim($m[2]));
            }
            break;
        }
    }
    return $kwd;
}

function check_private_ip($ip) {
    if (ip2long($ip) <> false && ip2long($ip) <> -1) {
        $arrIpSeg = explode(".", $ip);
        if (strcmp($ip, '127.0.0.1') == 0) {
            return true;
        } 
        elseif (strcmp($arrIpSeg[0], "10") == 0) {
            return true;
        } 
        elseif (strcmp($arrIpSeg[0], "192") == 0 && strcmp($arrIpSeg[1], "168") == 0) {
            return true;
        } 
        elseif (strcmp($arrIpSeg[0], "172") == 0 && intval($arrIpSeg[1]) >= 16 && intval($arrIpSeg[1]) <= 31) {
            return true;
        } 
        else {
            return false;
        }
    }
    return false;
}

function check_fraud_ip($ip) {
    if ($ip == '')
        return false;
    
    global $T_BLACK_IPS;
    if (isset($T_BLACK_IPS[$ip]))
        return true;
    else
        return false;
}


function check_robot_ua($ua) {
    if ($ua == '')
        return true;
    global $T_BLACK_UAS;

    foreach ($T_BLACK_UAS as $v) {
        switch(substr($v, 0, 1)) {
            case '~':
                if (strcasecmp($ua, substr($v, 1)) === 0)
                    return true;                           
                break;
            case '-':
                $v = substr($v, 1);    
            default:
                if (stripos($ua, $v) !== false) {
                    return true;
                }
                break;
	    }
    }
    return false;
}



function set_tracking_cookies() {
    global $T_SESSION, $T_TRAFFICTYPE, $T_SOURCE, $T_TPLID,$T_WWW,$T_SEO,$T_LANDING;
    setcookie(TRACKING_COOKIE, base64_encode(implode(COOKIE_SEPERATOR, array($T_SESSION, get_current_server_id(), $T_TRAFFICTYPE, $T_SOURCE, $T_TPLID, $T_WWW, $T_SEO,$T_LANDING))), 0, '/');

    $_ref = trim(get_referrer());
    //GA Tracking Source Dimenssion
    if (preg_match('/^(google|msn|bing|couponalert|newsletter|seasonalert)_/', $T_SOURCE, $m))
        $_ga_src = $m[1] == 'couponalert' || $m[1] == 'seasonalert' || $m[1] == 'newsletter'? 'EDM' : 'SEM';
    elseif ($_ref != '' && stripos($_ref, 'uk.promopro.com') === false)
        $_ga_src = 'SEO';
    else
        $_ga_src = 'RETENTION';

    setcookie(GA_COOKIE, $_ga_src, 0, '/');
}


function redirect($url, $code) {        
    if ($url == '')
        return false;

    switch ($code) {
        case '301':
           	header("HTTP/1.1 301 Moved Permanently");
            break;
        case '302':
           	header("HTTP/1.1 302 Found");
            break;   
        default:
            header("HTTP/1.1 200 OK");
            break;    
    }

    header("Cache-Control: no-cache");
    header("Location: $url");   
    return false;
}


function is_landing() {
    $k = TRACKING_COOKIE.'_lp';    
    
    if (!isset($_COOKIE[$k]) || $_COOKIE[$k] == 0) {
        setcookie($k, 1, 0, '/');
        return true;                   
    }
    else
        return false;
        
}

function has_outgoing() {
    $k = TRACKING_COOKIE.'_og';    
    
    if (isset($_COOKIE[$k]) && $_COOKIE[$k] > 0) {
        return true;          
    }
    else
        return false;
        
}


