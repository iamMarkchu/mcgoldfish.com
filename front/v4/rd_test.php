<?php
if(!defined("RM_SKIP_CHECKING")) define("RM_SKIP_CHECKING", false);
try {
    //fron end comm scripts has include incoming.php
    require_once dirname(__FILE__).'/../include_common.php';

    require_once TRACKING_FUNC_AFF;        
    require_once TRACKING_CONFIG_AFF;
    include_once 'pv.php';           

	$useBD = false;
    if (isset($_REQUEST['go']) && $_REQUEST['go'] != '') {
        $v = explode('|', $_REQUEST['go']);
        if (isset($v[0]) && preg_match('/^[\d]+$/', $v[0])) 
            $id = $v[0];
        elseif(isset($v[0])) {
            $id = 0;
            $desturl = base64_decode(urldecode($v[0]));
        }
        else {
            $id = 0;
            $desturl = '/';
        }
            

        $page_type  = isset($v[1]) && $v[1] != ''? base64_decode(urldecode($v[1])) : '';
        $page_value = isset($v[2]) && $v[2] != ''? base64_decode(urldecode($v[2])) : '';
        $clk_area   = isset($v[3]) && $v[3] != ''? base64_decode(urldecode($v[3])) : '';
        $mega_sid   = isset($v[4]) && $v[4] != ''? base64_decode(urldecode($v[4])) : '';                                                           
    }
    else {
        if (isset($_REQUEST['a']) && $_REQUEST['a'] == 1) {
            $page_type  = isset($_REQUEST['pt'])? urldecode($_REQUEST['pt']) : '';
            $page_value = isset($_REQUEST['pv'])? urldecode($_REQUEST['pv']) : '';
            $clk_area   = isset($_REQUEST['ca'])? urldecode($_REQUEST['ca']) : '';               
            $mega_sid   = isset($_REQUEST['mgsid'])? urldecode($_REQUEST['mgsid']) : '';                                
        }
        else {
            $page_type  = isset($_REQUEST['pt'])? base64_decode(urldecode($_REQUEST['pt'])) : '';
            $page_value = isset($_REQUEST['pv'])? base64_decode(urldecode($_REQUEST['pv'])) : '';
            $clk_area   = isset($_REQUEST['ca'])? base64_decode(urldecode($_REQUEST['ca'])) : '';        
            $mega_sid   = isset($_REQUEST['mgsid'])? base64_decode(urldecode($_REQUEST['mgsid'])) : '';                    
        }

        $id       = isset($_REQUEST['id'])? $_REQUEST['id'] : 0;
        $desturl  = isset($_REQUEST['du'])? base64_decode(urldecode($_REQUEST['du'])) : '/';
        $bz_type  = isset($_REQUEST['bz'])? strtoupper($_REQUEST['bz']) : '';        
        //department store link
        $is_dps   = isset($_REQUEST['dps'])? $_REQUEST['dps'] : 0;
    }


    //print 
    if ( $id > 0 && $bz_type == 'PRINT') {
        $deal  = get_promo($id, $bz_type);
    	$desturl = $deal['dest_url'];
    }elseif (($bz_type == 'PDEALS') && $id > 0) {
    	//PRODUCT DEAL    
        $deal  = get_pdeal($id);
    	$desturl = $deal['dest_url'];
    }
    elseif ($id > 0) {
        //COUPON    
        if (($bz_type == '' || $bz_type == 'COUPON' || $bz_type == 'VOUCHER') && $id > 0) {
            //get valid coupon, invariable coupon return false
            $deal  = get_promo($id, $bz_type);
            $bz_type = 'COUPON';
        }//MERCHANT
        elseif (($bz_type == 'S' || $bz_type == 'MERCHANT') && $id > 0) {
            $deal['merchant_id'] = $id;
            $deal['aff_url'    ] = '';                
            $deal['dest_url'   ] = '';            
            $id = 0;
            $bz_type = 'MERCHANT';
        }//deparate
        elseif ($bz_type == 'SC' && $id > 0) {
            $deal = get_additional_coupon($id); 
            $bz_type = 'STORE_COUPON';
        }//SHARE to SNS
        elseif ($bz_type == 'FB' || $bz_type == 'TW' || $bz_type == 'GG') {
            $deal['aff_url']     = get_share_url($id, strtolower($bz_type));
            $deal['merchant_id'] = 0;                
            $bz_type = 'SHARE_'.$bz_type;
        }//DEFAULT
        else {
            $deal['merchant_id'] = 0;
            $deal['aff_url'    ] = '';                
            $deal['dest_url'   ] = '';
            $bz_type = 'MANUAL';

            if ($desturl == '/') 
                throw new Exception ('/');         
        }
        
        //Department Store
        if ($is_dps && $deal['merchant_id'] > 0 && $page_value > 0) {
            $rs = get_department_store($page_type, $page_value, $deal['merchant_id']);
            if (isset($rs['AffUrl']) && $rs['AffUrl'] != '') {
                $deal['aff_url'] = $rs['AffUrl'];
            }
            elseif (isset($rs['LpUrl']) && $rs['LpUrl'] != '') {
                $deal['dest_url'] = $rs['LpUrl'];
            }                        
        }

        $affurl = $deepurl = $tplurl = $remark = $merdomain = '';
        //check coupon aff url has the same affiliate as merchant's        
        if ($bz_type == 'COUPON' && $deal['aff_url'] != '' && !check_same_affid($deal['aff_url'], get_store_affs($deal['merchant_id'])))
            $deal['aff_url'] = '';
    
        //1. vaild && has aff url on promo
        if (!$deal['expired'] && $deal['aff_url'] != '') {
            $desturl = $deal['aff_url'];
            $affurl = $desturl;
            $remark = 'coupon_aff';
        }
        else {            
            $store = get_store($deal['merchant_id']);                
            //merchant w/ affiliate
            if ($store['aff']) {    
                //vaild, desturl on promo, aff tpl    
                if (!$deal['expired'] && $deal['dest_url'] != '' && $store['deep_url_template'] != '') {
                    $desturl = wrap_deepurl($store['deep_url_template'], $deal['dest_url']);
                    $deepurl = $deal['dest_url'];
                    $tplurl  = $store['deep_url_template'];
                    $remark  = 'coupon_lpurl_tpl';
                    $merdomain = $store['url'];                    
                }
                elseif($store['affiliate_default_url'] != '') {//merchant aff url 
                    $desturl = $store['affiliate_default_url'];
                    $affurl  = $store['affiliate_default_url'];
                    $remark  = 'mer_aff';
                    $merdomain = $store['url'];
                }
                elseif($store['url'] != '' && $store['deep_url_template'] != '') {// merchant domain, aff tpl 
                    $desturl = wrap_deepurl($store['deep_url_template'], $store['url']);                
                    $deepurl = $store['url'];
                    $tplurl  = $store['deep_url_template'];                
                    $remark  = 'mer_domin_tpl';
                    $merdomain = $store['url'];
                }
                elseif(!$deal['expired'] && $deal['dest_url'] != '') { //vaild, dest url on promo
                    $desturl = $deal['dest_url'];
                    $remark  = 'coupon_dest';
                    $merdomain = $store['url'];                
                }
                elseif($store['dest_url'] != '') {// merchant dest url
                    $desturl = $store['dest_url'];
                    $remark  = 'mer_dest';
                    $merdomain = $store['url'];
                }                
                elseif($store['url'] != '') {// merchant domain
                    $desturl = $store['url'];
                    $remark  = 'mer_domain';
                    $merdomain = $store['url'];                                
                }
            }
            else { //merchant w/out affiliate
            	$useBD = true;
                if(!$deal['expired'] && $deal['dest_url'] != '') //vaild, dest url on promo
                    $desturl = $deal['dest_url'];                
                elseif($store['url'] != '') //merchant domain
                    $desturl = $store['url'];            
            }
        }
    }
    elseif (($bz_type == 'FB' || $bz_type == 'TW' || $bz_type == 'GG') && $id == 0) { //share any page
    	$desturl = get_share_url($id, strtolower($bz_type), $page_type == 'home'? true : false);
    	if(strpos($desturl, "Christmas-76127.html") > 0){
    		$desturl.= "?v=141017";
    	}
        $deal['merchant_id'] = 0;                
        $bz_type = 'SHARE_'.$bz_type;        
    }
    elseif($desturl != '') {   
        foreach ($AFF_HARDMAP as $_mdomain => $_afftpl) {
           if (!preg_match('/^http[s]*:\/\/[^\/]*'. $_mdomain .'/', $desturl))
               continue;
           $desturl = wrap_deepurl($_afftpl, $desturl);
           break;
       }    
    }

    if (!$useBD) {
        echo $id."\t".$deal['merchant_id']."\t".get_aff_id($desturl)."\t".$remark."\t".$desturl."\t".$affurl."\t".$deepurl."\t".$tplurl."\t".$merdomain."\n";
    }
    else {
        echo "no aff\n";
    }    


    exit;        
    $T_MERCHANTID = $deal['merchant_id'];    
    global $T_OUTGOINGID;
    $T_OUTGOINGID = set_outgoings($id, $deal['merchant_id'], $page_type, $page_value, $clk_area, $desturl, get_aff_id($desturl), $bz_type);
    
    if ($useBD && stripos($desturl, "http") === 0) {
		if($store["country"] == 'CA')
			$bdkey = "c9f0f895fb98ab9159f51fd0297e236d";
    	else
    		$bdkey = "1679091c5a880faf6fb5e6087eb1b2dc";
		$sid = urlencode(TRACKING_SITEID."_".$T_SERVERID."_".get_current_server_id()."_".$T_OUTGOINGID."_".$T_SESSION);
	    $desturl = "http://mgsvc.com/?key=" . $bdkey . "&id=" . $sid . "&url=" . urlencode($desturl);
    }else if ( $bz_type != 'PRINT' && stripos($bz_type, 'SHARE') === false && !$useBD) {
        $desturl = wrap_sid($desturl);
    }
    
    
    //set_outbounds($desturl, $T_OUTGOINGID, '', '', '', '', $mega_sid);    
    if (isset($_REQUEST['xo']) && $_REQUEST['xo'] == 'ox') {
        echo $bz_type." => ".$desturl."\n";
    }
    else {
        redirect($desturl, 302);
    }
    exit;

}
catch (Exception $e) {
    if (isset($_REQUEST['xo']) && $_REQUEST['xo'] == 'ox') {
        echo $desturl."\n";
    }
    else {
        redirect($e->getMessage(), 302); 
    }
    exit(1);
}

