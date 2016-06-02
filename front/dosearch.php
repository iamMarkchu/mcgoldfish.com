<?php
define('IN_DS', true);

include_once dirname(dirname(__FILE__)) . '/initiate.php';
$keyword = urlencode(urlencode(trim(get_get_var('keyword'))));
$dstUrl = '/' . "search/".$keyword."/";
permanent_header($dstUrl);
exit;
?>
