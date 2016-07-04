<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}

class RewriteUrl 
{
	function get_rewrite_url_by_path($path) {
		if (!$path)
			return;
		
		$sql = "select * from `rewrite_url` where `requestpath` = '" . addslashes($path) . "'";
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);
		return $r;
	}
	
	function get_rewrite_url_by_path_1($path) {
		if (!$path)
			return;
		
		$sql = "select * from `rewrite_url` where `RequestPath` = '" . addslashes($path) . "'";
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);
		
		return $r;
	}
	
	function get_rewrite_url_by_id($id) {
		if (!(int)$id)
			return;
		
		$sql = "select * from `rewrite_url` where `ID` = " . addslashes($id);
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);
		
		return $r;
	}

	function get_obj_url_data_final($ModelType,$OptDataId){
		$sql = 'SELECT * FROM rewrite_url WHERE ModelType = "'.$ModelType.'" AND OptDataId = '.intval($OptDataId).' AND isActivation = "yes"';	
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);

		if(empty($r)){
			return false;
		}
		
		$jumpId = $r['ID'];
		$i=0;
		do{
			$sql = 'SELECT * FROM rewrite_url WHERE isActivation = "yes" AND ID = '.intval($jumpId);	
			$q = $GLOBALS['db']->query($sql);
			$r = $GLOBALS['db']->getRow($q);

			if(!$r){
				break;
			}
			if($r['IsJump'] == '301' || $r['IsJump'] == '302' || $r['IsJump'] == 'HIJACK'){
				$jumpId = $r['JumpRewriteUrlID'];
			}else{
				$jumpId = 0;
			}
			$i++;
			if($i>10)break;
		}while($jumpId);
		

		if(empty($r)){
			return false;
		}

		if($r['IsJump'] == '404'){
			return false;
		}elseif($r['IsJump'] == '404'){
			return false;
		}elseif($r['IsJump'] == 'NO'){
			return $r;
		}
	}
	
	function get_obj_url_data_final_new($ModelType,$OptDataId){
		
			$sql = 'SELECT * FROM rewrite_url WHERE ModelType = "'.$ModelType.'" AND OptDataId = '.intval($OptDataId).' AND isActivation = "yes"  ';
			$q = $GLOBALS['db']->query($sql);
			$r = $GLOBALS['db']->getRow($q);
			if($r['IsJump'] == 'NO'){
				return $r;
				break;
			}else{
				$jumpId = $r['JumpRewriteUrlID'];
				for($i=0; $i<3; $i++){
					if(empty($r['JumpRewriteUrlID'])) break;
					$sql = 'SELECT * FROM rewrite_url WHERE ID ='.$r['JumpRewriteUrlID'];
					$q = $GLOBALS['db']->query($sql);
					$r = $GLOBALS['db']->getRow($q);
					if($r['IsJump'] != 'NO'){
						continue;
					}else{
						return $r;
						break;
					}
				}
		 }
		return false;
		
	
	}
	
	function check_active_url_exists($url) {
		if (!$url)
			return false;
		
		$sql = "select * from rewrite_url where RequestPath = '" . addslashes($url) . "' and (IsJump = 'no' or IsJump = '') and isActivation = 'yes'";
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);
		
		if (!$r)
			return false;
			
		return true;
	}
	
	function get_active_url($optdataid, $type) {
		if (!(int)$optdataid || empty($type))
			return;
		
		$sql = "select * from rewrite_url where OptDataId = " . addslashes($optdataid) . " and ModelType = '" . addslashes($type) . "' and (IsJump = 'no' or IsJump = '') and isActivation = 'yes'";
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);
		
		return $r;
	}
	
	function get_letters_url($type = 'store') {
		if ($type == 'store')
			$path = 'stores';
		elseif ($type == 'product')
			$path = 'products';
		elseif ($type == 'brand')
			$path = 'brands';
		elseif ($tpye == 'holiday')
			$path = 'holidays';
		
		if (!isset($path))
			return;
		
		$r = array();
		for ($i = ord('a'); $i<= ord('z'); $i++) {
			$char = chr($i);
			$charUpper = strtoupper($char);
			$uri = '/' . $path . '/' . $charUpper . '/';
			
			// if (!$this->check_active_url_exists($uri))
			// 	continue;
			
			$r[$char] = array(
				'title' => $charUpper,
				'url' => $uri
			);
		}
		
		$otherUri = '/' . $path . '/Other/';
		if (!$this->check_active_url_exists($otherUri)) {
			$r['other'] = array(
				'title' => 'Other',
				'url' => $otherUri
			);
		}
		
		return $r;
	}
	
	function insert_data($data) {
		if (empty($data))
			return;
		
		$sql = "insert into rewrite_url(RequestPath, ModelType, OptDataId, IsJump, JumpRewriteUrlID, isActivation, UrlType) values (
				'" . addslashes($data['RequestPath']) . "', 
				'" . addslashes($data['ModelType']) . "', 
				'" . addslashes($data['OptDataId']) . "', 
				'" . addslashes($data['IsJump']) . "', 
				'" . addslashes($data['JumpRewriteUrlID']) . "', 
				'yes', 
				'OLD'
		)";
		$db_master = new Mysql(DB_NAME_MASTER, DB_HOST_MASTER, DB_USER_MASTER, DB_PASS_MASTER);
		if(!empty($db_master) && $db_master->linkID){
			$q = $GLOBALS['db_master']->query($sql);
		}
		return true;
	}
}