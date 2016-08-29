<?php
include_once dirname(dirname(__FILE__)) . '/vendor/autoload.php'; 
include_once dirname(dirname(__FILE__)) . '/etc/initiate.php';
$httpObj  = new HttpManager();
if(isset($route[$httpObj->script_uri]))
	include_once FRONT_DIR.$route[$httpObj->script_uri].".php";
else
	$urlInfo = $httpObj->dispatch_url();
if(isset($urlInfo)){
	$modelType = $urlInfo['modeltype'];
	if(isset($modelType)) include_once FRONT_DIR . $modelType . '.php';
		else goto_404();
}