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


//outgoing
function set_outgoings($appid=0, $merid=0, $page_type='', $page_value='', $clk_area='', $url='', $affid=0, $bz_type='') {
    global $T_SESSION, $T_TRAFFICTYPE, $T_CLIENT_ID, $T_SERVERID, $T_USERID;    
    if ($T_SESSION == '' || $T_TRAFFICTYPE < 0) 
        return false;
    $page_type  = addslashes($page_type);
    $page_value = addslashes($page_value);    
    $clk_area   = addslashes($clk_area);        
    $url        = addslashes($url);
    $affid      = addslashes($affid);
    $appid      = addslashes($appid);
    if (empty($merid)) {
    	$merid ='0';
    }
    $merid      = addslashes($merid);    
    $bz_type    = addslashes($bz_type);    

    $sql = "insert into outgoinglog (SessionID, VisitTime, ServerID, CurrServerID, ClickArea, PageType, PageValue, DestUrl, HttpReferer, AFFILIATEID, COUPONID, MERCHANTID, BZ_TYPE)";
    $sql .= " values(" . intval($T_SESSION) . ", '" . date('Y-m-d H:i:s') . "', '" . addslashes($T_SERVERID) ."','". addslashes(get_current_server_id()) . "','{$clk_area}','{$page_type}','{$page_value}','{$url}', '".addslashes(get_referrer())."', '{$affid}', '{$appid}','{$merid}','{$bz_type}')";

    return send_query($sql);
}


//outbound
function setOutboundLog($url='', $oid=0, $aff=0, $active=0, $remark='', $affsid='', $megasid='') {

    global $T_SESSION, $T_TRAFFICTYPE;
    if ($T_SESSION == '' || $T_TRAFFICTYPE < 0) 
        return false;

    $sql = "insert into outbounds (ID, VisitTIme, DESTURL, HASAFF, ISACTIVE, REMARK, AFF_SID, MEGA_SID, CurrServerID)";
    $sql .= " values(" . intval($oid) . ", '" . date('Y-m-d H:i:s') . "', '". addslashes($url) . "', '" . addslashes($aff) . "', '" . addslashes($active) . "','". addslashes($remark) . "','".addslashes($affsid)."','".addslashes($megasid)."','". get_current_server_id() ."')";

    return send_query($sql);    
}



//search
function set_searchs($kw, $cost_time, $se_type, $page, $results, $is_cache, $is_suggest=0) {
    global $T_SESSION, $T_TRAFFICTYPE, $T_SOURCE, $T_SERVERID;    
    if ($T_SESSION == '' || $T_TRAFFICTYPE < 0) 
        return false;
        
    $is_mkt = $T_SOURCE ? 'YES' : 'NO';
    $page      = intval($page);
    $results   = intval($results);
    $is_cache  = intval($is_cache) ? 'YES' : 'NO';
    $cost_time = floor($cost_time);
    $sql = "insert into searchlog (SessionID, VisitTime, Keyword, IsMktSearch, CostTime, TrafficType, SearchType, PageNum, TotalResNum, isCache,ServerID,CurrServerID)";
    $sql .= " values(" . intval($T_SESSION) . ", '" . date('Y-m-d H:i:s') . "', '" . addslashes($kw) . "', '{$is_mkt}', $cost_time, " . intval($T_TRAFFICTYPE) . ", '".addslashes($se_type.'-'.$is_suggest) . "', {$page}, {$results}, '{$is_cache}','" . $T_SERVERID . "','" . addslashes(get_current_server_id()) . "')";
    return send_query($sql);
}


//breakpoints
function set_breakpoints($width=0,$height=0,$tag='') {
    global $T_SESSION, $T_TRAFFICTYPE,  $T_SERVERID;    

    if ($T_SESSION == '' || $T_TRAFFICTYPE < 0) 
        return false;

    $width  = addslashes($width);
    $height = addslashes($height);
    $tag    = addslashes($tag);
    $sql = "insert into breakpointlog (SessionID, VisitTime, CURRServerID, WIDTH, ServerID, HEIGHT, TAG)";
    $sql .= " values(" . intval($T_SESSION) . ",'" . date('Y-m-d H:i:s') . "','" . addslashes(get_current_server_id()) . "','{$width}','" . addslashes($T_SERVERID) ."','{$height}','{$tag}')";

     return send_query($sql);
}

//impression
function set_impressions($coupnid=0, $merid=0, $page_type='', $page_value='', $pvid=0, $block_rank=''){
    return set_batch_impression(array(0 => array($couponid, $merid, $page_type, $page_value, $pvid, $block_rank)));    
}


//batch impression
function set_batch_impressions($arr = array()){
    if (count($arr) == 0 )
        return false;

    global $T_SESSION, $T_TRAFFICTYPE, $T_SERVERID;    
    if ($T_SESSION == '' || $T_TRAFFICTYPE < 0) 
        return false;   

    $inserts = array();
    foreach ($arr as $v ) {
        array_push($inserts,  intval($T_SESSION) ."\t". date('Y-m-d H:i:s') ."\t". $v[0] ."\t". $v[1] ."\t". $v[2] ."\t". $v[3] ."\t". $v[4] ."\t". $T_SERVERID ."\t". get_current_server_id() ."\t". $v[5]);
    }
    return error_log(implode("\n", $inserts)."\n", TRACKING_LOG_TYPE, TRACKING_IMPR_LOG);            
}



//related impression
function set_related_impr($block_area='', $itemid=0, $page_type='', $page_value='', $pvid=0, $block_rank=''){
    return set_batch_related_impr(array(0 => array($block_area, $itemid, $page_type, $page_value, $pvid, $block_rank)));    
}

//batch related impression
function set_batch_related_impr($arr = array()){
    if (count($arr) == 0 )
        return false;

    global $T_SESSION, $T_TRAFFICTYPE, $T_SERVERID;    
    if ($T_SESSION == '' || $T_TRAFFICTYPE < 0) 
        return false;   

    $inserts = array();
    foreach ($arr as $v ) {
        array_push($inserts,  intval($T_SESSION) ."\t". date('Y-m-d H:i:s') ."\t". $v[0] ."\t". $v[1] ."\t". $v[2] ."\t". $v[3] ."\t". $v[4] ."\t". $T_SERVERID ."\t". get_current_server_id() ."\t". $v[5]);
    }
    return error_log(implode("\n", $inserts)."\n", TRACKING_LOG_TYPE, TRACKING_IMPR_RELATED_LOG);            
}
