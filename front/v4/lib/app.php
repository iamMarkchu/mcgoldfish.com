<?php

function get_aff_link($appid='', $country_code='', $jailbreak=false){ 
    if ($appid == '')
       return false;

    global $CONN_FE;

    $appid = addslashes($appid);
    $sql = "SELECT AFFID, AFFURL FROM app_aff_link WHERE APPID = '{$appid}'";
    if($country_code != '') {
        $sql .= " AND COUNTRY_CODE = '".addslashes($country_code)."'";
    }

    if($jailbreak) {
        $sql .= " AND ISJAILBREAK = 1 ";
    }
    else {
        $sql .= " AND ISJAILBREAK = 0 ";    
    }
    $sql .= "ORDER BY ISPRIMARY DESC LIMIT 1";
    $sth = mysql_query($sql, $CONN_FE);
    if (!$sth)
       throw new Exception (mysql_error($CONN_FE), mysql_errno($CONN_FE));
    
    $row = mysql_fetch_assoc($sth);
	return $row;
}


function get_aff_template($affid) {
    global $CONN_FE;

    $affid = addslashes($affid);
    $sql = "SELECT ISDEFAULT, URL_TEMPLATE FROM app_aff WHERE (ID = '{$affid}' OR ISDEFAULT = 1)";
    $sth = mysql_query($sql, $CONN_FE);
    if (!$sth)
       throw new Exception (mysql_error($CONN_FE), mysql_errno($CONN_FE));

    $arr = array('DEF'=>'', 'AFF'=>'');
    while($row = mysql_fetch_assoc($sth)){
        if ($row['ISDEFAULT'] == 1)
            $arr['DEF'] = $row['URL_TEMPLATE'];
        else
            $arr['AFF'] = $row['URL_TEMPLATE'];
    }
    return $arr;
}



function get_redirect_url($url='', $appid='', $country_code='', $jailbreak=false) {
    $aff = get_aff_link($appid, $country_code, $jailbreak);    

    if (isset($aff['AFFURL']) && $aff['AFFURL'] != '')
        return $aff['AFFURL'];
    else {
        $tpl = get_aff_template(isset($aff['AFFID'])? $aff['AFFID'] : '');
        return wrap_deepurl($tpl['AFF'] != ''? $tpl['AFF'] : $tpl['DEF'], $url);    
    }
}

