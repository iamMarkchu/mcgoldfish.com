<?php
class PageMeta
{
	function get_home_meta(){
		$r['MetaTitle'] = $GLOBALS['site_url_short'].' : php技术博客,记录菜鸟成长之路 '.$GLOBALS['year'];
		$r['MetaDesc'] = $GLOBALS['site_url_normal'].' 是一个博客类网站,主要内容涵盖了以下几方面:PHP,JS,CSS,HTML以及其他有关服务器环境搭建的知识,如果有任何问题请联系我.';
        $r['MetaKeyword'] = 'PHP教程,PHP初学者,开发环境搭建';
		return $r;
	}
	function get_article_meta($info,$modeltype='article'){
		if(empty($info)) return array();
		$optdataid = $info['id'];
		$sql = "select * from page_meta where optdataid = '{$optdataid}' and modeltype='{$modeltype}'";
		$result = $GLOBALS['db']->getFirstRow($sql);
		if(!empty($result)){
			$r['MetaTitle'] = $result['pagetitle'];
			$r['MetaDesc'] = $result['pagedescription'];
			$r['pagekeyword'] = $result['MetaKeyword'];
			return $r;
		}else{
			return array();
		}
	}
}