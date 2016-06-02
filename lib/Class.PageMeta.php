<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}

class PageMeta
{
	function get_home_meta(){
		$sql = 'SELECT pm.* FROM rewrite_url AS ru LEFT JOIN page_meta AS pm ON ru.PageMetaId = pm.ID WHERE ru.RequestPath = "/" ';
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);
		if(!$r){
			return array();
		}
		if($r['TermplateId']){
			$sql = 'SELECT MetaTitle,MetaDesc,MetaKeyword,PageH1 FROM page_meta WHERE ID = '.intval($r['TermplateId']);
			$q = $GLOBALS['db']->query($sql);
			$r = $GLOBALS['db']->getRow($q);
		}
		$r['MetaTitle'] = $r['MetaTitle']?$r['MetaTitle']:'[new_site_str1]: Online Angebote und Gutscheine [year]';
		$r['MetaDesc'] = $r['MetaDesc']?$r['MetaDesc']:'Alle neuen Gutscheine und Sonderaktionen findest du auf Saving Story. Günstig online shoppen war noch nie so leicht. Jetzt Gutscheincode holen und sparen.';

        $r['MetaKeyword'] = $r['MetaKeyword']?$r['MetaKeyword']:'Gutscheine [year], Aktuelle Rabatte und Sonderaktionen';
		$this->replace_meta_param($r);
		return $r;
	}

	function replace_meta_param(&$r,$replace_data=array(),$type='now'){
		if($type == 'now'){
			$w_l = '[';
			$w_r = ']';
		}else{
			$w_l = '<';
			$w_r = '>';
		}
		

		$sql = 'SELECT * FROM `data_dictionary` WHERE Pid = 50';
		$q = $GLOBALS['db']->getRows($sql);

		#system data dictionary
		if(!empty($q)){
			$replace_from = array();
			$replace_to = array();
			foreach($q as $v){
				$replace_from[] = $v['Name'];
				$replace_to[] = $v['Code'];
			}

			
			$r['MetaTitle'] = str_replace($replace_from, $replace_to, $r['MetaTitle']);
			$r['MetaDesc'] = str_replace($replace_from, $replace_to, $r['MetaDesc']);
			$r['MetaKeyword'] = str_replace($replace_from, $replace_to, $r['MetaKeyword']);
			$r['PageH1'] = str_replace($replace_from, $replace_to, $r['PageH1']);
		}

		#system global words
		global $global_word_tab;
		if(!empty($global_word_tab)){
			foreach($global_word_tab as $k=>$v){
				$replace_from[] = $w_l.$k.$w_r;
				$replace_to[] = $v;
			}
			$r['MetaTitle'] = str_replace($replace_from, $replace_to, $r['MetaTitle']);
			$r['MetaDesc'] = str_replace($replace_from, $replace_to, $r['MetaDesc']);
			$r['MetaKeyword'] = str_replace($replace_from, $replace_to, $r['MetaKeyword']);
			$r['PageH1'] = str_replace($replace_from, $replace_to, $r['PageH1']);
		}
		

		#replace data process
		if(!empty($replace_data)){
			foreach($replace_data as $k=>$v){
				$replace_from[] = $w_l.$k.$w_r;
				$replace_to[] = $v;
			}
			$r['MetaTitle'] = str_replace($replace_from, $replace_to, $r['MetaTitle']);
			$r['MetaDesc'] = str_replace($replace_from, $replace_to, $r['MetaDesc']);
			$r['MetaKeyword'] = str_replace($replace_from, $replace_to, $r['MetaKeyword']);
			$r['PageH1'] = str_replace($replace_from, $replace_to, $r['PageH1']);
		}
	}

	function get_meta($id){
		if(empty($id)){
			return array();
		}

		$sql = 'SELECT MetaTitle,MetaDesc,MetaKeyword,PageH1,TermplateId FROM page_meta WHERE ID = '.$id;
		$q = $GLOBALS['db']->query($sql);
		$r = $GLOBALS['db']->getRow($q);

		if($r['TermplateId']){
			$sql = 'SELECT MetaTitle,MetaDesc,MetaKeyword,PageH1 FROM page_meta WHERE ID = '.intval($r['TermplateId']);
			$q = $GLOBALS['db']->query($sql);
			$r = $GLOBALS['db']->getRow($q);
		}


		$this->replace_meta_param($r);
		return $r;
	}

}