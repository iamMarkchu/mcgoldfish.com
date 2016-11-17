<?php 
if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
	if(isset($_POST['action']) && $_POST['action'] == 'do-article-vote'){
		if(isset($_POST['id']) && is_numeric($_POST['id'])){
			@session_start();
			$id = intval($_POST['id']);
			if(isset($_SESSION['article_voted_'.$id])){
				echo '0';
				exit;
			}else{
				$_SESSION['article_voted_'.$id] = 1;				
			}
			$sql = sprintf("UPDATE `article` SET `votecount` = `votecount` + 1 WHERE `id` = '%d'", $id);
			$flag = $GLOBALS['db']->query($sql);
			echo $flag;
			exit;
		}
	}
}