<?php
	session_start();
	define('IN_DS', true);
	if(!defined('INCLUDE_ROOT')){
		define('INCLUDE_ROOT',dirname(dirname(__FILE__))."/");
	}
	include_once(INCLUDE_ROOT . 'func/front.denyip.func.php');
	$mmdenyid = new Memcache;
	$mmdenyid->addServer("localhost", "11211");
	$clientip = rm_get_clientip();
	if($_POST){
		/** Validate captcha */
		if (!empty($_REQUEST['captcha'])) {
			if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {
				echo ('Invalid captcha');
				exit;
			}
			unset($_SESSION['captcha']);
		}else{
			echo ('Invalid captcha');
			exit;
		}	
		rm_add_allowed_ip($clientip);
		$url = $_REQUEST['url'];
		header("Location:".$url);
		exit;
	}

	if(rm_is_need_deny($clientip)){
       $return_url = $_SERVER['REQUEST_URI'];
       $ip = $clientip;
	   $agent = $_SERVER['HTTP_USER_AGENT'];
	   log_403($_SERVER['SCRIPT_URL'] . "\t" . $ip . "\t" . $agent);
       $page_403_php = "403_func.php";
       include_once INCLUDE_ROOT."front/" . $page_403_php;
       exit;
	}
?>