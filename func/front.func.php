<?php
defined('IN_DS') or die('Hacking attempt');

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
		$r['modeltype'] = 'homepage';
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
function makeShortDesc($string){
	if(empty($string)) return '';
	$shorDesc = mbStrSplit($string,150);
	return strip_tags($shorDesc)."...";
}
function mbStrSplit ($string, $len=1) {
  $start = 0;
  $strlen = mb_strlen($string);
  while ($strlen) {
    $array[] = mb_substr($string,$start,$len,"utf8");
    $string = mb_substr($string, $len, $strlen,"utf8");
    $strlen = mb_strlen($string);
  }
  return $array[0];
}