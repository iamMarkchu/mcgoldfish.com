<?php 
include_once dirname(dirname(__FILE__)) . '/initiate.php';
if(strtoupper($_SERVER['REQUEST_METHOD']) == "POST" ){
	$data['content'] = addslashes(trim($_POST['comment']));
	$data['username'] = addslashes(trim($_POST['username']));
	$data['email'] = addslashes(trim($_POST['useremail']));
	$data['optdataid'] = $_POST['articleid'];
	$data['addtime'] = date('Y-m-d H:i:s');
	$data['status'] = 'republish';
	if(isset($_POST['parentcommentid'])) $data['parentcommentid'] = $_POST['parentcommentid'];
	if(isset($_POST['sourcetype'])) $data['sourcetype'] = $_POST['sourcetype'];
	$comment = new Comment();
	$flag = $comment->addComment($data);
	echo $flag;
}