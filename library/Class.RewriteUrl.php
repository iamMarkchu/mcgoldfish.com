<?php
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

}