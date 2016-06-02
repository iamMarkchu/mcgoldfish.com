<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}
define('D_PAGE_NAME', 'DE_TOPIC');
$canonical_uri = "http://de.savingstory.com/gutschein/weihnachten.html";

define("D_PAGE_VALUE",	$canonical_uri);

include_once 'tracking_block.php';
// set cache
$cacheFileName = MEM_PREX . 'topic_'.$_rewriteUrlInfo['OptDataId'];

$objCache = new Cache($cacheFileName);

if (DEBUG_MODE || isset($_GET['forcerefresh'])){
    $mainContent = '';
}else{
    $mainContent = $objCache->getCache();
}

if (!$mainContent) {
	$objCache->initialCache();
    $termObj = new Term();
    $weihnachten_id = 87389;  //online id = 87389
	$termInfo = $termObj->get_term_by_id($weihnachten_id);
	if (empty($termInfo))
		goto_404();
	$termInfoByCountry[$termInfo['ID']] = $termInfo;
	//指定需要展示的term和couponid
	$term_coupn_array = array('39520'=>'5502506,3107655',
						'42782'=>'3917937,5154379,4730174',
						'83539'=>'5408891,6020905',
						'35146'=>'4505413,5994275',
						'35394'=>'5663806,5015587',
						'50902'=>'5871427,5871424',
						'35139'=>'5370099,5583932',
						'82556'=>'3868289,3868283',
						'81717'=>'3249523,3804437',
						'86008'=>'5579449,4730554',
						'35592'=>'2803734,5544160',
						'35601'=>'5553288,6003084,5900199',
						'35602'=>'6013664,5985344,3458501',
						'35500'=>'5867117,5597879',
						);
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
		$_coupons_info = $termObj->get_coupons_by_termids_asd($term_coupn_array,D_PAGE_NAME,D_PAGE_VALUE); 
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
		$arrCoupons[$k]['coupons'] = addBlockName($arrCoupons[$k]['coupons'], format_tracking_blockName( $v['CountryCode']." PromoS" ) );
		$validCouponsCnt += count($arrCoupons[$k]['coupons']);
		foreach ($arrCoupons[$k]['coupons'] as $_v) {
			if ($_v['CsgPromotionDetail']) {
				if ($_v['CsgPromotionOff'] > $promoOff) {
					$promoOff = $_v['CsgPromotionOff'];
					$promoDetail = $_v['CsgPromotionDetail'];
				}
			}	
		}
	}

	if(!isset($_GET['forcerefresh'])){  //记录coupon展现数据
		set_batch_impressions($all_coupon_ids);
	}
	$objCache->setCacheByKey($CpCacheFileName,$all_coupon_ids);
	$coupon_list = array();
	$other_related_coupons = array();
	$code_num=0;
	foreach ($arrCoupons as $k => $v) {
		foreach ($v as $kk => $vv) {
			if ($kk == 'coupons') {
				$coupon_list[$k][$kk] = sortCouponData($vv);                    //coupon sort
				foreach ($vv as $vvv) {
					if (isset($vvv['other_term']) && !empty($vvv['other_term'])) {
						$other_related_coupons = singleOtherTermData($vvv['other_term'], $other_related_coupons);
					}
					if (!empty($vvv['CsgCode'])){
					   $code_num++;
					}
				}
				$v['terminfo']['CodeNums']=$code_num;
			}
			else {
				$coupon_list[$k][$kk] = $vv;
			}
		}		
	}
	$from = array('term name' => str_replace(" DE","",$termInfo['Name']), 
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
	//get term hot stores and related stores
	$relatedStores = $termObj->get_related_term($termInfo['ID']);
	$relatedStores_2 = array_slice($relatedStores, 0, 9);
	//canonical url
	$canonical_url = SITE_URL . $canonical_uri;
	$tpl->assign('canonical_uri', $canonical_uri);
	$tpl->assign('canonical_url', $canonical_url);
	$lastUpdate = '';
	//显示模板描述
	 if ($default_lang=='de'){
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
        	  }
        	  else{
        	      $term_desc=$termInfo['Name'];
        	      $desStr="  Gutschein Code";
        	  }
        	  //die(dump($termInfo));
        	  $termInfo['Sketch'] = "Wir haben insgesamt <em>{$couponNums}</em> neue {$termInfo['PageH1']} Gutscheine &  Angebote für dich zusammen gestellt und finden täglich die besten Angebote für dich raus. Melde dich auch für unseren Newsletter an, um stets up-to-date über die neusten Gutscheine zu bleiben.<span>Letztes Up-Date {$lastUpdateTime}</span>";
	}
	$metadesc = array("Bis zu {$from['promo detail']} Rabatt auf Weihnachtsgeschenke im Online Shop erhalten",
					"Den besten Gutschein zu Weihnachten mit bis zu {$from['promo detail']}Rabatt findest nur bei Saving Story"
		);
	shuffle($metadesc);
	$meta = array(
	    'MetaTitle' =>"{$termInfo['PageH1']} Frohe Weihnachten {$from['year']} - Die günstigsten Geschenke zu Weihnachten ",
	    'MetaDesc' => $metadesc[0],
	    'MetaKeyword' => "Weihnachts Gutscheine Dezember {$from['year']}, Weihnachten Rabattaktion",
	);
	
		
	$page_header = array(
	    'lang' => 'de',
	    'meta' => $meta,
	);
	#get block recent launched data
	$limit=15;
	$objTerm = new Term();
	$recent_launched_data = $objTerm->get_home_page_new_term($limit);
	$tpl->assign('recent_launched_data', $recent_launched_data);
	$homepage_id = array("35111","35139","81717","83539","35109","35258","35487");
	shuffle($homepage_id);
	$re_term_id = $homepage_id[0];
	$relatedStores = $objTerm->get_related_term($re_term_id);
	$relatedStores = array_slice($relatedStores, 0, 9);
	$tpl->assign('featured_stores', $relatedStores_2);
	$tpl->assign('page_header', $page_header);
	$tpl->assign('coupon_list', $coupon_list);
	$tpl->assign('termInfo', $termInfo);
	$tpl->assign('paths', $paths);
	$tpl->assign("canonical_uri",$canonical_uri);

	$tpl->display("de_topic.html");
	$mainContent = $objCache->endCache();
}
	echo $mainContent;		
