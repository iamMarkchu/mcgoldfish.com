<?php
    function wrap_deepurl($template='', $url='') {
        if ($template == '')
            return $url;

        if (stripos($template, 'shareasale.com')) {
            $nTmp = stripos($url, 'http://');
            if ($nTmp !== false) {
                $url = substr($url, $nTmp + 7);
            } 
            else {
                $nTmp = stripos($url, urlencode('http://'));
                if ($nTmp !== false) {
                    $url = substr($url, strlen(urlencode('http://')));
                }
            }
        }
        //changed by jimmy @ 2010-01-18
        //changed by Pani @2012-03-29
        //To handle all the custome links which were start with PURE_DEEPURL,DEEPURL,DOUBLE_ENCODE_DEEPURL
        //Normalize the Query Mark [?|&] in destional url
        $mark_and = '&';
        $mark_que = '?';
        $has_deep_mark = false;
        if (preg_match('/(.*)\[(PURE_DEEPURL|DEEPURL|DOUBLE_ENCODE_DEEPURL|URI|ENCODE_URI|DOUBLE_ENCODE_URI)\](\[\?\|&\])*/', $template, $m)) {
            preg_match('/^http(s)?:\/\/[^\/]+(\/)?(.*)/', $url, $q);
            $has_deep_mark = $m[3] != ''? true : $has_deep_mark;

            switch ($m[2]) {
                case 'PURE_DEEPURL':
                    $template = str_ireplace('[PURE_DEEPURL]', $url, $template);                            
                    break;
                case 'DEEPURL':                                
                    $template = str_ireplace('[DEEPURL]', ($m[1] == ''? $url: urlencode($url)), $template);
                    if ($m[3] == '[?|&]' && $m[1] != '') {
                        $mark_and = urlencode($mark_and);
                        $mark_que = urlencode($mark_que);                            
                    }
                    break;
                case 'DOUBLE_ENCODE_DEEPURL':
                    $template = str_ireplace('[DOUBLE_ENCODE_DEEPURL]', ($m[1] == ''? $url : urlencode(urlencode($url))), $template);                            
                    if ($m[3] == '[?|&]' && $m[1] != '') {
                        $mark_and = urlencode(urlencode($mark_and));
                        $mark_que = urlencode(urlencode($mark_que));                            
                    }                                
                    break;                                               
                case 'URI':
                    $template = preg_replace('/([^:])\/{2,}/', '\1/', str_ireplace('[URI]', '/'.(isset($q[3]) && $q[3] != ''? $q[3] : ''), $template));                               
                    break;
                case 'ENCODE_URI':
                    $template = preg_replace('/([^:])\/{2,}/', '\1/',  str_ireplace('[ENCODE_URI]', urlencode('/'.(isset($q[3]) && $q[3] != ''? $q[3] : '')), $template));
                    if ($m[3] == '[?|&]' && $m[1] != '') {
                        $mark_and = urlencode($mark_and);
                        $mark_que = urlencode($mark_que);                            
                    }                                
                    break;
                case 'DOUBLE_ENCODE_URI':
                    $template = preg_replace('/([^:])\/{2,}/', '\1/',  str_ireplace('[DOUBLE_ENCODE_URI]', urlencode(urlencode('/'.(isset($q[3]) && $q[3] != ''? $q[3] : ''))), $template));
                    if ($m[3] == '[?|&]' && $m[1] != '') {
                        $mark_and = urlencode(urlencode($mark_and));
                        $mark_que = urlencode(urlencode($mark_que));                            
                    }                                                                
                    break;
            }
        }

        $m = array();
        if (preg_match('/(.*)(\[\?\|&\].*)/', $template, $m)) { //&& $start_w_tpl
            if ($has_deep_mark) {
                $m[1] = $url;
            }
                            
            if (preg_match('/[\?&][^&]+=[^&]*/U', $m[1]))
                $template = str_replace('[?|&]', $mark_and, $template);
            else
                $template = str_replace('[?|&]', $mark_que, $template);
        }
        return $template;
    }




    function get_aff_name($url='') {
        if ($url == '')
            return false;

        global $AFFILIATE_DOMAINS;            
//        global $AFFILIATE_SIDS;            

        foreach ($AFFILIATE_DOMAINS as $aff => $v) {
            $arr = explode(',', $v);
            foreach ($arr as $domain) {
                if (stripos($url, $domain) !== false) {                    
                    return $aff;
                }
            }
        }
        return false;        
    }

    function get_aff_id($url='') {
        if ($url == '')
            return 0;

        $affname = get_aff_name($url);
        
        global $AFFILIATE_IDS;
        return isset($AFFILIATE_IDS[$affname])? $AFFILIATE_IDS[$affname] : 0;
    }


    function check_same_affid($url, $affids) {
        if ($url == '' || !is_array($affids) || count($affids) == 0)
            return false;                
    
        global $AFFILIATE_DOMAINS;            
        global $AFFILIATE_IDS;

        foreach ($AFFILIATE_DOMAINS as $aff => $v) {
            $arr = explode(',', $v);
            foreach ($arr as $domain) {
                if (stripos($url, $domain) !== false && isset($AFFILIATE_IDS[$aff]) && in_array($AFFILIATE_IDS[$aff], $affids)) {                    
                    return true;
                }
            }
        }
        return false;                    
    }


    /**
     * Find out affiliate name in destination url
     *
     * @param 
     * @return bool
     */
    function get_sid($url='', $source='') {
        global $AFFILIATE_SIDS, $T_SESSION, $T_SERVERID, $T_OUTGOINGID ;                        

        $sid = '';
        $aff = get_aff_name($url);
        if (isset($AFFILIATE_SIDS[$aff])) {
            $sid = str_replace(array('{site}','{serverid}','{currserverid}','{outgoingid}','{incomingid}'), array(TRACKING_SITEID, $T_SERVERID, get_current_server_id(), $T_OUTGOINGID, $T_SESSION), $AFFILIATE_SIDS[$aff]);

            if ($source == 'manu' && preg_match('/(_|aa)/', $sid, $m))            	
                $sid .= $m[1].'m';              
       }
       return $sid;
    }



    /**
     *
     *  replace amazon id in url
     */
    function replace_amazon_id($url) {
        global $AFF_AMZUS_SITES;
        global $AFF_AMZCA_SITES;
        global $AFF_AMZUK_SITES;

        $tag = '';
        if(preg_match('/^http(s)?:\/\/[^\/]+\.amazon\.([^\/]+)\//', $url, $m)) {
            if (stripos($m[2], 'com') !== false && isset($AFF_AMZUS_SITES[TRACKING_SITENAME]) ) {
                $tag = $AFF_AMZUS_SITES[TRACKING_SITENAME];
            }
            elseif (stripos($m[2], 'ca') !== false && isset($AFF_AMZCA_SITES[TRACKING_SITENAME])) {
                $tag = $AFF_AMZCA_SITES[TRACKING_SITENAME];            
            }
            elseif (stripos($m[2], 'co.uk') !== false && isset($AFF_AMZUK_SITES[TRACKING_SITENAME])) {
                $tag = $AFF_AMZUK_SITES[TRACKING_SITENAME];            
            }
            else {
                return $url;
            }            
        }
        else {
            return $url;
        }

        if(stripos($url, '&tag=') !== false) 
            $url =  preg_replace('/&tag=[^&]+/', '&tag='.$tag, $url);
        else
            $url .= "&tag=".$tag;

        return $url;
    }


    /**
     *
     *  replace ebay id in url
     */    
    function replace_ebay_id($url) {
        global $AFF_EBAY_SITES;
        
        $tag = '';
        if(preg_match('/^http(s)?:\/\/[^\/]+\.ebay\.com\//', $url) && isset($AFF_EBAY_SITES[TRACKING_SITENAME])) {
            $tag = $AFF_EBAY_SITES[TRACKING_SITENAME];
        }
        else {
            return $url;
        }

        if(stripos($url, '&campid=') !== false) 
            $url =  preg_replace('/&campid=[^&]+/', '&campid='.$tag, $url);
        else
            $url .= "&campid=".$tag;

        return $url;    
    }


    /**
     *
     *  replace CJ id in url
     */      
    function replace_cj_id($url) {
        if(!preg_match('/\/click-([^-]+)-/i', $url, $m))
            return $url;

		//replace CJ PID
		global $AFF_CJ_SITES;
		if($m[1] && isset($AFF_CJ_SITES[TRACKING_SITENAME]) && strcasecmp($AFF_CJ_SITES[TRACKING_SITENAME], $m[1]) <> 0) {
			$url = preg_replace("/\/click-{$m[1]}-/i", "/click-".$AFF_CJ_SITES[TRACKING_SITENAME]."-", $url);
        }
        
        return $url;
    }


    /**
     *
     *  replace Skimlinks id in url
     */      
    function replace_skimlinks_id($url) {
        if(!preg_match('/(.*id=7438X)([^&]+)(.*)/i', $url, $m))
            return $url;


		global $AFF_SKIMLINKS_SITES;
        if($m[2] && isset($AFF_SKIMLINKS_SITES[TRACKING_SITENAME]) && strcasecmp($AFF_SKIMLINKS_SITES[TRACKING_SITENAME], $m[2]) <> 0) {
			$url = $m[1].$AFF_SKIMLINKS_SITES[TRACKING_SITENAME].$m[3];
		}        
        return $url;
    }


    /**
     * Find and set sub tracking id 
     *
     * @param string $userAgent
     * @return bool
     */
    function wrap_sid($url) {
		/*
		for some stupid affiliates!!!, they do NOT allow to append a GET value to the url, if the url ends with a deep url
		we sparate the deep url and append it again at last
		*/            
        global $AFFILIATE_DEEPURLS;

        $unusual_sid = false;
	    if (preg_match('/tradedoubler\.com\/click\?p\([^\)]+\)/', $url)) {               
	        $unusual_sid = true;
	    }
    
        $aff = get_aff_name($url);
        $sid = get_sid($url);
        $deepurl = '';        
		if(isset($AFFILIATE_DEEPURLS[$aff]) && preg_match("/^(http.*[&\?]{1})({$AFFILIATE_DEEPURLS[$aff]}=[^&]*)(.*)/", $url, $m)) {
			//try to move deep url para to the last position
			$deepurl = $m[2];
			$url = $m[1].ltrim($m[3], '&'); 
		}

        $sid_uri = $sid;
        $sid_val = $sid;
        if(preg_match('/^([^=]*)=([^&]*)$/', $sid, $m)) {
            $sid_uri = $m[1];
            $sid_val = $m[2];
        } 

        if (stripos($url, '[SUBTRACKING]') !== false) { 
            if (stripos($url, $sid_uri) !== false || $aff == "Paid_On_Results")
                $url = str_ireplace('[SUBTRACKING]', $sid_val, $url);
            else
                $url = str_ireplace('[SUBTRACKING]', $sid, $url);
        }
        elseif (preg_match('/([&\?]{1})'.$sid_uri.'=([^&]*)/', $url, $m)) {
            $url = str_ireplace($m[1].$sid_uri.'='.$m[2], $m[1].$sid, $url);
        }
        elseif (stripos($url, '?') === false && stripos($url, '&') === false) {
        	if(stripos($aff, 'onenetworkdirect') !== false)
				$url .=  "&" . $sid;
			else if (!empty($sid))
				$url .=  "?" . $sid;
        }
        else {
			$url = rtrim($url, "&");
            if ($unusual_sid) {
                $url = $url . $sid_uri .'('. $sid_val .')';
            }  
			elseif(substr($url,-1) == "?" || $sid == '') {
				$url .= $sid;
			}
			else {
                $url .= '&'. $sid;
			}            
        }

		if ($deepurl) {
            if ($unusual_sid) 
                $url = $url . $deepurl;                
            else
                $url = $url .'&'. $deepurl;
		}        

        //CJ URL FORMAT
        if(stripos($aff, 'Commission_Junction') !== false)
            $url = replace_cj_id($url);
        elseif (stripos($aff, 'Amazon') !== false) 
            $url = replace_amazon_id($url);
        elseif (stripos($aff, 'eBay') !== false) 
            $url = replace_ebay_id($url);
        elseif (stripos($aff, 'Skimlinks') !== false) 
            $url = replace_skimlinks_id($url);

        //return replace_link($aff, $url);
        return $url;
    }    
    
    
    function replace_link($aff, $url) {
    	include 'subaff_refuse.php';
    	
    	global $T_MERCHANTID;
        if(isset($VIGLINK_MIDS[$T_MERCHANTID])) {
        	return replace_viglink($aff, $url);
        } else {
        	return replace_skimlinks($aff, $url);
        }
    }

    /**
     * Find and set sub tracking id 
     *
     * @param string $userAgent
     * @return bool
      */
    function replace_viglink($aff, $url) {
        include 'subaff_refuse.php';

        global $T_MERCHANTID;
        if (strpos($url, "http") !== 0 || $aff || stripos($url, 'viglink.com') !== false || !defined('TOKEN_VIGLINK') || !TOKEN_VIGLINK || (isset($VIGLINK_REFUSED) && $VIGLINK_REFUSED[$T_MERCHANTID]))
            return $url;                

        global $AFFILIATE_SIDS, $T_SESSION, $T_SERVERID, $T_OUTGOINGID ;                                
        return "http://redirect.viglink.com?key=".TOKEN_VIGLINK."&u=".urlencode($url)."&cuid=".urlencode(TRACKING_SITEID."_".$T_SERVERID."_".get_current_server_id()."_".$T_OUTGOINGID."_".$T_SESSION);
    }

    /**
     * Find and set sub tracking id 
     *
     * @param string $userAgent
     * @return bool
      */
    function replace_skimlinks($aff, $url) {
        include 'subaff_refuse.php';

        global $T_MERCHANTID;
        if (strpos($url, "http") !== 0 || $aff || stripos($url, 'go.redirectingat.com') !== false || !defined('TOKEN_SKIMLINKS') || !TOKEN_SKIMLINKS || (isset($SKIMLINKS_REFUSED) && $SKIMLINKS_REFUSED[$T_MERCHANTID]))
            return $url;                

        global $AFFILIATE_SIDS, $T_SESSION, $T_SERVERID, $T_OUTGOINGID ;                                
        //http://go.redirectingat.com?id=7438X662619&xcust=s01aa3aa3aa20128134aa44910135&xs=1&&url=https%3A%2F%2Fwww.lootcrate.com%2F        
        return "http://go.redirectingat.com?id=".TOKEN_SKIMLINKS."&xcust=".urlencode(TRACKING_SITEID."_".$T_SERVERID."_".get_current_server_id()."_".$T_OUTGOINGID."_".$T_SESSION)."&xs=1&url=".urlencode($url);
    }  