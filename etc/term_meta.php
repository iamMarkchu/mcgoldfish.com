<?php

$on_data = date("Y-m-d H");
if(strcmp($on_data,"2014-12-28 00")>0 && strcmp("2015-01-04 22",$on_data)>0){
	$en_meta_title = array(
			'[term name] Promo Code for [month] [year]: [promo detail] [term name] New Year [coupon title]', 
			'[term name] Promo Code New Year for [month] [year]', 
		);
	$uk_meta_title = array("New Year [promo detail] [term name] Discount Codes & Vouchers for [month] [year]");
}else{
	$en_meta_title = array(
			'[term name] Promo Code [month] [year]: Get [promo detail] w/ [term name] [coupon title]',
			'[term name] Promo Code [month] [year]',
		);
	$uk_meta_title = array("[promo detail] [term name] Discount Codes & Voucher Codes [month] [year]");
}

// $en_meta_title = array(
// 		'[term name] Promo Code [month] [year]: Get [promo detail] w/ [term name] [coupon title]',
// 		'[term name] Promo Code [month] [year]',
// );
// $uk_meta_title = array("[promo detail] [term name] Discount Codes & Voucher Codes [month] [year]");

$term_meta_ini = array(
	'en' => array(
		'MetaTitle' => $en_meta_title, 
		'MetaDesc' => array(
			'Save with a [promo detail] [term name] coupon code and other free promo code, discount voucher at '.SITE_DOMAIN_END.'. There are[coupons cnt] [domain url] coupons available in [month] [year].', 
			'[coupons cnt] available [term name] coupons on '.SITE_DOMAIN_END.'. Top Promo Code: Get [promo detail] Code. Save more with [domain url] coupon codes and discounts in [month] [year].', 
			'Get a [promo detail] [term name] coupon code or promo code from '.SITE_DOMAIN_END.'. [domain url] has [coupons cnt] coupons & discount vouchers in [month] [year].', 
			'Find the latest [coupons cnt] [term name] promo codes, coupons, discounts in [month] [year]. Receive [promo detail] [domain url] coupon.', 
			'Find the latest [term name] promo codes, coupons, discounts in [month] [year]. Get a free [domain url] coupon.', 
		), 
		'MetaKeyword' => '[term name] Promo Code [month] [year], [term name] Promotional Codes', 
	), 
	
	'en_h' => array(
			'MetaTitle' => array(
			'[term pageh1] [month] [year]: Get [promo detail] w/ [term name] [coupon title]',
			'[term pageh1] [month] [year]',
			),
			'MetaDesc' => array(
					'Save with a [promo detail] [term name] coupon code and other free promo code, discount voucher at '.SITE_DOMAIN_END.'. There are[coupons cnt] [domain url] coupons available in [month] [year].',
					'[coupons cnt] available [term name] coupons on '.SITE_DOMAIN_END.'. Top Promo Code: Get [promo detail] Code. Save more with [domain url] coupon codes and discounts in [month] [year].',
					'Get a [promo detail] [term name] coupon code or promo code from '.SITE_DOMAIN_END.'. [domain url] has [coupons cnt] coupons & discount vouchers in [month] [year].',
					'Find the latest [coupons cnt] [term name] promo codes, coupons, discounts in [month] [year]. Receive [promo detail] [domain url] coupon.',
					'Find the latest [term name] promo codes, coupons, discounts in [month] [year]. Get a free [domain url] coupon.',
			),
			'MetaKeyword' => '[term name] Promo Code [month] [year], [term name] Promotional Codes',
	),
	
	'de' => array(
		'MetaTitle' => array(
					'[term name] Gutschein [month] [year]: Hol\' dir [coupons cnt] Gutscheincodes von [term name]',
					'[term name] Gutschein [month] [year]',
					'[term name] Gutschein [month] [year] -SavingStory',
					'[term name] Gutschein [month] [year] -Nutze [coupons cnt] neue Gutscheine & Angebote',
					'[term name] Gutschein [month] [year] -Hol dir [coupons cnt] neue Gutscheine & Angebote',
					'[month] [year] [term name] Gutschein - Aktuell [coupons cnt] Gutscheine & Angebote',
					'[month] [year] [term name] Gutschein - [coupons cnt] Aktive Online Gutscheine',
					'[month] [year] [term name] Gutschein - Die Besten [coupons cnt] Online Codes',
					'Gutschein [term name] [month] - [coupons cnt] Gutscheine & Angebote',
					'Gutschein [term name] [month] - [coupons cnt] Gutscheine & Deals',
					'[term name] Gutschein - [month] [year] geprüft',
					'[term name] Rabatt Gutschein - Nutze [coupons cnt] Aktive Gutscheine & Angebote',
					'[term name] - Neuster Gutschein [month] [year]',
					'[term name] Gutschein - [month] [year] Geprüft',
					'[term name]\'s Beste Gutschein für [month] [year]',
					'[term name] Gutschein [month] [year] - Günstig shoppen',
					'[month] [year] - [term name] Online Gutschein',
					'[month] [year] Geprüft - [term name] Gutschein',
					'[month] [year] [term name] Gutschein - Saving Story',
					'[coupons cnt] Aktive Gutscheine & Deals - [term name] Code [month] [year]',
					'[coupons cnt] Aktive Gutscheine & Deals - [term name]  [year] Rabatt',
					'Neuste [term name] Gutscheine für [month] [year]',
					'[month] [year] Getestet - [term name] Gutschein',
					'[promo detail] Rabatt Gutschein - [term name] Saving Story',
					'[month] [year] [promo detail] [term name] Rabatt Gutschein - Saving Story',
					'[promo detail] Rabatt Gutschein - [term name] [month]',
					'[promo detail] Rabatt Gutschein - [term name] [year]',
					'[promo detail] Rabatt Gutschein von [term name] - Saving Story',
					'[promo detail] Rabatt für [month] [year] bei [term name] - Saving Story',
					'[term name][promo detail] Rabatt Gutschein [month] [year]' ,
					'[term name] Gutschein - Spare bis zu [promo detail] bei Saving Story',
					'[month] [year] [term name] Gutschein :Spare bis zu [promo detail]',
					'[month] [year] Bis zu -[promo detail] mit [term name] Gutschein - Saving Story',
					'Bis zu [promo detail] Rabatt mit [term name] Gutschein - Saving Story',
					'[promo detail] Rabatt mit [term name] Gutschein - Saving Story',
					'Sicher dir [term name] Gutschein - [promo detail] Rabatt',
					'[term name]\'s [promo detail] Rabatt  Gutschein - Saving Story',
					'Bester [term name] Gutschein - Bis zu [promo detail] Rabatt im [month] [year]',
					'[term name] Rabatt Gutschein - [promo detail] Rabatt',
					'[term name] Online Gutschein - Bis zu [promo detail] im [month] [year] sparen',
					'[term name] Aktive Online Gutschein | [promo detail] Rabatt',
					'[term name] Gutschein [month] [year] -Bis zu [promo detail] sparen',
					'[term name] Gutschein [month] [year] - [code cnt] Gutscheine & [deals cnt] Angebote',
					'Gutschein [term name] [month] - [code cnt] Gutscheine & [deals cnt] Deals',
					'[year] [term name] Gutschein | [code cnt] Gutscheincodes & [deals cnt] Deals',
		), 
		'MetaDesc' => array(
			'Spare mit einem [promo detail] [term name] Gutscheincode und anderen gratis Gutscheincodes, Rabattgutscheine [coupons cnt] [term name] Gutscheine verfügbar [month] [year].', 
			'Es sind jetzt [coupons cnt] Gutscheine [term name] verfügbar. Bester Gutschein: Hole [promo detail] Code. Spare mehr mit [term name] Gutscheincodes und Rabatten für [month] [year].', 
			'Hol\' dir einen [promo detail] [term name] Gutschein oder Deal. [term name] hat [coupons cnt] Gutscheincodes & Rabattgutscheine für [month] [year]', 
			'Finde die neusten [coupons cnt] [term name] Gutscheincodes, Gutscheine, Rabatte für [month] [year] und erhalte [promo detail] bei [term name].', 
			'Finde die neusten [term name] Gutscheincodes, Gutscheine, Rabatte für [month] [year]. Sicher dir einen kostenlosen [term name] Gutschein.',
		), 
		'MetaKeyword' => '[term name] Gutschein [month] [year], [term name] Aktionscodes', 
	), 
	'de_h' => array(
			'MetaTitle' => array(
					'[term pageh1] [month] [year]: Hol\' dir [coupons cnt] Gutscheincodes von [term name]',
					'[term pageh1] [month] [year]',
					'[term pageh1] [month] [year] -SavingStory',
					'[term pageh1] [month] [year] -Nutze [coupons cnt] neue Gutscheine & Angebote',
					'[term pageh1] [month] [year] -Hol dir [coupons cnt] neue Gutscheine & Angebote',
					'[month] [year] [term pageh1] - Aktuell [coupons cnt] Gutscheine & Angebote',
					'[month] [year] [term pageh1] - [coupons cnt] Aktive Online Gutscheine',
					'[month] [year] [term pageh1] - Die Besten [coupons cnt] Online Codes',
					'Gutschein [term name] [month] - [coupons cnt] Gutscheine & Angebote',
					'Gutschein [term name] [month] - [coupons cnt] Gutscheine & Deals',
					'[term pageh1] - [month] [year] geprüft',
					'[term name] Rabatt Gutschein - Nutze [coupons cnt] Aktive Gutscheine & Angebote',
					'[term name] - Neuster Gutschein [month] [year]',
					'[term pageh1] - [month] [year] Geprüft',
					'[term name]\'s Beste Gutschein für [month] [year]',
					'[term pageh1] [month] [year] - Günstig shoppen',
					'[month] [year] - [term name] Online Gutschein',
					'[month] [year] Geprüft - [term name] Gutschein',
					'[month] [year] [term pageh1] - Saving Story',
					'[coupons cnt] Aktive Gutscheine & Deals - [term name] Code [month] [year]',
					'[coupons cnt] Aktive Gutscheine & Deals - [term name]  [year] Rabatt',
					'Neuste [term name] Gutscheine für [month] [year]',
					'[month] [year] Getestet - [term name] Gutschein',
					'[promo detail] Rabatt Gutschein - [term name] Saving Story',
					'[promo detail] [term name] Rabatt Gutschein - Saving Story',
					'[promo detail] Rabatt Gutschein - [term name] [month]',
					'[promo detail] Rabatt Gutschein - [term name] [year]',
					'[promo detail] Rabatt Gutschein von [term name] - Saving Story',
					'[promo detail] Rabatt für [month] [year] bei [term name] - Saving Story',
					'[term name] [promo detail] Rabatt Gutschein [month] [year]' ,
					'[term pageh1] - Spare bis zu [promo detail] bei Saving Story',
					'[term pageh1] [month] [year] : Spare bis zu [promo detail]',
					'[month] [year] Bis zu -[promo detail] mit [term pageh1] - Saving Story',
					'[month] [year] Bis zu [promo detail] Rabatt mit [term pageh1] - Saving Story',
					'[promo detail] Rabatt mit [term pageh1] - Saving Story',
					'Sicher dir [term pageh1] - [promo detail] Rabatt',
					'[term name]\'s [promo detail] Rabatt  Gutschein - Saving Story',
					'Bester [term pageh1] - Bis zu [promo detail] Rabatt im [month] [year]',
					'[term name] Rabatt Gutschein - [promo detail] Rabatt',
					'[term name] Online Gutschein - Bis zu [promo detail] im [month] [year] sparen',
					'[term name] Aktive Online Gutschein | [promo detail] Rabatt',
					'[term pageh1] [month] [year] -Bis zu [promo detail] sparen',
					'[term pageh1] [month] [year] - [code cnt] Gutscheine & [deals cnt] Angebote',
					'Gutschein [term name] [month] - [code cnt] Gutscheine & [deals cnt] Deals',
					'[year] [term pageh1] | [code cnt] Gutscheincodes & [deals cnt] Deals',
			),
			'MetaDesc' => array(
					'Spare mit einem [promo detail] [term pageh1] und anderen gratis Gutscheincodes, Rabattgutscheine [coupons cnt] [term name] Gutscheine verfügbar [month] [year].',
					'Es sind jetzt [coupons cnt] Gutscheine [term name] verfügbar. Bester Gutschein: Hole [promo detail] Code. Spare mehr mit [term name] Gutscheincodes und Rabatten für [month] [year].',
					'Hol\' dir einen [promo detail] [term name] Gutscheincode oder Promo Code. [term name] hat [coupons cnt] Gutscheincodes & Rabattgutscheine für [month] [year]',
					'Finde die neusten [coupons cnt] [term pageh1], Rabatte für [month] [year] und erhalte [promo detail] [term name] Gutscheine.',
					'Finde die neusten [term pageh1], Rabatte für [month] [year]. Erhalte einen kostenlosen [term name] Gutschein.',
					'Saving Story hat [term name] Gutscheine inklusive [code cnt] aktiven Codes & [deals cnt] geprüften Deals für [month] [year].',
			),
			'MetaKeyword' => '[term pageh1] [month] [year], [term name] Aktionscodes',
	),
	
	'fr' => array(
		'MetaTitle' => array(
			'Code Promo [term name] [month] [year]: Obtenez [promo detail] Reduction [term name]', 
			'Code Promo [term name] [month] [year]  ', 
		), 
		'MetaDesc' => array(
			'Economisez avec un [promo detail] code promo [term name] et code reduction gratuit, bon de reduction. [coupons cnt] codes de [term name] disponibles en [month] [year].', 
			'Il y a [coupons cnt] bons de reduction [term name] maintenant. Top code promo: obtenez le [promo detail] code. Economisez plus avec code [term name] et discompte en [month] [year].', 
			'Obtenez un code reduction ou code promo [term name]. [coupons cnt] coupons & bons de reduction de [promo detail] [term name] sont rassemblés en [month] [year].', 
			'Trouvez les [coupons cnt] derniers codes promo, coupons, bons de reduction de [term name] en [month] [year] et recevez le [promo detail] code reduction [term name].', 
			'Trouvez les derniers codes promo, coupons, codes reduction de [term name] en [month] [year]. Obtenez un coupon reduction [term name].', 
		), 
		'MetaKeyword' => 'Code Promo [term name] [month] [year], Code Reduction [term name]', 
						  
	),
	
	'fr_h' => array(
			'MetaTitle' => array(
					'[term pageh1] [month] [year]: Obtenez [promo detail] Reduction [term name]',
					'[term pageh1] [month] [year]  ',
			),
			'MetaDesc' => array(
					'Economisez avec un [promo detail] code promo [term name] et code reduction gratuit, bon de reduction. [coupons cnt] codes de [term name] disponibles en [month] [year].',
					'Il y a [coupons cnt] bons de reduction [term name] maintenant. Top code promo: obtenez le [promo detail] code. Economisez plus avec code [term name] et discompte en [month] [year].',
					'Obtenez un code reduction ou code promo [term name]. [coupons cnt] coupons & bons de reduction de [promo detail] [term name] sont rassemblés en [month] [year].',
					'Trouvez les [coupons cnt] derniers codes promo, coupons, bons de reduction de [term name] en [month] [year] et recevez le [promo detail] code reduction [term name].',
					'Trouvez les derniers codes promo, coupons, codes reduction de [term name] en [month] [year]. Obtenez un coupon reduction [term name].',
			),
			'MetaKeyword' => 'Code Promo [term name] [month] [year], Code Reduction [term name]',
	
	),
		
	'uk' => array(
			'MetaTitle' => $uk_meta_title,
			'MetaDesc' => array('Find the latest [coupons cnt] [term name] discount codes, promo codes, vouchers in [month] [year]. Receive free [promo detail] [domain url] coupon.'),
			'MetaKeyword' => '[term name] Discount Code,[term name] Voucher Code,[term name] Promo Code',
	
	),
	
	'uk_h' => array(
			'MetaTitle' => array("[promo detail] [term pageh1] & Voucher Codes [month] [year]"),
			'MetaDesc' => array('Find the latest [coupons cnt] [term name] discount codes, promo codes, vouchers in [month] [year]. Receive free [promo detail] [domain url] coupon.'),
			'MetaKeyword' => '[term name] Discount Code,[term name] Voucher Code,[term name] Promo Code',
	
	)
);