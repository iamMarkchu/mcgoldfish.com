<?php
defined('IN_DS') or die('Hacking attempt');
if(USE_TRACKING){
	include_once 'v4/incoming.php';
	include_once 'v4/pv.php';
	filter_source_tag();
}

