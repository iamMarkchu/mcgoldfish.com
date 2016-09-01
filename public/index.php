<?php
/**
 * composer自动加载机制
 */
include_once dirname(dirname(__FILE__)) . '/vendor/autoload.php'; 
/**
 * 初始化配置
 */
include_once dirname(dirname(__FILE__)) . '/config/initiate.php';
/**
 * 初始化帮助类完成路由操作
 */
$httpObj  = new HttpManager();
if(isset($route[$httpObj->script_uri]))
	include_once FRONT_DIR.$route[$httpObj->script_uri].".php";
else
	$urlInfo = $httpObj->dispatch_url();
if(isset($urlInfo)){
	$modelType = $urlInfo['modeltype'];
	$optDataId = $urlInfo['optdataid'];
	if(isset($modelType)) include_once FRONT_DIR . $modelType . '.php';
		else goto_404();
}
exit;