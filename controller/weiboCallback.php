<?php 
$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
}
$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
$uid_get = $c->get_uid();
$uid = $uid_get['uid'];
$user_message = $c->show_user_by_id( $uid);

$sql = "SELECT * FROM `outuser` WHERE `id` = ".$uid;
$result = $GLOBALS['db']->getFirstRow($sql);
if(empty($result)){
    $sql = sprintf("INSERT INTO `outuser` (`id`,`nickname`,`userimage`,`source`,`addtime`) values ('%s', '%s', '%s', 'sina', '%s')", $user_message['idstr'], $user_message['name'], $user_message['profile_image_url'], date('Y-m-d H:i:s'));
    $GLOBALS['db']->query($sql);
}

header('Location: /');