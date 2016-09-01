<?php 
class HttpManager 
{
	var $script_uri = '';
	function __construct(){
		error_reporting(1);
		$this->test_for_nginx();
		$this->script_uri = isset($_SERVER['SCRIPT_URL'])?$_SERVER['SCRIPT_URL']:'';
	}
	protected function test_for_nginx(){
		if(isset($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'],'nginx')){
			if(!empty($_SERVER['QUERY_STRING'])){
				$replace = "?".$_SERVER['QUERY_STRING'];
				$_SERVER['SCRIPT_URL'] = str_replace($replace,"",$_SERVER['REQUEST_URI']);
			}else{
				$_SERVER['SCRIPT_URL'] = $_SERVER['REQUEST_URI'];
			}
			return true;
		}
		return false;
	}
	public function dispatch_url() { 
		$url_path = $this->script_uri;
		$r = array();
		$old_url_path = $url_path;
		$qu_mark_pos = strpos($url_path, '?');
		$and_mark_pos = strpos($url_path, '&');

		if ($url_path == '/' || empty($url_path)) {
			$r['modeltype'] = 'homepage';
			return $r;
		}
		elseif ($qu_mark_pos !== false) {
			$left_url_str = substr($url_path, $qu_mark_pos);
			$url_path = substr($url_path, 0, $qu_mark_pos);	
		}
		elseif ($and_mark_pos !== false) {
			$left_url_str = substr($url_path, $and_mark_pos);
			$url_path = substr($url_path, 0, $and_mark_pos);
		}
		
		$rewrite_url_obj = new RewriteUrl();
		$ii = 0;
		do {
			if (!isset($_r)) {
				$_r = $rewrite_url_obj->get_rewrite_url_by_path($url_path);
				if (!$_r) {
					goto_404();
				}
				if($_r['status'] == "no") {
					goto_404();
				}
			}else {
				if($_r['status'] == "no") {
					goto_404();
				}
				$_r = $rewrite_url_obj->get_rewrite_url_by_id($_r['JumpRewriteUrlID']);
			}
			
			if (isset($_r['IsJump']) && in_array($_r['IsJump'], array(301, 302, 404, 'HIJACK'))) {
				$prev_rewrite = $_r;
			}else {
				if (!$_r) {
					$_r = $prev_rewrite;
				}
				
				if (isset($prev_rewrite)) {
					if ($prev_rewrite['IsJump'] == 301 || $prev_rewrite['IsJump'] == 'HIJACK') {
						permanent_header($_r['RequestPath'] . $left_url_str);
					}
					elseif ($prev_rewrite['IsJump'] == 302) {
						temporarily_header($_r['RequestPath'] . $left_url_str);
					}
					elseif ($prev_rewrite['IsJump'] == 404) {
						goto_404($_r['RequestPath'] . $left_url_str);
					}
				}
				break;
			}
			
			$ii++;
			if($ii > 5){
				file_put_contents(INCLUDE_ROOT."data/data_a.txt", $url_path,FILE_APPEND);
				goto_404();
			}
		} while(true);
		
		if ($_r['status'] == 'no')
			goto_404();
			
		$r = $_r;
		if (!empty($r) && $url_path != $r['requestpath']) {
			permanent_header($r['requestpath']);
			exit;
		}
		return $r;
	}
}