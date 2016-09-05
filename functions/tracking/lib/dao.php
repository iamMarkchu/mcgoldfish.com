<?php
//incoming
function set_incomings() {
    global $T_SESSION, $T_TRAFFICTYPE, $T_CLIENT_ID, $T_SOURCE, $T_TPLID, $T_SEO;

    if ($T_TRAFFICTYPE < 0) 
        return false;

    $T_SOURCE = get_source();
    $kwd = get_refkwd();
    if (preg_match('/^([^_]+)_/', $kwd, $m))
        $T_SEO = $m[1];            

    $sql = "INSERT INTO incominglog (VisitTime, IP, HttpReferer, HttpUserAgent, RequestUri, TrafficType, RetentionUserID, Source, SourceGroup, RefKeyword, CurrServerID, ServerID, COUNTRY) VALUES ('". date('Y-m-d H:i:s') ."','".addslashes(get_ip())."','".addslashes(get_referrer())."','".addslashes(get_useragent())."','".addslashes(get_request())."','".addslashes($T_TRAFFICTYPE)."','".addslashes($T_CLIENT_ID)."','".addslashes($T_SOURCE)."','".addslashes(get_source_group())."','".addslashes($kwd)."','".addslashes(get_current_server_id())."','".addslashes(get_current_server_id())."','".$T_TPLID."')";

    return send_query($sql); 
}

//pagevisit
function set_pagevisits($page_type='', $page_value='') {
    global $T_SESSION, $T_TRAFFICTYPE, $T_CLIENT_ID, $T_SERVERID, $T_USERID, $T_TPLID;    

    if ($T_SESSION == '' || $T_TRAFFICTYPE < 0) 
        return false;

    $page_type  = addslashes($page_type);
    $page_value = addslashes($page_value);
    $sql = "insert into pagevisitlog (SessionID, Referer, RequestUri, VisitTime,CurrServerID, PAGETYPE, PAGEVALUE, ServerID, tplid)";
    $sql .= " values(" . intval($T_SESSION) . ", '" . addslashes(get_referrer()) . "', '" . addslashes(get_request()) . "', '" . date('Y-m-d H:i:s') . "','" . addslashes(get_current_server_id()) . "','{$page_type}', '{$page_value}', '" . addslashes($T_SERVERID) ."','".$T_TPLID."')";

     return send_query($sql);
}
