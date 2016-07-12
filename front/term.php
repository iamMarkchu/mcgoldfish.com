<?php
defined('IN_DS')    die('Hacking attempt');

define('D_PAGE_NAME', 'MERCHANT');
if (!(int)$_opt_data_id)
	goto_404();
$canonical_uri = $_rewriteUrlInfo['RequestPath'];
define("D_PAGE_VALUE",	$canonical_uri);
include_once 'verify.php';
include_once 'tracking_block.php';
include_once INCLUDE_ROOT . 'etc/term_meta.php';
// set cache
$cacheFileName = MEM_PREX . 'term_' . $_opt_data_id;
$CpCacheFileName = $cacheFileName."_cp";
$objCache = new Cache($cacheFileName);
if (DEBUG_MODE || isset($_GET['forcerefresh'])){
	$mainContent = '';
}else{
	$mainContent = $objCache->getCache();
	set_batch_impressions($objCache->getCacheByKey($CpCacheFileName));
}
if (!$mainContent) {
	$objCache->initialCache();
	$termObj = new Term();
	$termInfo = $termObj->get_term_by_id($_opt_data_id, array('status' => 'active'));
	if (empty($termInfo))
		goto_404();
	if (!empty($termInfo['Alias']))
	    $termInfo['Name']=$termInfo['Alias'];
	$termInfo['Name'] = trim(preg_replace("/( DE| Deutschland)$/i ", "", $termInfo['Name']));
	/*if(in_array(strtolower($termInfo['Name'][0]), array('a','h','n'))){
		$tpl->assign('abtest','abtest');
	}*/
	$termInfoByCountry[$termInfo['ID']] = $termInfo;
	$primaryCatetory = $termObj->get_term_primary_category($termInfo['ID']);
	$objCategory = new Category();
	if(!empty($primaryCatetory)){
		$category_data = $termObj->get_term_related_category($primaryCatetory['ID']);
	}else{
		$category_data = $objCategory->get_top_category('de');
	}
	if(empty($category_data)) $category_data = $objCategory->get_top_category('de');
	 $tpl->assign('category_data', $category_data);
	//修改系统和语言相关的设置
	global $default_lang;
	change_sys_set_by_lang($default_lang);
	//coupon list
	$arrCoupons = array();
	$termids = array();
	$validCouponsCnt = 0;
	$promoDetail = '';
	$promoOff = 0;
	$display_coupon_alert = false;
	$all_coupon_ids = array();
	foreach ($termInfoByCountry as $k => $v) {
		$termids[] = $v['ID'];
		$_coupons_info = $termObj->get_coupons_by_term_id_track($k,D_PAGE_NAME,D_PAGE_VALUE);  //获取term下的coupon
		$_coupons = $_coupons_info['coupon_info'];
		$all_coupon_ids = array_merge($all_coupon_ids,$_coupons_info['coupon_ids']);
		$arrCoupons[$k]['display_coupon_alert'] = false;
		if (empty($_coupons))
			continue;
		if (!$display_coupon_alert) {
			$arrCoupons[$k]['display_coupon_alert'] = false;
			$display_coupon_alert = false;
		}
		$arrCoupons[$k]['terminfo'] = $v;
		$arrCoupons[$k]['coupons'] = $_coupons;
		//$arrCoupons[$k]['coupons'] = addBlockName($arrCoupons[$k]['coupons'], format_tracking_blockName( $v['CountryCode']." PromoS" ) );
		$validCouponsCnt += count($arrCoupons[$k]['coupons']);
		foreach ($arrCoupons[$k]['coupons'] as $_v) {
			if ($_v['CsgPromotionDetail'] && !preg_match('/from/', $_v['CsgPromotionDetail'])) {
				if ($_v['CsgPromotionOff'] > $promoOff) {
					$promoOff = $_v['CsgPromotionOff'];
					$promoDetail = $_v['CsgPromotionDetail'];
				}
			}	
		}
	}

	//die(dump($arrCoupons));
	if(!isset($_GET['forcerefresh'])){  //记录coupon展现数据
		set_batch_impressions($all_coupon_ids);
	}
	$objCache->setCacheByKey($CpCacheFileName,$all_coupon_ids);
	$coupon_list = array();
	$code_num=0;
	foreach ($arrCoupons as $k => $v) {
		foreach ($v as $kk => $vv) {
			if ($kk == 'coupons') {
				$coupon_list[$k][$kk] = $termInfo['HasAffiliate'] == 'yes'?sortCouponData($vv):sortCouponData_NO($vv);                    //coupon sort
				foreach ($vv as $vvv) {
					if (!empty($vvv['CsgCode'])){
					   $code_num++;
					}
				}
				$v['terminfo']['CodeNums']=$code_num;
				//term页面中除了store类型。其他页面促销最多显示35条
				if($termInfo['Type'] != 'STORE'){
					$coupon_list[$k][$kk] = array_slice($coupon_list[$k][$kk], 0,35);
				}
			}
			else {
				$coupon_list[$k][$kk] = $vv;
			}
		}		
	}
	$termids = $termInfo['ID'];
	$expired_coupons_list = array();
	//如果商家为有联盟商家，则挑选该term最近1个月内Revenue最高的3条促销，根据revenue值倒叙排列。
	if($termInfo['HasAffiliate'] == 'yes' && $termInfo['IsDisplayExpireCoupon'] == 'yes' ){
		 $expired_coupons_ids = $termObj->get_expired_coupon_ids_in_month($termids);
	     $expired_coupons_three_ids =$termObj->get_three_expired_coupons_order_revenue_V2($expired_coupons_ids);
	     if(empty($expired_coupons_three_ids)){
	     	$expired_coupons_three_ids = $termObj->get_expired_coupon_ids($termids,$limit=3);
	     }
	     $expired_coupons_list = $termObj->get_expired_coupons_list_by_ids($expired_coupons_three_ids);
	}
	//如果商家为无联盟商家，且在线coupon小于5，则取最近过期的5条促销
	if($validCouponsCnt < 5 && $termInfo['HasAffiliate'] == 'no' && $termInfo['IsDisplayExpireCoupon'] == 'yes'){
		$expired_coupons_ids = $termObj->get_expired_coupon_ids($termids);
		$expired_coupons_list = $termObj->get_expired_coupons_list_by_ids($expired_coupons_ids,'order');
	}
	//similar_term 
	$similar_term_data = $termObj->get_interest_data($termInfo['ID'],$termInfo['CountryCode']);
	$tpl->assign('similar_term_data', $similar_term_data);
	//get term hot stores
	$similar_term = array();
	foreach ($similar_term_data as $v) {
		$similar_term[] = $v['ID'];
	}
	$relatedStores = $termObj->get_related_term($termInfo['ID']);
	foreach ($relatedStores as $k => $v) {
		if(in_array($v['superId'],$similar_term)){
			unset($relatedStores[$k]);
		}
	}
	$relatedStores_2 = array_slice($relatedStores, 0, 9);
	$tpl->assign('featured_stores', $relatedStores_2);

	//canonical url
	$canonical_url = SITE_URL . $canonical_uri;
	$tpl->assign('canonical_uri', $canonical_uri);
	$tpl->assign('canonical_url', $canonical_url);
	$lang = "de";
	$lang_key = $lang;
	if(!empty($termInfo['PageH1'])){
		$lang_key = $lang."_h";
	}
	$rand_key = '';
	if($promoOff > 0 && $code_num > 0){
		if((int)$termInfo['ID']%2 == 1){
			$rand_key = 31;
		}else{
			$rand_key = 32;
		}
	}elseif($promoOff > 0) {
		if((int)$termInfo['ID']%2 == 1){
			$rand_key = 33;
		}else{
			$rand_key = 32;
		}
	}elseif($code_num > 0){
		$rand_key = 42;
	}else{
		$rand_key = 1;
	}
	$title_template = $term_meta_ini[$lang_key]['MetaTitle'][$rand_key];
	if ($promoOff > 0 && $validCouponsCnt) {
		$rand_key = rand(0, 3);
		$desc_template = $term_meta_ini[$lang_key]['MetaDesc'][$rand_key];
	}
	else {
		$desc_template = $term_meta_ini[$lang_key]['MetaDesc'][4];
	}
	$keyword_template = $term_meta_ini[$lang_key]['MetaKeyword'];
	$termInfo['DomainUrl'] = empty($termInfo['DomainUrl'])?$termInfo['Name']:$termInfo['DomainUrl'];
	$deal_num = $validCouponsCnt - $code_num;

	$from = array('term name' => $termInfo['Name'], 
				  /*'coupon title' => $arrDefaultCouponTitle[$cp_tit_key], */    //暂时不需要coupontitle
				  'promo detail' => generate_promotion_detail($promoDetail, $promoOff, $lang), 
				  'coupons cnt' => $validCouponsCnt, 
				  'deals cnt'  	=> $deal_num,
				  'code cnt'    => $code_num,
				  'month' => $next3days_month, 
				  'year' => $year,
				  'term pageh1' => trim($termInfo['PageH1']),
				 'domain url' => trim($termInfo['DomainUrl']),
	);
	//判断人工处理meta by aaron
	if(!empty($_rewriteUrlInfo['PageMetaId'])){
		$meta_obj = $termObj->get_meta_info_by_id($_rewriteUrlInfo['PageMetaId']);
		if(!empty($meta_obj)){
			if(!empty($meta_obj['MetaTitle'])) $title_template = $meta_obj['MetaTitle'];
			if(!empty($meta_obj['MetaKeyword'])) $keyword_template = $meta_obj['MetaKeyword'];
			if(!empty($meta_obj['MetaDesc'])) $desc_template = $meta_obj['MetaDesc'];
		}
	}
	$meta = array(
		'MetaTitle' => replace_meta($from, $title_template),
		'MetaDesc' => replace_meta($from, $desc_template), 
		'MetaKeyword' => replace_meta($from, $keyword_template),
	);
	$page_header = array(
		'meta' => $meta,
		'css' => array_merge($default_css, array('couponlist.css?' .VER)),
		'js' => $default_js,
	);
	$paths = array();
	$home_lang = get_lang_value_by_key("HOME",$lang);
	$paths[] = array('name' => $home_lang, 'url' => '/');
	
	$termNameFirstChar = substr(strtoupper(trim($termInfo['Name'])), 0, 1);
	if (ord($termNameFirstChar) < 65 || ord($termNameFirstChar) > 90) {
		$termNameFirstChar = 'Autre';
	}
	if (!empty($primaryCatetory) && !empty($primaryCatetory['url'])) {
		$cateObj = new Category();
		$cate_to_top = $cateObj->get_parent_category_to_top($primaryCatetory);
		foreach ($cate_to_top as $v) {
			$paths[] = array('name' => $v['DisplayName'], 'url' => $v['url']);
		}
	}
	else {
		$paths[] = array('name' => 'Shops', 'url' => '/stores/');
		$paths[] = array('name' => $termNameFirstChar, 'url' => '/stores/' . $termNameFirstChar . '/');
	}
	$lastUpdate = '';
	//显示模板描述
	$objLanguage=new Language();
	$couponNums = count($coupon_list[$termInfo['ID']]['coupons']);
	$lastUpdateTime_year =date("Y");
	$lastUpdateTime_month =$objLanguage->getMonthWordShort($default_lang, date("M"));
	$lastUpdateTime_day = date("j");
    if($lastUpdateTime_day==01){         
       $lastUpdateTime_day="first day";
    }else{
       $lastUpdateTime_day=$lastUpdateTime_day;
    }
  	$lastUpdateTime=$lastUpdateTime_day.". ".$lastUpdateTime_month." ".$lastUpdateTime_year;
  	$lastUpdate = $lastUpdateTime_month." ".$lastUpdateTime_year;
 	if(!empty($termInfo['PageH1'])){
      $term_desc=$termInfo['PageH1'];
      $desStr="";
  	}else{
      $term_desc=$termInfo['Name'];
      $desStr="  Gutschein Code";
    }
    $special_term = array("83791","42782","42921","44532","42837");
    if(in_array($termInfo['ID'], $special_term)){
    	$s_term = file(INCLUDE_ROOT."etc/special_term.txt");
    	foreach ($s_term as $k => $v) {
    		$tmp = explode("/", $v);
    		if($tmp[0] == $termInfo['ID']){
    			$flag = preg_replace("/\{Total Coupon Count\}/", "<em>{$couponNums}</em>", $tmp[1]);
    			$termInfo['Sketch'] = $flag."<span>Letztes Up-Date {$lastUpdateTime}</span>";
    			break;
    		}
    	}
    	$tpl->assign("no_recommand",1);
    }else{
    	$sssk = '';
    	if(!empty($termInfo['PageH1'])){
    		$sssk =$termInfo['PageH1'];
    	}else{
    		$sssk =$termInfo['Name'] .' Gutscheine';
    	}
    	$termInfo['Sketch'] = "Wir haben insgesamt <em>{$couponNums}</em> neue {$sssk} &  Angebote für dich zusammen gestellt und finden täglich die besten Angebote für dich raus. Melde dich auch für unseren Newsletter an, um stets up-to-date über die neusten Gutscheine zu bleiben.<span>Letztes Up-Date {$lastUpdateTime}</span>";
    }
	//判断是否有H1，并作为面包屑
    if(!empty($termInfo['PageH1'])){
    		$path_name = $termInfo['PageH1'].' Letztes Up-Date '.$lastUpdateTime;
    }else{
        $path_name = $termInfo['Name'];
        $path_name = $path_name.' Gutschein Letztes Up-Date '.$lastUpdateTime;
    }
	$paths[] = array('name' => $path_name, 'url' => '');
	//hot shop for term 
	$blshop = $termObj->get_Beliebte_Shops_term($_opt_data_id);
	//取前6个显示图片
	$iblshop = array();
	foreach ($blshop as $k => $v) {
		if($k ==6) break;
		$iblshop[] = $v;
		unset($blshop[$k]);
	}
	$tpl->assign('iblshop',$iblshop);
	$tpl->assign('blshop',$blshop);
	$recent_launched_data = $termObj->get_Beliebte_Shops($_opt_data_id);
	$tpl->assign('p_cate',$primaryCatetory);
	$tpl->assign('recent_launched_data', $recent_launched_data);
	$tpl->assign('page_header', $page_header);
	$tpl->assign('termInfo', $termInfo);
	$tpl->assign('paths', $paths);
	$tpl->assign('coupon_list', $coupon_list);
	$tpl->assign('expired_coupons_list', $expired_coupons_list);
	$tpl->display('de_term.html');
	$mainContent = $objCache->endCache();
}
echo $mainContent;




