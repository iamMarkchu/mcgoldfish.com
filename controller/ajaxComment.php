<?php
if(strtoupper($_SERVER['REQUEST_METHOD']) == "POST" ){
	$data['content'] = addslashes(trim($_POST['comment']));
	$data['username'] = addslashes(trim($_POST['username']));
	$data['email'] = addslashes(trim($_POST['useremail']));
	$data['optdataid'] = isset($_POST['articleid'])?$_POST['articleid']:0;
	$data['addtime'] = date('Y-m-d H:i:s');
	$data['status'] = 'republish';
	if(isset($_POST['parentcommentid'])) $data['parentcommentid'] = $_POST['parentcommentid'];
	if(isset($_POST['sourcetype'])) $data['sourcetype'] = $_POST['sourcetype'];
	$comment = new Comment();
	$flag = $comment->addComment($data);
	header("Location: ".$_SERVER['HTTP_REFERER']);
}