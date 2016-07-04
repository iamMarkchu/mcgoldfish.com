<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}
//get static page block
function get_page_static_data(){
	
	// set cache
	$cacheFileName = 'glob_static_content';
	$objCache = new Cache($cacheFileName);
	if (DEBUG_MODE || isset($_GET['forcerefresh'])) //refresh the cache by force
		$data = '';
	else
		$data = $objCache->getCache();
	if(!$data){
		$static_obj = new StaticBlockData();
		$list = $static_obj->get_data_by_type_id(92); //page header
		if(!empty($list)){
			$data['page_header'] = $list[0];
		}
		$list = $static_obj->get_data_by_type_id(93); //page left holiday
		if(!empty($list)){
			$data['page_left_holiday'] = $list;
		}
		$objCache->setCache($data);
	}
	return $data;
}
function get_page_static_nav_data(){
	//导航条从memcache中读取
    $cacheFileName = 'glob_static_nav';
    $objNavCache = new Cache($cacheFileName);
    if (DEBUG_MODE || isset($_GET['forcerefresh'])) //refresh the cache by force
        $data = '';
    else
        $data = $objNavCache->getCache();
    if(!$data){
        $objCategory = new Category();
        $data = $objCategory->getAllCategory();
        $objNavCache->setCache($data);
    }
    return $data;
}
function dispatch_url($url_path) { 
	$r = array();
	$old_url_path = $url_path;
	$qu_mark_pos = strpos($url_path, '?');
	$and_mark_pos = strpos($url_path, '&');

	if ($url_path == '/' || empty($url_path)) {
		$r['ModelType'] = 'homepage';
		return $r;
	}
	elseif ($qu_mark_pos !== false) {
		$left_url_str = substr($url_path, $qu_mark_pos);
		$url_path = substr($url_path, 0, $qu_mark_pos);	
	}
	elseif ($and_mark_pos !== false) {
		$left_url_str = substr($url_path, $and_mark_pos);
		$url_path = substr($url_path, 0, $and_mark_pos);
	}
	
	// if (substr($url_path, -1) != '/' && strpos($url_path, '-HIJACK') === false)
	// 	$url_path .= '/';
	$rewrite_url_obj = new RewriteUrl();
	$ii = 0;
	do {
		if (!isset($_r)) {
			$_r = $rewrite_url_obj->get_rewrite_url_by_path($url_path);
			if (!$_r) {
				goto_404();
			}
			if($_r['status'] == "no") {
				goto_404();
			}
		}else {
			if($_r['status'] == "no") {
				goto_404();
			}
			$_r = $rewrite_url_obj->get_rewrite_url_by_id($_r['JumpRewriteUrlID']);
		}
		
		if (isset($_r['IsJump']) && in_array($_r['IsJump'], array(301, 302, 404, 'HIJACK'))) {
			$prev_rewrite = $_r;
		}else {
			if (!$_r) {
				$_r = $prev_rewrite;
			}
			
			if (isset($prev_rewrite)) {
				if ($prev_rewrite['IsJump'] == 301 || $prev_rewrite['IsJump'] == 'HIJACK') {
					permanent_header($_r['RequestPath'] . $left_url_str);
				}
				elseif ($prev_rewrite['IsJump'] == 302) {
					temporarily_header($_r['RequestPath'] . $left_url_str);
				}
				elseif ($prev_rewrite['IsJump'] == 404) {
					goto_404($_r['RequestPath'] . $left_url_str);
				}
			}
			break;
		}
		
		$ii++;
		if($ii > 5){
			file_put_contents(INCLUDE_ROOT."data/data_a.txt", $url_path,FILE_APPEND);
			goto_404();
		}
	} while(true);
	
	if ($_r['status'] == 'no')
		goto_404();
		
	$r = $_r;
	if (!empty($r) && $url_path != $r['requestpath']) {
		permanent_header($r['requestpath']);
		exit;
	}
	return $r;
}

function split_404_url($url_path) {
	if (empty($url_path))
		return;
	
	$qu_mark_pos = strpos($url_path, '?');
	$and_mark_pos = strpos($url_path, '&');
	
	if ($qu_mark_pos !== false) {
		$left_url_str = substr($url_path, $qu_mark_pos);
		$url_path = substr($url_path, 0, $qu_mark_pos);
		
	}
	elseif ($and_mark_pos !== false) {
		$left_url_str = substr($url_path, $and_mark_pos);
		$url_path = substr($url_path, 0, $and_mark_pos);
	}
	
	if (substr($url_path, -1) != '/')
		$url_path .= '/';
	
	$url_path_new = '';
	preg_match('{.*?/([^/]+)/$}', $url_path, $matches);
	if (isset($matches[1]) && !empty($matches[1])) {
		$url_path_new = trim($matches[1]);
	}
	
	if (empty($url_path_new))
		return;
	
	$url_path_new = preg_replace('{[^a-zA-Z0-9]+}', ' ', $url_path_new);
	$url_path_new = trim($url_path_new);
	$url_path_new = preg_replace('{\s+}', '-', $url_path_new);
	if (empty($url_path_new))
		return;
	
	$url_path_new = '/' . $url_path_new . '/';
	
	return '/coupons' . $url_path_new;
	
}

function dispatch_constant_url($url_path){
	if (!$url_path) 
		return false;
	global $__contants_rewrite;
	#劫持/black-friday-ads/
	if($url_path == '/black-friday-ads/'){
		$tmp = array();
		$tmp['RequestPath'] = $url_path;
		$tmp['ModelType'] = 'channel';
		$tmp['data'] = array();
		return $tmp;
	}

	#劫持/black-friday-ads/blain-s-farm-fleet/.
	$url_path_info = explode('/',$url_path);
	if(isset($url_path_info[1]) && $url_path_info[1] == 'black-friday-ads' && isset($url_path_info[2]) && $url_path_info[2] ){
		$name = addslashes($url_path_info[2]);
		$page = $url_path_info[3];
		if($page && $page[0] == 'p')$page = substr($page,1);
		$page = intval($page);

		$tmp = array();
		$tmp['RequestPath'] = $url_path;
		$tmp['ModelType'] = 'ads';
		$tmp['data'] = array('name'=>$name,'page'=>$page);
		return $tmp;
	}

	foreach($__contants_rewrite as $v){
		if($v['RequestPath'] == $url_path){
			if(isset($v['JumpToUrl']) && $v['JumpToUrl']){
				permanent_header($v['JumpToUrl']);
			}else{
				return $v;
			}
		}
	}
	return false;
}

function permanent_header($url = '') {
	$url = !trim($url) ? '/' : trim($url);
	header('HTTP/1.1 301 Moved Permanently');
	header('Cache-Control: no-cache');
	header('Location: ' . $url);
	exit;
}

function permanent_header_new($url,$g_sessionID,$outgoingId) {
	$url = !trim($url) ? '/' : trim($url);
	header('HTTP/1.1 302 Moved Permanently');
	header('Cache-Control: no-cache');
	$defurl = "http://redirect.viglink.com?key=cad6cf4a614403969204fb78b3f0b467&u=[[URL]]&cuid=s73_[[INCOMCLK]]_[[OUTCLK]]_m";
	preg_match("/\.([a-z|A-Z|-|0-9]+)\./",$url,$r_s);
	
	if(!empty($r_s[1])){
		$r_s[1] = strtolower($r_s[1]).".";
		$sql = "SELECT ID FROM aff_merchant_domain WHERE Domain LIKE '".addslashes($r_s[1])."%' limit 1 ";
		
		$r = $GLOBALS['db']->getFirstRow($sql);
	
		if(!empty($r['ID'])){
			$defurl = "http://go.redirectingat.com?id=7438X1485686&xcust=s73aa[[INCOMCLK]]aa[[OUTCLK]]aam&xs=1&url=[[URL]]";
		}
		
	}
	$from_arr = array("[[URL]]", "[[INCOMCLK]]", "[[OUTCLK]]");
	$to_arr   = array($url, $g_sessionID, $outgoingId);
	$url = str_replace($from_arr, $to_arr, $defurl);
	header('Location: ' . $url);
	exit;
}



function temporarily_header($url = '') {
	$url = !trim($url) ? '/' : trim($url);
	header('HTTP/1.1 302 Moved Temporarily');
	header('Cache-Control: no-cache');
	header('Location: ' . $url);
	exit;
}

function goto_404(){
	$ip = get_client_ip();
	$agent = $_SERVER['HTTP_USER_AGENT'];
	log_404($_SERVER['SCRIPT_URL'] . "\t" . $ip . "\t" . $agent);
	
	header('HTTP/1.1 404 Not Found');
	header('Cache-Control: no-cache');
	
	include_once FRONT_DIR . '404_func.php';
	exit;
}

function filter_source_tag() {
	$tags = array('mktsrc','ca');
	foreach ($tags as $i => $_tag) $tags[] = strtoupper($_tag);
	
	$filtered_url = '';
	$url = $_SERVER['REQUEST_URI'];
	$pattern = '/(.*)(\\/|html|php)(&|\\?)(.*)/i';
	if (! preg_match($pattern,$url,$matches)) {
		//something wrong here: not standard format
		return false;
	}
	
	$filtered_url = $matches[1] . $matches[2];
	$arr_vars = preg_split('[&\\?]',$matches[4]);
	$matchedcount = 0;
	foreach ($arr_vars as $i => $_var) {
		list($var_name) = explode('=', $_var);
		if (in_array($var_name, $tags) || $_var == '') {
			$matchedcount++;
			unset($arr_vars[$i]);
		} 
	}

	if ($matchedcount > 0) {
		if (sizeof($arr_vars)) $filtered_url .= $matches[3] . implode('&', $arr_vars);
		permanent_header($filtered_url);
	}
}

function getRdUrl($type, $id, $dataSource, $dataurl) {
	return base64_encode($type) . '|' . base64_encode($id) . '|' . base64_encode($dataSource) . '|' . base64_encode($dataurl);
}

function get_lang_by_country_code($countrycode) {
	if (!$countrycode)
		return 'en';
	
	if ($countrycode == 'DE')
		$r = 'de';
	elseif ($countrycode == 'FR')
		$r = 'fr';
	else
		$r = 'en';
	
	return $r;
}

function get_coupon_display_title($data){
	global $default_lang;
	#get language package
    $objLang = new Language();
    $objLang->setLang($default_lang);
    $word_dict = $objLang->get_word_dict();
	$code = $data['CsgCode'];
	$des = $data['CsgTitle'];
	$promotiondetail = $data['CsgPromotionDetail'];
	$promotionoff = $data['CsgPromotionOff'];
	if(isset($data['CsgPromotionContent']) && $data['CsgPromotionContent']){
		$PromotionContent = $data['CsgPromotionContent'];	
	}else{
		$PromotionContent = '';
	}
	if(empty($promotiondetail)){
		$otherwords = array('Sonderaktion','Top Rabattaktion','Großer Preissturz','Riesen Schnäppchen','Top Angebot');
		shuffle($otherwords);
		$title = $otherwords[0];
		return $title;
	}
	if(empty($PromotionContent)){
		#原逻辑 
		#get rand other words
		$otherwords = array('Sonderaktion','Top Rabattaktion','Großer Preissturz','Riesen Schnäppchen','Top Angebot');
		$count = count($otherwords);
		$other = $otherwords[rand(0,$count-1)];
		#get rand site wide words
	    $site_wide = 'Sonderaktion';
	    #parse promotiondetail
		$promotiontype = array(
			"bngn"=>$word_dict['BNGN_'.rand(1,3)],               //rand 随机
			"bogo"=>$word_dict['BOGO'], 
			"free_gift"=>$word_dict['FREE_GIFTS_'.rand(1,4)], 
			"free_sample"=>$word_dict['FREE_SAMPLE_'.rand(1,3)], 
			"free_shipping"=>$word_dict['FREE_SHIPPING'.rand(1,5)], 
			"money"=>"", 
			"other"=>$other, 
			"percent"=>"",
			"site_wide"=>$site_wide,
			"sale_clearance"=>$word_dict['SALES_CLEARANCE_'.rand(1,4)],
			"reward"=>$word_dict['REWARD'],
			"free_trial"=>$word_dict['FREE_TRIAL_'.rand(1,4)],
			"free_download"=>$word_dict['FREE_DOWNLOAD_'.rand(1,4)],
			"rebate"=>$word_dict['REBATE'],
		);
		$currency = array("dollar"=>"$", "euro"=>"&euro;", "pound"=>"&pound;", "rmb"=>"&yen;", "rupee"=>"Rs", "naira"=>"NGN");
		$title = "";
		$iscoupon = $code?true:false;
		#make title
		if(!empty($promotiondetail)){
			if(strpos($promotiondetail, "money")===0){
				list($money, $type) = explode(",", $promotiondetail);
				if(!empty($type) && isset($currency[$type])){
					$s_mo = $promotionoff.$currency[$type];      //缩短变量名
					if($iscoupon){
						$title_arr = array(' Rabatt Gutscheincode',' Gutscheincode sichern',' Online Gutschein nutzen');
						$count = count($title_arr);
						$title = $s_mo. $title_arr[rand(0,$count-1)];
					}else{
						$title_arr[] = 'Jetzt im Netz '.$s_mo.' sparen';
						$title_arr[] = 'Hol dir '.$s_mo.' Rabatt - Nur im Web';
						$title_arr[] = 'Jetzt satte '.$s_mo.' im Web sparen';
						$title_arr[] = $s_mo.' Euro Ermäßigung ';
						$title = $title_arr[rand(1,4)];
					}
				}
			} elseif (isset($promotiontype[$promotiondetail])){
				if ($promotiondetail == "percent") {
					$s_per = $promotionoff . "% "." ";
					$otherwords = array($s_per.'Online Rabatt ',
										$s_per.'beim Einkauf sparen',
										$s_per.'Preisnachlass holen',
										$s_per.'beim Einkauf sparen',
										"Jetzt ".$s_per.' weniger bezahlen',
										"Jetzt bestellen und online ".$s_per." sparen",
										"Nur im Online Shop - ".$s_per." Sofort Rabatt ",
										$s_per." Rabatt auf deine Bestellung",
										);
					if($iscoupon){
						$otherwords[] = 'Hol dir den '.$s_per.' Rabatt Gutschein';
						$otherwords[] = 'Gleich mit '.$s_per.' Gutschein bestellen';
					}
					$count = count($otherwords);
					$title = $otherwords[rand(0,$count-1)];		
				} else {
					$title = $promotiontype[$promotiondetail] . " ";
				}
			}
		}
	}else{
		#当有$PromotionContent时。拆分$PromotionContent获取title
		$title = '';
		if($promotiondetail == 'other'){
			$otherwords = array('Sonderaktion','Top Rabattaktion','Großer Preissturz','Riesen Schnäppchen','Top Angebot');
			$count = count($otherwords);
			$title = $otherwords[rand(0,$count-1)];
		}else{
			$title_arr = array();
			$arr_tmp_promotion_content = explode("\n",$PromotionContent);
			foreach($arr_tmp_promotion_content as $k=>$v){
				if(!empty($v)){
					$title_arr[] = translate_promotion_content_to_title($v,$code,$des);	
				}
			}
			$title = $title_arr[0];
		}
	}
	return $title;
}

function translate_promotion_content_to_title($str,$flag,$des){
	
	global $default_lang;
	#get language package
    $objLang = new Language();
    $objLang->setLang($default_lang);
    $word_dict = $objLang->get_word_dict();
	list($type,$words) = explode('|',$str);
	$type_arr = explode(':',$type);
	$title = '';
	$currency = array("dollar"=>"$", "euro"=>"&euro;", "pound"=>"&pound;", "rmb"=>"&yen;", "rupee"=>"Rs", "naira"=>"NGN");
	if($type_arr[0] == 'money'){
		if($type_arr[1] == "euro"){
			$s_mo = $type_arr[2].$currency[$type_arr[1]];   //缩短变量名
			if(!empty($flag)){
				$title_arr = array($s_mo.' Rabatt Gutscheincode',$s_mo.' Gutscheincode sichern',$s_mo.' Online Gutschein nutzen','Jetzt satte '.$s_mo.' im Web sparen');
				$count = count($title_arr);
				$title = $title_arr[rand(0,$count-1)];
			}else{
				$title_arr[] = 'Jetzt im Netz '.$s_mo.' sparen';
				$title_arr[] = 'Hol dir '.$s_mo.' Rabatt - Nur im Web';
				$title_arr[] = 'Jetzt satte '.$s_mo.' im Web sparen';
				$title_arr[] = $s_mo.' Euro Ermäßigung ';
				$title = $title_arr[rand(0,count($title_arr)-1)];
			}
		}else{
			$title_arr = array(' Rabatt Gutschein',' Sonderaktion');
			shuffle($title_arr);
			$title = $currency[$type_arr[1]] . $type_arr[2] . $title_arr[0];
		}
		
	}elseif($type_arr[0] == 'from'){            //ab  
		$reg = '/(\d+([\.|\,]\d+)?)(\€|&euro;)/';
		preg_match($reg, $des,$match);
		if(!empty($match[0])){
			$title_arr[] = 'Ausgewählte Artikel jetzt ab '.$match[0];
			$title_arr[] = 'Tolle Angebote ab nur '.$match[0];
			$title_arr[] = 'Neuer Preissturz - Artikel ab nur '.$match[0];
			$title_arr[] = 'Neue Deals ab nur '.$match[0];
			$title = $title_arr[rand(0,count($title_arr)-1)];
		}else{
			$title = $des;
		}
	}elseif($type_arr[0] == 'percent'){
		$s_per = $type_arr[1] . "% "." ";
		$otherwords = array($s_per.'Online Rabatt ',
							$s_per.'beim Einkauf sparen',
							$s_per.'Preisnachlass holen',
							$s_per.'beim Einkauf sparen',
							"Jetzt ".$s_per.' weniger bezahlen',
							"Jetzt bestellen und online ".$s_per." sparen",
							"Nur im Online Shop - ".$s_per." Sofort Rabatt ",
							$s_per." Rabatt auf deine Bestellung",
							);
		if(!empty($flag)){
			$otherwords[] = 'Hol dir den '.$s_per.' Rabatt Gutschein';
			$otherwords[] = 'Gleich mit '.$s_per.' Gutschein bestellen';
		}
		$count = count($otherwords);
		$title = $otherwords[rand(0,$count-1)];
	}else{
		#get rand other words
		$other = '';
		$otherwords = $otherwords = array('Sonderaktion','Top Rabattaktion','Großer Preissturz','Riesen Schnäppchen','Top Angebot');
		shuffle($otherwords);
		$other = $otherwords[0];
		#get rand site wide words
	    $site_wide = '';
	    $site_wide_words = array('Sonderaktion');
	    shuffle($site_wide_words);
	    $site_wide = $site_wide_words[0];
		$promotiontype = array(
			"bngn"=>$word_dict['BNGN_'.rand(1,3)],               //rand 随机
			"bogo"=>$word_dict['BOGO'], 
			"free_gift"=>$word_dict['FREE_GIFTS_'.rand(1,4)], 
			"free_sample"=>$word_dict['FREE_SAMPLE_'.rand(1,3)], 
			"free_shipping"=>$word_dict['FREE_SHIPPING'.rand(1,5)], 
			"money"=>"", 
			"other"=>$other, 
			"percent"=>"",
			"site_wide"=>$site_wide,
			"sale_clearance"=>$word_dict['SALES_CLEARANCE_'.rand(1,4)],
			"reward"=>$word_dict['REWARD'],
			"free_trial"=>$word_dict['FREE_TRIAL_'.rand(1,4)],
			"free_download"=>$word_dict['FREE_DOWNLOAD_'.rand(1,4)],
			"rebate"=>$word_dict['REBATE'],
		);
		$title = $promotiontype[$type_arr[0]];
	}
	return $title;
}

function format_coupon_expire_date($datetime,$type=''){
	global $default_lang;
	$objLang = new Language();
	if(strcmp($datetime, '0000-00-00 00:00:00') == 0){
		return '';
	}else{
		if($default_lang == 'de'){
			$month = date("M", strtotime($datetime));
			$real_month = $objLang->getMonthWordShort($default_lang,$month);
			return date("j. ", strtotime($datetime)).$real_month/*.date(" Y", strtotime($datetime))*/;
		}elseif($default_lang == 'fr'){
			$month = date("M", strtotime($datetime));
			$real_month = $objLang->getMonthWordShort($default_lang,$month);
			return date("j ", strtotime($datetime)).$real_month.date(" Y", strtotime($datetime));
		}else{
			return date("M j, Y", strtotime($datetime));
		}
	}
}

function get_current_page_url() {
    $current_page_url = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $current_page_url .= "s";
    }
     $current_page_url .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
    	$current_page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $current_page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $current_page_url;
}
function sortCouponData($data) {
	$r = array();
	
	if (!is_array($data) || empty($data)) 
		return $r;

	#feed product coupon 显示规则
	$feed_coupon = array();
    foreach($data as $k=>$v){
        if($v['Source'] == 'AffFeed'){
            $feed_coupon[] = $v;
            unset($data[$k]);
        }
    }
    if(!empty($feed_coupon)){
    	$termObj = new Term();
    	$feed_coupon = $termObj->get_aff_feed_product_coupon($feed_coupon,2);
    	foreach($feed_coupon as $a=>$b){
    		$feed_coupon[$a] = aff_feed_coupon_title($b);
    	}
	}

	//取click数组用来排序 ,移除重复code
	#使用feed product的时候需要考虑是否去重
	$code_list = array();
	foreach($data as $k=>$v){
		if(!empty($v['CsgCode'])){
			$code_list[$v['CsgCode']][$k]=$v;
		}
	}
	
	if(!empty($code_list)){
		$del_code_key = array();
		foreach ($code_list as $k=>$v){
			if(count($v)<2) continue;
			
			$no_del_key = array();
			foreach($v as $dk=>$dv){
				if($dv['AddEditor'] != 'system'){
					$no_del_key[] = $dk;
					break;
				}
			}
			$t_all_key = array_keys($v);
			if(empty($no_del_key)){
				$no_del_key[] = $t_all_key[0];
			}
		
			$del_cp_ids = array_diff($t_all_key,$no_del_key);
			$del_code_key = array_merge($del_code_key,$del_cp_ids);
		}
		if(!empty($del_code_key)){
			foreach($del_code_key as $unk){
				unset($data[$unk]);
			}
		}
		$data =array_values($data);
	}

	$data = array_merge($data,$feed_coupon);
	
	
	$couponData = array();
	$dealData = array();
	$Data_ids = array();       //页面subterm包含的Coupon的ID
	
	foreach ($data as $k => $v){
	    if(!empty($v['ID'])){
	        $Data_ids[$v['ID']] =$k ;
	    }
	}

    //Displayorder为1、2的促销排在第一、二位，不受规则影响。
    //如果多条促销order相同，则挑选其中CsgClickCnt最高的1条，如CsgClickCnt相同，则挑选CsgAddTime最大的1条。2条（没有则从2开始）
	$__DisplayOrder = array();
	$highDispCouponids=array();
	$displayOrderData = array();
	
    $dis_clk = array();
    $dis_addtime = array();
    $dis_dis = array();
    $i = 0;

    foreach ((array)$data as $vv) {
        if (isset($vv['DisplayOrder']) && ($vv['DisplayOrder'] ==1 || $vv['DisplayOrder'] ==2)){
            $__DisplayOrder[$i]['ID'] = $vv['ID'];
            $__DisplayOrder[$i]['DisplayOrder'] = $vv['DisplayOrder'];
            $__DisplayOrder[$i]['CsgClickCnt'] = !empty($vv['CsgClickCnt'])?$vv['CsgClickCnt']:0;
            $__DisplayOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
            $dis_dis[]= $vv['DisplayOrder'];
            $dis_clk[] = !empty($vv['CsgClickCnt'])?$vv['CsgClickCnt']:0;
            $dis_addtime[] = strtotime($vv['CsgAddTime']);
            $i++;
        }
        
    }
    
    if(!empty($__DisplayOrder)){
        array_multisort($dis_dis,$dis_clk,SORT_DESC,$dis_addtime,SORT_DESC,$__DisplayOrder);
    
        foreach ($__DisplayOrder as $v){
            if(!isset($highDispCouponids[$v['DisplayOrder']])){
                $highDispCouponids[$v['DisplayOrder']]=$v['ID'];
            }
        }
    }   

    if(!empty($highDispCouponids)){       
        foreach ($highDispCouponids as $key_id){
            if(isset( $Data_ids[$key_id])){
            	$data[$Data_ids[$key_id]]['ooo'] = 'disp';
                $displayOrderData[] = $data[$Data_ids[$key_id]];
                unset($data[$Data_ids[$key_id]]);
                unset($Data_ids[$key_id]);
            }
        } 
    }
    unset($__DisplayOrder);
    unset($dis_dis);
    unset($dis_clk);
    unset($dis_addtime);

//获取最近7天rev最高的coupon
    $__RevsOrder = array();
    $highRevsCouponids = array();
    $highRevsCouponData = array();
    $i = 0;
    
	foreach ((array)$data as $vv) {
	    if (isset($vv['sevenD_rev']) && $vv['sevenD_rev']>0){
	        $__RevsOrder[$i]['ID'] =$vv['ID'];
	        $__RevsOrder[$i]['sevenD_rev'] = $vv['sevenD_rev'];
	         
	        $__Revs[$i] = $vv['sevenD_rev'];
	         
	        $i++;
	    }	         
	        
	}
	
	if (!empty($__RevsOrder)) {
	    array_multisort($__Revs,SORT_DESC,SORT_NUMERIC,$__RevsOrder);

	    foreach ($__RevsOrder as $_k => $_v) {
	        array_push($highRevsCouponids, $_v['ID']);
	        if (count($highRevsCouponids) >= 2)
	            break;
	    }
	}
	
if(!empty($highRevsCouponids)){
	$rd_k = '';
	$rd_num = 0;
	$rc_num = 0;
    foreach ($highRevsCouponids as $key_id){
        if(isset( $Data_ids[$key_id])){
        	if(empty($data[$Data_ids[$key_id]]['CsgCode'])){
        		if($rd_num == 1)  continue;
        		$data[$Data_ids[$key_id]]['ooo'] = 'hireve';
            	$highRevsCouponData[] = $data[$Data_ids[$key_id]];
        		$rd_k = $key_id;
        		$rd_num++;
        	}else{
        		$data[$Data_ids[$key_id]]['ooo'] = 'hireve';
            	$highRevsCouponData[] = $data[$Data_ids[$key_id]];
            	unset($data[$Data_ids[$key_id]]);
            	unset($Data_ids[$key_id]);
        		$rc_num++;
        	}           
        }
    }
    if($rc_num == 0){
    	unset($data[$Data_ids[$rd_k]]);
    	unset($Data_ids[$rd_k]);
    }else{
    	foreach ($highRevsCouponData as $k => $v) {
    		if(empty($v['CsgCode'])){
    			unset($highRevsCouponData[$k]);
    		}
    	}
    }
}
	unset($__Revs);
	unset($__RevsOrder);
	unset($highRevsCouponids);

//最近15天添加的促销中按照%折扣倒序显示2个code（没有折扣度，任意2条code）；只有1条code时，只显示1条code，不显示deal；没有code时，只显示1条deal。 
/* 	$__fiftyOFFOrder =array();
	$__fiftyOFFCouponids = array();
	$__fiftyOFFCouponData =array();
	$__code = array();
	$__off = array();
	$i=0;

foreach ((array)$data as $vv){
    if(strtotime($vv['CsgAddTime'])>(time()-1296000)){
        $n = stripos($vv['CsgPromotionContent'] ,'percent');
        if($n !== false && isset($vv['CsgPromotionOff'])){
                $__fiftyOFFOrder[$i]['ID'] = $vv['ID'];
                $__fiftyOFFOrder[$i]['CsgCode'] = !empty($vv['CsgCode'])?1:0;
                $__fiftyOFFOrder[$i]['CsgPromotionOff'] = $vv['CsgPromotionOff'];
                
                $__code[] = $__fiftyOFFOrder[$i]['CsgCode'];
                $__off[] = $vv['CsgPromotionOff'];                       
        }else{
            $__fiftyOFFOrder[$i]['ID'] = $vv['ID'];
            $__fiftyOFFOrder[$i]['CsgCode'] = !empty($vv['CsgCode'])?1:0;
            $__fiftyOFFOrder[$i]['CsgPromotionOff'] = 0;
            
            $__code[] = $__fiftyOFFOrder[$i]['CsgCode'];
            $__off[] = 0;  
        }
    }
    $i++;
}

if(!empty($__fiftyOFFOrder)){
    array_multisort($__code,SORT_DESC,SORT_NUMERIC,$__off,SORT_DESC,SORT_NUMERIC,$__fiftyOFFOrder);
    //如果$__fiftyOFFOrder[1]的是code，则截取前两个数组，如果不是，则截取第一个数组
    if(isset($__fiftyOFFOrder[1]) && !empty($__fiftyOFFOrder[1]['CsgCode'])){       
        foreach ($__fiftyOFFOrder as $k=>$v){
            $__fiftyOFFCouponids[] = $v['ID'];
        if(count($__fiftyOFFCouponids)>=2){
                break;
            }
        }
    }else{
        if(isset($__fiftyOFFOrder[0])){
            $__fiftyOFFCouponids[] = $__fiftyOFFOrder[0]['ID'];
        }
    }
}


if(!empty($__fiftyOFFCouponids)){
    foreach ((array)$data as $k => $v) {
        if (in_array($v['ID'], $__fiftyOFFCouponids)) {
            $__fiftyOFFCouponData[$k] = $v;
            unset($data[$k]);
        }
    }
}

unset($__fiftyOFFOrder);
unset($__fiftyOFFCouponids);
unset($__code);
unset($__off); */
	//CsgClickCnt最高的促销，注：CsgClickCnt＞0。 1条
$highClicksCouponData = array();
$__clicks = array();
$highClicksCouponids = array();
	 foreach ((array)$data as $vv) {
	     if (isset($vv['CsgClickCnt']) && $vv['CsgClickCnt'])
	         $__clicks[$vv['ID']] = $vv['CsgClickCnt'];
	 }
	 
	 if (!empty($__clicks)) {
	     arsort($__clicks, SORT_NUMERIC);
	     foreach ($__clicks as $_k => $_v) {
	         array_push($highClicksCouponids, $_k);
	         if (count($highClicksCouponids) >= 1)
	             break;
	     }
	 }
	 
	 foreach ((array)$data as $k => $v) {
	     if (in_array($v['ID'], $highClicksCouponids)) {
	         $_couponClicks[$k] = $v['CsgClickCnt'];
	         $highClicksCouponData[$k] = $v;
	         $highClicksCouponData[$k]['ooo'] = 'click';
	         $highClicksCouponData[$k]['hot'] = 'hot';
	         unset($data[$k]);
	     }
	 }	 

//最新的2个code；只有1条code时，只显示1条code，不显示deal；没有code时，只显示1条deal。
$i = 0;
$_NewOrder = array();
$newTimeCouponData = array();
$__newtimes = array();
$newTimeCouponids = array();


$_time_order = array();
$_code_order = array();

foreach ($data as $k =>$vv){
    $_NewOrder[$i]['ID'] = $vv['ID'];
    $_NewOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
    $_NewOrder[$i]['CsgCode'] = $vv['CsgCode'];
    
    $_time_order[$i] =  $_NewOrder[$i]['CsgAddTime'];
    $_code_order[$i] = $vv['CsgCode'];
    
    $i++;
}
if(!empty($_NewOrder)){
    array_multisort($_code_order,SORT_DESC,SORT_STRING,$_time_order,SORT_DESC,SORT_NUMERIC,$_NewOrder);
    
   foreach ($_NewOrder as $k=>$v){
		if(!empty($v['CsgCode'])){
			if(isset($newTimeCouponids[0])) continue;
			$newTimeCouponids[0] = $v['ID'];
		}else{
			if(isset($newTimeCouponids[1])) continue;
			$newTimeCouponids[1] = $v['ID'];
		}
	    if($k >3){
	    	break;
	    }
	    if(count($newTimeCouponids)>=2){
	    	 break;
	    }
	}
}
if(!empty($newTimeCouponids)){
    foreach ($newTimeCouponids as $key_id){
        if(isset( $Data_ids[$key_id])){
        	$data[$Data_ids[$key_id]]['ooo'] = 'new';
        	$data[$Data_ids[$key_id]]['new'] = 'new';
            $newTimeCouponData[] = $data[$Data_ids[$key_id]];
            unset($data[$Data_ids[$key_id]]);
            unset($Data_ids[$key_id]);
        }
    }
}
//%最大的Code，如%相同挑选CsgAddTime最大的。（没有就挑选off最大的一条code）在没有就随便放条code 1条 
$i = 0;
$k = 0;
$_OffOrder = array();
$_csgOff =array();
$_offAddtime = array();
$_high_off_couponids=array();
$highOffOrderData = array();
	 foreach ($data as $k=>$vv){
	     $n = stripos($vv['CsgPromotionContent'] ,'percent');
	     if($n !== false && isset($vv['CsgPromotionOff']) && $vv['CsgPromotionOff'] && $vv['CsgCode']){
	             $_OffOrder[$i]['ID'] = $vv['ID'];
	             $_OffOrder[$i]['CsgPromotionOff'] = $vv['CsgPromotionOff'];
	             $_OffOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
	             
	             $_csgOff[$i] =  $vv['CsgPromotionOff'];
	             $_offAddtime[$i] = strtotime($vv['CsgAddTime']);
	         
	             $i++;
	     }
	            
	 }
	 if($i == 0){
	 	foreach ($data as $k=>$vv){
		     $n = stripos($vv['CsgPromotionContent'] ,'money');
		     if($n !== false && isset($vv['CsgPromotionOff']) && $vv['CsgPromotionOff'] && $vv['CsgCode']){
		             $_OffOrder[$i]['ID'] = $vv['ID'];
		             $_OffOrder[$i]['CsgPromotionOff'] = $vv['CsgPromotionOff'];
		             $_OffOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
		             
		             $_csgOff[$i] =  $vv['CsgPromotionOff'];
		             $_offAddtime[$i] = strtotime($vv['CsgAddTime']);
		         
		             $k++;
		     }
	            
	 	}
	 }
	 if(!empty($_OffOrder)){
	     array_multisort($_csgOff,SORT_DESC,SORT_STRING,$_offAddtime,SORT_DESC,SORT_STRING,$_OffOrder);
	     foreach ($_OffOrder as $v){
	         $_high_off_couponids[] = $v['ID'];
	         if(count($_high_off_couponids)>=1){
	             break;
	         }
	     }
	 }

	if(!empty($_high_off_couponids)){
	    foreach ((array)$data as $k => $v) {
	        if (in_array($v['ID'], $_high_off_couponids)) {
	            $highOffOrderData[$k] = $v;
	            $highOffOrderData[$k]['ooo'] = 'highoff';
	            unset($data[$k]);
	        }
	    }
	}else{
		foreach ((array)$data as $k => $v) {
	        if (!empty($v['CsgCode'])) {
	            $highOffOrderData[$k] = $v;
	            $highOffOrderData[$k]['ooo'] = 'hioff code';
	            unset($data[$k]);
	            break;
	        }
	    }
	}

// Free Shipping的Code，如多条为Free Shipping则挑选CsgAddTime最大的。 1条 
	 //dump($data);die;
	$__FreeOrder = array();
	 $newFreeCouponids = array();
	 $newFreeCouponData = array();
	 foreach ((array)$data as $vv) {
	     if (isset($vv['CsgPromotionDetail'])&& $vv['CsgCode'] && $vv['CsgPromotionDetail'] == 'free_shipping' )
	         $__FreeOrder[$vv['ID']] = strtotime($vv['CsgAddTime']);
	 }
	 if (!empty($__FreeOrder)) {
	     arsort($__FreeOrder, SORT_NUMERIC);
	     foreach ($__FreeOrder as $_k => $_v) {
	         array_push($newFreeCouponids, $_k);
	         if (count($newFreeCouponids) >= 1)
	             break;
	     }
	 }
	
	 foreach ((array)$data as $k => $v) {
	     if (in_array($v['ID'], $newFreeCouponids)) {
	         $_couponClicks[$k] = $v['CsgClickCnt'];
	         $newFreeCouponData[$k] = $v;
	         unset($data[$k]);
	     }
	 }
	

	
	//取click数组用来排序
	$click_sort_arr = array();
	$code_list = array();
	foreach($data as $k=>$v){
		$click_sort_arr[$k] = $v['CsgClickCnt'];
	}
	
	array_multisort($click_sort_arr, SORT_DESC, SORT_NUMERIC, $data);
	
	foreach ((array)$data as $k => $v) {
		if (!empty($v['CsgCode'])) {
			$couponData[] = $v;
		}
		else{
			$dealData[]=$v;
		}
	}
	
	$systemCoupon=array();
	foreach ($couponData as $k => $vo){
		if($vo['AddEditor']=="system"){
			unset($couponData[$k]);
			$systemCoupon[]=$vo;
		}
	}
	$couponData=array_merge($couponData,$systemCoupon);
	$systemDeal=array();
	foreach ($dealData as $k => $vo){
		if($vo['AddEditor']=="system"){
			unset($dealData[$k]);
			$systemDeal[]=$vo;
		}
	}
	$dealData=array_merge($dealData,$systemDeal);
	
	$r = array_merge($displayOrderData,$highRevsCouponData,$highClicksCouponData,$newTimeCouponData,$highOffOrderData,$newFreeCouponData, $couponData, $dealData);
	return $r;
}

function sortCouponData_NO($data) {
	$r = array();
	
	if (!is_array($data) || empty($data)) 
		return $r;

	#feed product coupon 显示规则
	$feed_coupon = array();
    foreach($data as $k=>$v){
        if($v['Source'] == 'AffFeed'){
            $feed_coupon[] = $v;
            unset($data[$k]);
        }
    }
    if(!empty($feed_coupon)){
    	$termObj = new Term();
    	$feed_coupon = $termObj->get_aff_feed_product_coupon($feed_coupon,2);
    	foreach($feed_coupon as $a=>$b){
    		$feed_coupon[$a] = aff_feed_coupon_title($b);
    	}
	}

	//取click数组用来排序 ,移除重复code
	#使用feed product的时候需要考虑是否去重
	$code_list = array();
	foreach($data as $k=>$v){
		if(!empty($v['CsgCode'])){
			$code_list[$v['CsgCode']][$k]=$v;
		}
	}
	
	if(!empty($code_list)){
		$del_code_key = array();
		foreach ($code_list as $k=>$v){
			if(count($v)<2) continue;
			
			$no_del_key = array();
			foreach($v as $dk=>$dv){
				if($dv['AddEditor'] != 'system'){
					$no_del_key[] = $dk;
					break;
				}
			}
			$t_all_key = array_keys($v);
			if(empty($no_del_key)){
				$no_del_key[] = $t_all_key[0];
			}
		
			$del_cp_ids = array_diff($t_all_key,$no_del_key);
			$del_code_key = array_merge($del_code_key,$del_cp_ids);
		}
		if(!empty($del_code_key)){
			foreach($del_code_key as $unk){
				unset($data[$unk]);
			}
		}
		$data =array_values($data);
	}

	$data = array_merge($data,$feed_coupon);
	
	
	$couponData = array();
	$dealData = array();
	$Data_ids = array();       //页面subterm包含的Coupon的ID
	
	foreach ($data as $k => $v){
	    if(!empty($v['ID'])){
	        $Data_ids[$v['ID']] =$k ;
	    }
	}

    //Displayorder为1、2的促销排在第一、二位，不受规则影响。
    //如果多条促销order相同，则挑选其中CsgClickCnt最高的1条，如CsgClickCnt相同，则挑选CsgAddTime最大的1条。2条（没有则从2开始）
	$__DisplayOrder = array();
	$highDispCouponids=array();
	$displayOrderData = array();
	
    $dis_clk = array();
    $dis_addtime = array();
    $dis_dis = array();
    $i = 0;

    foreach ((array)$data as $vv) {
        if (isset($vv['DisplayOrder']) && ($vv['DisplayOrder'] ==1 || $vv['DisplayOrder'] ==2)){
            $__DisplayOrder[$i]['ID'] = $vv['ID'];
            $__DisplayOrder[$i]['DisplayOrder'] = $vv['DisplayOrder'];
            $__DisplayOrder[$i]['CsgClickCnt'] = !empty($vv['CsgClickCnt'])?$vv['CsgClickCnt']:0;
            $__DisplayOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
            $dis_dis[]= $vv['DisplayOrder'];
            $dis_clk[] = !empty($vv['CsgClickCnt'])?$vv['CsgClickCnt']:0;
            $dis_addtime[] = strtotime($vv['CsgAddTime']);
            $i++;
        }
        
    }
    
    if(!empty($__DisplayOrder)){
        array_multisort($dis_dis,$dis_clk,SORT_DESC,$dis_addtime,SORT_DESC,$__DisplayOrder);
    
        foreach ($__DisplayOrder as $v){
            if(!isset($highDispCouponids[$v['DisplayOrder']])){
                $highDispCouponids[$v['DisplayOrder']]=$v['ID'];
            }
        }
    }   
    if(!empty($highDispCouponids)){       
        foreach ($highDispCouponids as $key_id){
            if(isset( $Data_ids[$key_id])){
            	$data[$Data_ids[$key_id]]['ooo'] = 'disp';
                $displayOrderData[] = $data[$Data_ids[$key_id]];
                unset($data[$Data_ids[$key_id]]);
                unset($Data_ids[$key_id]);
            }
        } 
    }
    unset($__DisplayOrder);
    unset($dis_dis);
    unset($dis_clk);
    unset($dis_addtime);
//CsgClickCnt最高的促销，注：CsgClickCnt＞0。 1条
$highClicksCouponData = array();
$__clicks = array();
$highClicksCouponids = array();
	 foreach ((array)$data as $vv) {
	     if (isset($vv['CsgClickCnt']) && $vv['CsgClickCnt'])
	         $__clicks[$vv['ID']] = $vv['CsgClickCnt'];
	 }
	 
	 if (!empty($__clicks)) {
	     arsort($__clicks, SORT_NUMERIC);
	     foreach ($__clicks as $_k => $_v) {
	         array_push($highClicksCouponids, $_k);
	         if (count($highClicksCouponids) >= 1)
	             break;
	     }
	 }
	 
	 foreach ((array)$data as $k => $v) {
	     if (in_array($v['ID'], $highClicksCouponids)) {
	         $_couponClicks[$k] = $v['CsgClickCnt'];
	         $v['ooo'] = 'click';
	         $v['hot'] = 'hot';
	         $highClicksCouponData[$k] = $v;
	         unset($data[$k]);
	     }
	 }	 
//最新的2个code；只有1条code时，只显示1条code，不显示deal；没有code时，只显示1条deal。
$i = 0;
$_NewOrder = array();
$newTimeCouponData = array();
$__newtimes = array();
$newTimeCouponids = array();


$_time_order = array();
$_code_order = array();

foreach ($data as $k =>$vv){
    $_NewOrder[$i]['ID'] = $vv['ID'];
    $_NewOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
    $_NewOrder[$i]['CsgCode'] = $vv['CsgCode'];
    
    $_time_order[$i] =  $_NewOrder[$i]['CsgAddTime'];
    $_code_order[$i] = $vv['CsgCode'];
    
    $i++;
}
if(!empty($_NewOrder)){
    array_multisort($_code_order,SORT_DESC,SORT_STRING,$_time_order,SORT_DESC,SORT_NUMERIC,$_NewOrder);
    
	foreach ($_NewOrder as $k=>$v){
		if(!empty($v['CsgCode'])){
			if(isset($newTimeCouponids[0])) continue;
			$newTimeCouponids[0] = $v['ID'];
		}else{
			if(isset($newTimeCouponids[1])) continue;
			$newTimeCouponids[1] = $v['ID'];
		}
	    if($k >3){
	    	break;
	    }
	    if(count($newTimeCouponids)>=2){
	    	 break;
	    }
	}
}
if(!empty($newTimeCouponids)){
    foreach ($newTimeCouponids as $key_id){
        if(isset( $Data_ids[$key_id])){
        	$data[$Data_ids[$key_id]]['ooo'] = 'new';
        	$data[$Data_ids[$key_id]]['new'] = 'new';
            $newTimeCouponData[] = $data[$Data_ids[$key_id]];
            unset($data[$Data_ids[$key_id]]);
            unset($Data_ids[$key_id]);
        }
    }
}


//%最大的Code，如%相同挑选CsgAddTime最大的。 1条 
$i = 0;
$k = 0;
$_OffOrder = array();
$_csgOff =array();
$_offAddtime = array();
$_high_off_couponids=array();
$highOffOrderData = array();
	 foreach ($data as $k=>$vv){
	     $n = stripos($vv['CsgPromotionContent'] ,'percent');
	     if($n !== false && isset($vv['CsgPromotionOff']) && $vv['CsgPromotionOff'] && $vv['CsgCode']){
	             $_OffOrder[$i]['ID'] = $vv['ID'];
	             $_OffOrder[$i]['CsgPromotionOff'] = $vv['CsgPromotionOff'];
	             $_OffOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
	             
	             $_csgOff[$i] =  $vv['CsgPromotionOff'];
	             $_offAddtime[$i] = strtotime($vv['CsgAddTime']);
	         
	             $i++;
	     }
	            
	 }
	 if($i == 0){
	 	foreach ($data as $k=>$vv){
		     $n = stripos($vv['CsgPromotionContent'] ,'money');
		     if($n !== false && isset($vv['CsgPromotionOff']) && $vv['CsgPromotionOff'] && $vv['CsgCode']){
		             $_OffOrder[$i]['ID'] = $vv['ID'];
		             $_OffOrder[$i]['CsgPromotionOff'] = $vv['CsgPromotionOff'];
		             $_OffOrder[$i]['CsgAddTime'] = strtotime($vv['CsgAddTime']);
		             
		             $_csgOff[$i] =  $vv['CsgPromotionOff'];
		             $_offAddtime[$i] = strtotime($vv['CsgAddTime']);
		         
		             $k++;
		     }
	            
	 	}
	 }
	 if(!empty($_OffOrder)){
	     array_multisort($_csgOff,SORT_DESC,SORT_STRING,$_offAddtime,SORT_DESC,SORT_STRING,$_OffOrder);
	     foreach ($_OffOrder as $v){
	         $_high_off_couponids[] = $v['ID'];
	         if(count($_high_off_couponids)>=1){
	             break;
	         }
	     }
	 }

	if(!empty($_high_off_couponids)){
	    foreach ((array)$data as $k => $v) {
	        if (in_array($v['ID'], $_high_off_couponids)) {
	            $highOffOrderData[$k] = $v;
	            $highOffOrderData[$k]['ooo'] = 'highoff';
	            unset($data[$k]);
	        }
	    }
	}else{
		foreach ((array)$data as $k => $v) {
	        if (!empty($v['CsgCode'])) {
	            $highOffOrderData[$k] = $v;
	            $highOffOrderData[$k]['ooo'] = 'hioff code';
	            unset($data[$k]);
	            break;
	        }
	    }
	}


//最近15天添加的促销中按照%折扣倒序显示2个code（没有折扣度，任意2条code）；只有1条code时，只显示1条code，不显示deal；没有code时，只显示1条deal。 
/* 	$__fiftyOFFOrder =array();
	$__fiftyOFFCouponids = array();
	$__fiftyOFFCouponData =array();
	$__code = array();
	$__off = array();
	$i=0;

foreach ((array)$data as $vv){
    if(strtotime($vv['CsgAddTime'])>(time()-1296000)){
        $n = stripos($vv['CsgPromotionContent'] ,'percent');
        if($n !== false && isset($vv['CsgPromotionOff'])){
                $__fiftyOFFOrder[$i]['ID'] = $vv['ID'];
                $__fiftyOFFOrder[$i]['CsgCode'] = !empty($vv['CsgCode'])?1:0;
                $__fiftyOFFOrder[$i]['CsgPromotionOff'] = $vv['CsgPromotionOff'];
                
                $__code[] = $__fiftyOFFOrder[$i]['CsgCode'];
                $__off[] = $vv['CsgPromotionOff'];                       
        }else{
            $__fiftyOFFOrder[$i]['ID'] = $vv['ID'];
            $__fiftyOFFOrder[$i]['CsgCode'] = !empty($vv['CsgCode'])?1:0;
            $__fiftyOFFOrder[$i]['CsgPromotionOff'] = 0;
            
            $__code[] = $__fiftyOFFOrder[$i]['CsgCode'];
            $__off[] = 0;  
        }
    }
    $i++;
}

if(!empty($__fiftyOFFOrder)){
    array_multisort($__code,SORT_DESC,SORT_NUMERIC,$__off,SORT_DESC,SORT_NUMERIC,$__fiftyOFFOrder);
    //如果$__fiftyOFFOrder[1]的是code，则截取前两个数组，如果不是，则截取第一个数组
    if(isset($__fiftyOFFOrder[1]) && !empty($__fiftyOFFOrder[1]['CsgCode'])){       
        foreach ($__fiftyOFFOrder as $k=>$v){
            $__fiftyOFFCouponids[] = $v['ID'];
        if(count($__fiftyOFFCouponids)>=2){
                break;
            }
        }
    }else{
        if(isset($__fiftyOFFOrder[0])){
            $__fiftyOFFCouponids[] = $__fiftyOFFOrder[0]['ID'];
        }
    }
}


if(!empty($__fiftyOFFCouponids)){
    foreach ((array)$data as $k => $v) {
        if (in_array($v['ID'], $__fiftyOFFCouponids)) {
            $__fiftyOFFCouponData[$k] = $v;
            unset($data[$k]);
        }
    }
}

unset($__fiftyOFFOrder);
unset($__fiftyOFFCouponids);
unset($__code);
unset($__off); */
	



// Free Shipping的Code，如多条为Free Shipping则挑选CsgAddTime最大的。 1条 
	 //dump($data);die;
	$__FreeOrder = array();
	 $newFreeCouponids = array();
	 $newFreeCouponData = array();
	 foreach ((array)$data as $vv) {
	     if (isset($vv['CsgPromotionDetail'])&& $vv['CsgCode'] && $vv['CsgPromotionDetail'] == 'free_shipping' )
	         $__FreeOrder[$vv['ID']] = strtotime($vv['CsgAddTime']);
	 }
	 if (!empty($__FreeOrder)) {
	     arsort($__FreeOrder, SORT_NUMERIC);
	     foreach ($__FreeOrder as $_k => $_v) {
	         array_push($newFreeCouponids, $_k);
	         if (count($newFreeCouponids) >= 1)
	             break;
	     }
	 }
	
	 foreach ((array)$data as $k => $v) {
	     if (in_array($v['ID'], $newFreeCouponids)) {
	         $_couponClicks[$k] = $v['CsgClickCnt'];
	         $newFreeCouponData[$k] = $v;
	         unset($data[$k]);
	     }
	 }
	

	
	//取click数组用来排序
	$click_sort_arr = array();
	$code_list = array();
	foreach($data as $k=>$v){
		$click_sort_arr[$k] = $v['CsgClickCnt'];
	}
	
	array_multisort($click_sort_arr, SORT_DESC, SORT_NUMERIC, $data);
	
	foreach ((array)$data as $k => $v) {
		if (!empty($v['CsgCode'])) {
			$couponData[] = $v;
		}
		else{
			$dealData[]=$v;
		}
	}
	
	$systemCoupon=array();
	foreach ($couponData as $k => $vo){
		if($vo['AddEditor']=="system"){
			unset($couponData[$k]);
			$systemCoupon[]=$vo;
		}
	}
	$couponData=array_merge($couponData,$systemCoupon);
	$systemDeal=array();
	foreach ($dealData as $k => $vo){
		if($vo['AddEditor']=="system"){
			unset($dealData[$k]);
			$systemDeal[]=$vo;
		}
	}
	$dealData=array_merge($dealData,$systemDeal);
	$r = array_merge($displayOrderData,$highClicksCouponData,$newTimeCouponData,$highOffOrderData,$newFreeCouponData, $couponData, $dealData);
	return $r;
}

function singleOtherTermData($data = array(), $source = array()) {
	if (!is_array($data) || empty($data))
		return $source;
	
	foreach ($data as $k => $v) {
		$url = strtolower(trim($v['RequestPath']));
		
		$is_exists = false;
		foreach ($source as $_v) {
			$s = strtolower(trim($_v['RequestPath']));
			if ($url == $s) {
				$is_exists = true;
				break;
			}
		}
		
		if (!$is_exists)
			$source[] = $v;
	}
	
	return $source;
}

function get_client_ip() {
    return isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"]? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER['REMOTE_ADDR'];    
}

function get_country_code_by_ip() {
	$ip = get_client_ip();
	
	if (empty($ip))
		return 'US';
	
	include_once INCLUDE_ROOT . 'lib/ip2country/geoip.inc';
	$gi = geoip_open(INCLUDE_ROOT . 'lib/ip2country/GeoIP.dat', GEOIP_STANDARD);
	$countryCode = geoip_country_code_by_addr($gi, $ip);
	geoip_close($gi);
	
	$termObj = new Term();
	$allCountryCodes = $termObj->get_all_country_code();
	
	if (empty($countryCode) || !in_array($countryCode, $allCountryCodes))
		return 'US';
	
	return $countryCode;
}

function addBlockName($arrCoupons, $blockName) {
	foreach ($arrCoupons as $key => $val) {
		$arrCoupons[$key]["BlockName"] =  $blockName;
	};
	return $arrCoupons;
}

function addBlockNameInTermCouponList(&$arrCoupons, $blockName) {
	foreach ($arrCoupons as $key => $val) {
		$arrCoupons[$key]['coupon_info']["BlockName"] =  $blockName;
	};
}

function checkEmailFormat($email) {
	$preg = preg_match("/\w+([-+.']\w+)*@\w+([-+']\w+)*\.\w+([-.]\w+)*/",trim($email));
	return $preg;
}

function format_tracking_blockName($str) {
	$str = preg_replace("/[^a-z\d]+/i", "-", $str);
	$str = trim($str,'-');
		//a-z0-9-_. would not be encode in urlencoding
		//% and + can not be replaced, since it might means an encoded character
//		$str = preg_replace("/[^a-z0-9\-_\.%+]+/i", "-",$str); 
	return $str;
}

function replace_meta($data, $template) {
	if (empty($data))
		return;
	
	
	//$from = array('[term name]', '[promo detail]', '[coupon cnt]', '[month]', '[year]');
	$from = array();
	$to = array();
	foreach ($data as $k => $v) {
		$from[] = '[' . $k . ']';
		$to[] = $v;
	}
	$r = str_replace($from, $to, $template);
	return $r;
}

function getTypeStr($type) {
	switch($type){
		case "STORE":
		case "STORE SINGLE COUNTRY":
		case "STORE MULTI COUNTRY":
			$type = 'STORE';
			break;
		case "BRAND":
		case "BRAND HAS OFFICALSITE":
			$type = 'BRAND';
			break;
		case "PRODUCT":
		case "STORE PRODUCT TYPE":
		case "OTHER":
			$type ='PRODUCT';
		case "HOLIDAY":
			$type = "HOLIDAY";
			break;
	}
	return $type;
}

function clickCntTypeAddTime_cmp($coupon_a, $coupon_b){
	$arrCmpFactor = array('CsgClickCnt','CsgType','CsgAddTime');
	foreach ($arrCmpFactor as $v) {
		if ($v == 'type') {
			if ($coupon_a[ $v ]  > $coupon_b[ $v ]) {
				return 1;
			}elseif ($coupon_a[ $v ] < $coupon_b[ $v ]){
				return -1;
			}else {
				continue;
			}
		}else {
			if ($coupon_a[ $v ]  < $coupon_b[ $v ]) {
				return 1;
			}elseif ($coupon_a[ $v ] > $coupon_b[ $v ]){
				return -1;
			}else {
				continue;
			}
		}
	}
	return 0;
}

function generate_promotion_detail($promotion_detail, $promotion_off, $lang) {
	if (!$promotion_detail || !$promotion_off)
		return;

	$objLang = new Language();
	$objLang->setLang($lang);
	$word_dict = $objLang->get_word_dict($lang);
	$currency = array("dollar"=>"$", "euro"=>"€", "pound"=>"£", "rmb"=>"¥", "rupee"=>"Rs", "naira"=>"NGN");
	if (strpos($promotion_detail, 'money') !== false){
		list($money, $type) = explode(',', $promotion_detail);
		$r =  $promotion_off.$currency[$type]  ;
	}
	elseif (strpos($promotion_detail, 'percent') !== false) {
		$r = $promotion_off . '% ';
	}else{
		$r = '';
	}
	return $r;
}

function log_404($msg) {
	if (empty($msg))
		return false;

	$msg = date('Y-m-d H:i:s') . "\t\t" . $msg . "\n";
	$filename = INCLUDE_ROOT . 'data/404_log.txt';
	$fp = fopen($filename, 'a+'); 
	if(!$fp)
		return false;
	
	fwrite($fp, $msg);
	fclose ($fp);
}
function log_403($msg) {
	if (empty($msg))
		return false;

	$msg = date('Y-m-d H:i:s') . "\t\t" . $msg . "\n";
	$filename = INCLUDE_ROOT . 'data/403_log.txt';
	$fp = fopen($filename, 'a+'); 
	if(!$fp)
		return false;
	
	fwrite($fp, $msg);
	fclose ($fp);
}
function change_sys_set_by_lang($lang){
	global $month,$global_word_tab,$next3days_month;
	$objLang = new Language();

	$global_word_tab['month'] = $month = $objLang->getMonthWord($lang,$month);
	$next3days_month = $objLang->getMonthWord($lang,$next3days_month);
}
//根据key 语言获取值
function get_lang_value_by_key($key,$lang){
	$objLang = new Language();
	$content = $objLang->getLib();
	return $content[$lang][$key];
}

//coupon单元 左边logo替换成的文字输出
function get_coupon_display_logo($data){
	$promotiondetail = $data['CsgPromotionDetail'];
	$promotionoff = $data['CsgPromotionOff'];
	$PromotionContent = $data['CsgPromotionContent'];
	$code = $data['CsgCode'];
	$des = $data['CsgTitle'];
	$iscoupon = $code?true:false;
	$displaylogo = array();
	if(empty($promotiondetail)){
		if(!$iscoupon){
			$tmp = array("<i>Deal</i>","<i>Angebot</i>");
			shuffle($tmp);
			$displaylogo['word'] = $tmp[0];
			
		}else{
			$displaylogo['word'] = '<i>Gutschein</i>';
		}
		$displaylogo['type'] = 6;
	}

	if(empty($PromotionContent)){
		#原逻辑 
		$promotiontype = array(
			"bngn"=>'<i>1+1</i>', 
			"bogo"=>'<i>bogo</i>', 
			"free_gift"=>'<i>Gratis</i><p>Geschenk<p>',  
			"free_shipping"=>'<i>Gratis</i><p>Versand<p>', 
			"money"=>"", 
			"percent"=>"",	
		);

		$currency = array("dollar"=>"$", "euro"=>"&euro;", "pound"=>"&pound;", "rmb"=>"&yen;", "rupee"=>"Rs", "naira"=>"NGN");

		
		#make title

		if(!empty($promotiondetail)){
			if(strpos($promotiondetail, "money")===0){
				list($money, $type) = explode(",", $promotiondetail);
				if(!empty($type) && isset($currency[$type])){
					if($type == "euro"){
						$displaylogo['word'] = "<i>".$promotionoff.$currency[$type]."</i>" ." <p>Rabatt<p>";
					}else{
						$displaylogo['word'] = "<i>".$currency[$type] . $promotionoff."</i>" ." <p>Rabatt<p>";
					}
					$displaylogo['type'] = 1;
				}
			}elseif (isset($promotiontype[$promotiondetail])){
				if ($promotiondetail == "percent") {
					$displaylogo['word'] = "<i>".$promotionoff . "% "."</i>". " <p>Rabatt<p>";
				}else {
					if($promotiondetail == "bngn"){
						$displaylogo['word'] = $promotiontype[$promotiondetail];
						$displaylogo['type'] = 7;
					}elseif ($promotiondetail == "bogo") {
						$displaylogo['word'] = $promotiontype[$promotiondetail];
						$displaylogo['type'] = 7;
					}elseif ($promotiondetail == "free_gift") {
						$displaylogo['word'] = $promotiontype[$promotiondetail];
						$displaylogo['type'] = 4;
					}elseif ($promotiondetail == "free_shipping") {
						$displaylogo['word'] = $promotiontype[$promotiondetail];
						$displaylogo['type'] = 3;
					}
				}
			}
		}
	}else{
		#当有$PromotionContent时。拆分$PromotionContent获取title
		if($promotiondetail == 'other'){
			if(!$iscoupon){
				$tmp = array("<i>Deal</i>","<i>Angebot</i>");
				shuffle($tmp);
				$displaylogo['word'] = $tmp[0];
				
			}else{
				$displaylogo['word'] = '<i>Gutschein</i>';
			}
			$displaylogo['type'] = 6;
		}else{
			$arr_tmp_promotion_content = explode("\n",$PromotionContent);
			$promotiontype = array(
				"bngn"=>'<i>1+1</i>', 
				"bogo"=>'<i>bogo</i>', 
				"free_gift"=>'<i>Gratis</i><p>Geschenk<p>',  
				"free_shipping"=>"<i>Gratis</i><p>Versand<p>", 
				"money"=>"", 
				"percent"=>"",	
			);
			$str = $arr_tmp_promotion_content[0];
			list($type,$words) = explode('|',$str);
			$type_arr = explode(':',$type);
			//die(dump($type_arr));
			$currency = array("dollar"=>"$", "euro"=>"&euro;", "pound"=>"&pound;", "rmb"=>"&yen;", "rupee"=>"Rs", "naira"=>"NGN");
			$iscoupon = $code?true:false;
			if($type_arr[0] == 'money'){
				if($type_arr[1] == "euro"){
					$displaylogo['word'] = "<i>".$type_arr[2].$currency[$type_arr[1]]."</i>" ." <p>Rabatt<p>";
				}else{
					$displaylogo['word'] = "<i>".$currency[$type_arr[1]] . $type_arr[2]."</i>" . " <p>Rabatt<p>"; 
				}
				$displaylogo['type'] = 1;
			}elseif($type_arr[0] == 'from'){      
				preg_match("/ab\s*([0-9|\,|\€\£]+)/i",$des,$match);
				if(!empty($match)){
					$tmp = substr($match[0],0,2);
					$tmp2 = substr($match[0],3);
					$displaylogo['word'] = "<p>".$tmp."</p>"."<i>".$tmp2."</i>";
					$displaylogo['type'] = 5;
				}
			}elseif($type_arr[0] == 'percent'){
				$displaylogo['word'] = "<i>".$type_arr[1] . "% "."</i>"." <p>Rabatt<p>";	
				$displaylogo['type'] = 2;
			}else{
				if($type_arr[0] == "bngn"){
					$displaylogo['word'] = $promotiontype[$type_arr[0]];
					$displaylogo['type'] = 7;
				}elseif ($type_arr[0] == "bogo") {
					$displaylogo['word'] = $promotiontype[$type_arr[0]];
					$displaylogo['type'] = 7;
				}elseif ($type_arr[0] == "free_gift") {
					$displaylogo['word'] = $promotiontype[$type_arr[0]];
					$displaylogo['type'] = 4;
				}elseif ($type_arr[0] == "free_shipping") {
					$displaylogo['word'] = $promotiontype[$type_arr[0]];
					$displaylogo['type'] = 3;
				}
			}
		}
	}
	if(isset($displaylogo) && !empty($displaylogo)){
		preg_match("/ab nur\s*([0-9|\,|\€\£]+)/i",$des,$match);
		if(!empty($match)){
			$tmp = substr($match[0],0,6);
			$tmp2 = substr($match[0],7);
			$displaylogo['word'] = "<p>".$tmp."</p>"."<i>".$tmp2."</i>";
			$displaylogo['type'] = 5;
		}else{
			preg_match("/ab\s*([0-9|\,|\€\£]+)/i",$des,$match1);
			if($displaylogo['type'] != 1 && $displaylogo['type'] != 2){
				if(!empty($match1)){
					$tmp = substr($match1[0],0,2);
					$tmp2 = substr($match1[0],3);
					$displaylogo['word'] = "<p>".$tmp."</p>"."<i>".$tmp2."</i>";
					$displaylogo['type'] = 5;
				}
			}
		}
		return $displaylogo;
	}else{
		preg_match("/ab\s*([0-9|\,|\€\£]+)/i",$des,$match);
		if(!empty($match)){
			$tmp = substr($match[0],0,2);
			$tmp2 = substr($match[0],3);
			$displaylogo['word'] = "<p>".$tmp."</p>"."<i>".$tmp2."</i>";
			$displaylogo['type'] = 5;
		}else{
			preg_match("/nur\s*([0-9|\,|\€\£]+)/i",$des,$match1);
			if(!empty($match1)){
				$tmp = substr($match1[0],0,3);
				$tmp2 = substr($match1[0],3);
				$displaylogo['word'] = "<p>".$tmp."</p>"."<i>".$tmp2."</i>";
				$displaylogo['type'] = 5;
			}else{
				if(!$iscoupon){
					$tmp = array("<i>Deal</i>","<i>Angebot</i>");
					shuffle($tmp);
					$displaylogo['word'] = $tmp[0];
					
				}else{
					$displaylogo['word'] = '<i>Gutschein</i>';
				}
				$displaylogo['type'] = 6;
			}
		}
		return $displaylogo;
	}
}

function dump($array){
	echo "<pre>";
	print_r($array);
}
function test_for_nginx(){
	if(isset($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'],'nginx')){
		if(!empty($_SERVER['QUERY_STRING'])){
			$replace = "?".$_SERVER['QUERY_STRING'];
			$_SERVER['SCRIPT_URL'] = str_replace($replace,"",$_SERVER['REQUEST_URI']);
		}else{
			$_SERVER['SCRIPT_URL'] = $_SERVER['REQUEST_URI'];
		}
		return true;
	}
	return false;
}