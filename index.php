<?php
error_reporting(1);
define('IN_DS', true);
include_once dirname(__FILE__) . '/initiate.php';
$script_uri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
$_rewriteUrlInfo = dispatch_constant_url($script_uri);
if($_rewriteUrlInfo){
	$_model_type = $_rewriteUrlInfo['ModelType'];
	include_once FRONT_DIR . $_model_type . '.php';
	exit;	
}elseif ($script_uri == '/about-us/') {
	include_once FRONT_DIR . 'about_us.php';
}
elseif ($script_uri == '/contact-us/') {
	include_once FRONT_DIR . 'contact_us.php';
}
elseif ($script_uri == '/privacy-policy/') {
	include_once FRONT_DIR . 'privacy_policy.php';
}else{
	$_rewriteUrlInfo = dispatch_url($script_uri);
	$_model_type = $_rewriteUrlInfo['ModelType'];
	$_opt_data_id = isset($_rewriteUrlInfo['OptDataId']) ? $_rewriteUrlInfo['OptDataId'] : '';
	if (in_array($_model_type, array('homepage','article','category'))) {
		include_once FRONT_DIR . $_model_type . '.php';
		exit;
	}
	goto_404();
}
