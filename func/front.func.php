<?php
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
		
		if (isset($_r['isjump']) && in_array($_r['isjump'], array(301, 302, 404, 'HIJACK'))) {
			$prev_rewrite = $_r;
		}else {
			if (!$_r) {
				$_r = $prev_rewrite;
			}
			if (isset($prev_rewrite)) {
				if ($prev_rewrite['isjump'] == 301 || $prev_rewrite['isjump'] == 'HIJACK') {
					permanent_header($_r['RequestPath'] . $left_url_str);
				}
				elseif ($prev_rewrite['isjump'] == 302) {
					temporarily_header($_r['requestpath'] . $left_url_str);
				}
				elseif ($prev_rewrite['isjump'] == 404) {
					goto_404($_r['requestpath'] . $left_url_str);
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

function goto_404(){
	$ip = get_client_ip();
	$agent = $_SERVER['HTTP_USER_AGENT'];
	log_404($_SERVER['SCRIPT_URL'] . "\t" . $ip . "\t" . $agent);
	
	header('HTTP/1.1 404 Not Found');
	header('Cache-Control: no-cache');
	
	include_once FRONT_DIR . '404_func.php';
	exit;
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

function checkEmailFormat($email) {
	$preg = preg_match("/\w+([-+.']\w+)*@\w+([-+']\w+)*\.\w+([-.]\w+)*/",trim($email));
	return $preg;
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