<?php
error_reporting(1);
define('IN_DS', true);
include_once dirname(__FILE__) . '/initiate.php';
test_for_nginx();
$script_uri = isset($_SERVER['SCRIPT_URL'])?$_SERVER['SCRIPT_URL']:'';
if ($script_uri == '/about-us/') {
	include_once FRONT_DIR . 'about_us.php';
}
elseif ($script_uri == '/contact-us/') {
	include_once FRONT_DIR . 'contact_us.php';
}
elseif ($script_uri == '/privacy-policy/') {
	include_once FRONT_DIR . 'privacy_policy.php';
}else{
	$_rewriteUrlInfo = dispatch_url($script_uri);
	$_model_type = $_rewriteUrlInfo['modeltype'];
	$_opt_data_id = isset($_rewriteUrlInfo['optdataid']) ? $_rewriteUrlInfo['optdataid'] : '';
	if(isset($_model_type)) include_once FRONT_DIR . $_model_type . '.php';
		else goto_404();
}
