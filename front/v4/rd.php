<?php
if(!defined("RM_SKIP_CHECKING")) define("RM_SKIP_CHECKING",true);
try {
    //fron end comm scripts has include incoming.php
    require_once dirname(__FILE__).'/../include_common.php';

    require_once TRACKING_FUNC_AFF;        
    require_once TRACKING_CONFIG_AFF;
    include_once 'pv.php';           


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

        //check coupon aff url has the same affiliate as merchant's        
        if ($bz_type == 'COUPON' && $deal['aff_url'] != '' && !check_same_affid($deal['aff_url'], get_store_affs($deal['merchant_id'])))
            $deal['aff_url'] = '';
    
        //1. vaild && has aff url on promo
        if (!$deal['expired'] && $deal['aff_url'] != '') {
        	$desturl = $deal['aff_url'];
        }
        elseif (!$deal['expired'] && $deal['dest_url'] != '') {
        	$desturl = $deal['dest_url'];
        }
        elseif (isset($deal['merchant_id'])) {            
            $store = get_store($deal['merchant_id']); 
            if($store['url'] != "") {
            	$desturl = $store['url'];
            }
        }

      
        //printable coupon
/*        if (preg_match('/\.(jpg|pdf|png|gif|jpeg)$/i', $desturl)) {
            $bz_type = 'PRINT';
            $desturl = get_share_url($id, strtolower($bz_type));
        }  */              
    }
    elseif (($bz_type == 'FB' || $bz_type == 'TW' || $bz_type == 'GG') && $id == 0) { //share any page
    	$desturl = get_share_url($id, strtolower($bz_type), $page_type == 'home'? true : false);    
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
    $T_MERCHANTID = $deal['merchant_id'];
    global $T_OUTGOINGID;
    $T_OUTGOINGID = set_outgoings($id, $deal['merchant_id'], $page_type, $page_value, $clk_area, $desturl, get_aff_id($desturl), $bz_type);
    if ($bz_type != 'PRINT' && stripos($bz_type, 'SHARE') === false) {
    	if(stripos($desturl, 'mgsvc.com') === false && stripos($desturl, "http") !== false) {
    		$sid = urlencode(TRACKING_SITEID."_".$T_SERVERID."_".get_current_server_id()."_".$T_OUTGOINGID."_".$T_SESSION);
    		$mid = isset($deal['merchant_id']) ? intval($deal['merchant_id']) : 0;
			$cid = !$deal['expired'] ? intval($id) : 0;
	    	$desturl = "http://mgsvc.com/?key=e4da3b7fbbce2345d7772b0674a318d5&id=" . $sid . "&mid=" . $mid . "&cid=" . $cid . "&url=" . urlencode($desturl);
	    	@error_log(date("Y-m-d H:i:s")."\t".$sid."\t".$desturl."\n", 3, DATA_ROOT . "bdg.log");
    	} else {
    		@error_log(date("Y-m-d H:i:s")."\t".$T_OUTGOINGID."\t".$desturl."\n", 3, DATA_ROOT . "bdg2.log");
    	}
    } else {
    	@error_log(date("Y-m-d H:i:s")."\t".$T_OUTGOINGID."\t".$desturl."\n", 3, DATA_ROOT . "bdg3.log");
    }
    setcookie(TRACKING_COOKIE.'_og', 1, 0, '/');
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

