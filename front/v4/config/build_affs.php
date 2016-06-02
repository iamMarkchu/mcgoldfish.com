<?php

try {
    
    $file_lock = str_replace('.php', '.lck', __FILE__);
    if ($file_lock != '' && file_exists($file_lock)) 
        throw new Exception ("Process exists");

    touch($file_lock);

    //dump file domain    
    $file_domain = "t_affurl.txt";
    $fw = fopen($file_domain, 'w');
    if(!$fw)
        throw new Exception ("Create file {$file_domain} failed");

    $ch = curl_init('http://bcg.mgsvr.com/data/t_affurl.txt');
    $curl_opts = array(CURLOPT_HEADER=>false,
                       CURLOPT_NOBODY=>false,
                       CURLOPT_RETURNTRANSFER=>true,
                       CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
                       CURLOPT_FILE => $fw,
                 );
    curl_setopt_array($ch, $curl_opts);

    $pass = curl_exec($ch);
    curl_close($ch);
    if (!$pass)
        throw new Exception ("{$file_domain} dump failed");
    fclose($fw);

    clearstatcache();
    if(filesize($file_domain) == 0)
        throw new Exception ("Empty size of {$file_domain}");


    //dump file domain    
    $file_sid = "t_affsid.txt";
    $fw = fopen($file_sid, 'w');
    if(!$fw)
        throw new Exception ("Create file {$file_sid} failed");

    $ch = curl_init('http://bcg.mgsvr.com/data/t_affsid.txt');
    $curl_opts = array(CURLOPT_HEADER=>false,
                       CURLOPT_NOBODY=>false,
                       CURLOPT_RETURNTRANSFER=>true,
                       CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
                       CURLOPT_FILE => $fw,
                 );
    curl_setopt_array($ch, $curl_opts);

    $pass = curl_exec($ch);
    curl_close($ch);
    if (!$pass)
        throw new Exception ("{$file_sid} dump failed");
    fclose($fw);

    clearstatcache();
    if(filesize($file_sid) == 0)
        throw new Exception ("Empty size of {$file_sid}");


    //dump file domain    
    $file_deep = "t_affdeepurl.txt";
    $fw = fopen($file_deep, 'w');
    if(!$fw)
        throw new Exception ("Create file {$file_deep} failed");

    $ch = curl_init('http://bcg.mgsvr.com/data/t_affdeepurl.txt');
    $curl_opts = array(CURLOPT_HEADER=>false,
                       CURLOPT_NOBODY=>false,
                       CURLOPT_RETURNTRANSFER=>true,
                       CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
                       CURLOPT_FILE => $fw,
                 );
    curl_setopt_array($ch, $curl_opts);

    $pass = curl_exec($ch);
    curl_close($ch);
    if (!$pass)
        throw new Exception ("{$file_deep} dump failed");
    fclose($fw);


    clearstatcache();
    if(filesize($file_deep) == 0)
        throw new Exception ("Empty size of {$file_deep}");


    //build affs
    $affs = array();
    $fp = fopen ($file_domain, 'r');
    if(!$fp)
         throw new Exception ("{$file_domain} open failed");

    while (!feof($fp)) {
        $lr = explode("\t", trim(fgets($fp)));
        
        if ($lr[0] == '' || strpos($lr[0], '#') === 0 || $lr[1] == '')
            continue;
    
        if (!preg_match('/^([0-9]+)~(.*)/', $lr[0], $m))
            continue;
   
        if (isset($affs[$m[2]])) {
            array_push($affs[$m[2]]['do'], $lr[1]);
        }
        else {
            $affs[$m[2]]['id' ] = $m[1];
            $affs[$m[2]]['do' ] = array($lr[1]);                
            $affs[$m[2]]['de' ] = '';
            $affs[$m[2]]['sid'] = '';            
        }
    }
    fclose($fp);


    $fp = fopen ($file_sid, 'r');
    if(!$fp)
         throw new Exception ("{$file_sid} open failed");

    while (!feof($fp)) {
        $lr = explode("\t", trim(fgets($fp)));
        
        if ($lr[0] == '' || strpos($lr[0], '#') === 0 || $lr[1] == '')
            continue;
    
        if (!preg_match('/^([0-9]+)~(.*)/', $lr[0], $m))
            continue;
   
        if (isset($affs[$m[2]])) {
           $affs[$m[2]]['sid'] =  $lr[1];
        }
    }
    fclose($fp);


    $fp = fopen ($file_deep, 'r');
    if(!$fp)
         throw new Exception ("{$file_deep} open failed");

    while (!feof($fp)) {
        $lr = explode("\t", trim(fgets($fp)));
        
        if ($lr[0] == '' || strpos($lr[0], '#') === 0 || $lr[1] == '')
            continue;
    
        if (!preg_match('/^([0-9]+)~(.*)/', $lr[0], $m))
            continue;
   
        if (isset($affs[$m[2]])) {
           $affs[$m[2]]['de'] = $lr[1];
        }
    }
    fclose($fp);


    //build affiliate.php
    if (count($affs) == 0) {
        throw new Exception ("No affiliate need to be created");
    }

    $file_aff = 'affiliate.php';
    $file_tmp = $file_aff.'.upd';

/* 
 * remove following domain   
    $AFFILIATE_DOMAINS['Amazon_US']="amazon.com";    
    $AFFILIATE_DOMAINS['Amazon_CA']="amazon.ca";    
    $AFFILIATE_DOMAINS['Amazon_UK']="amazon.co.uk";
 */
    $fw = fopen ($file_tmp, 'w');
    fwrite($fw, "<?php\n");
    foreach ($affs as $name => $v) {
        if (stripos($name, 'amazon') === 0)
            continue;                    
            
        fwrite($fw, '$AFFILIATE_IDS[\''.$name.'\']="'.$v['id']."\";\n");
    }    

    foreach ($affs as $name => $v) {
        if (stripos($name, 'amazon') === 0)
            continue;                    

        fwrite($fw, '$AFFILIATE_DOMAINS[\''.$name.'\']="'.implode(',', $v['do'])."\";\n");
    }
    fwrite($fw, "\n\n");
    foreach ($affs as $name => $v) {
        if ($v['sid'] == '')
             continue;
        fwrite($fw, '$AFFILIATE_SIDS[\''.$name.'\']="'.$v['sid']."\";\n");
    }
    fwrite($fw, "\n\n");
    foreach ($affs as $name => $v) {
        if ($v['de'] == '')
           continue;    
        fwrite($fw, '$AFFILIATE_DEEPURLS[\''.$name.'\']="'.$v['de']."\";\n");
    }   


    

    fwrite($fw, '$AFF_CJ_SITES = array(	\'snapus\' => \'2567387\', 
                                        \'snapuk\' => \'2830169\', 
                                    	\'snapca\' => \'3409523\', 
                                    	\'snapie\' => \'3409537\', 
                                   	    \'snapau\' => \'3629537\', 
                                    	\'snapde\'	=> \'3833191\',
                                        \'snapnz\'	=> \'3915421\',
                                        \'cs5soft\'	=> \'3896208\',
                                        \'morecouponcodes\' => \'2707521\', 
                                        \'aperfectcoupon\' => \'2945227\',
                                        \'hosting\' => \'2958015\',
                                        \'cs3soft\' => \'3182550\',
                                        \'cs4softuk\' => \'3182552\',
                                        \'cs4soft\' => \'3619774\',
                                        \'stuffitstore\' => \'3939808\',
                                        \'antivirus\' => \'3580570\',
                                        \'urccus\' => \'5752344\',
                                        \'laihaitao\' => \'6441991\',                                        
                            			\'pc2011\' => \'5565789\',
                                        \'coup4lap\' => \'6233710\',
                                        \'hotdeals\' => \'7292928\',
                                        
                );'."\n"
        );   

	//end

    fwrite($fw, '$AFF_AMZUS_SITES = array(\'pc2011\' => \'xc201x-20\', \'coup4lap\' => \'xc201x-20\');'."\n");
    fwrite($fw, '$AFF_AMZCA_SITES = array(\'pc2011\' => \'xc201xca-20\', \'coup4lap\' => \'xc201xca-20\');'."\n");
    fwrite($fw, '$AFF_AMZUK_SITES = array(\'pc2011\' => \'xc201xuk-21\', \'coup4lap\' => \'xc201xuk-21\');'."\n");
    fwrite($fw, '$AFF_EBAY_STIES  = array(\'pc2011\' => \'5337200376\',
                                          \'coup4lap\' => \'5337200376\',
                                          \'snapus\' => \'5335906561\',
                                          \'snapuk\' => \'5336701661\',
                                          \'snapau\' => \'5336704270\',
                                          \'snapca\' => \'5336713549\',
                                  );'."\n"
           );

    fwrite($fw, '$AFF_SKIMLINKS_SITES = array (\'snapus\' => \'662619\', 
                                          	   \'snapuk\' => \'1485685\', 
                                               \'snapca\'	=> \'1485688\', 
                                               \'snapau\'	=> \'1310416\', 
                                               \'snapde\'	=> \'1485698\',
                                               \'AnyPromoCodes\' => \'1485699\',
                                               \'hotdeals\' => \'1485700\',
                                               \'csusm\'    => \'1488941\',
                                               \'pc2011\'   => \'1485686\',
                                               \'dealsa\'   => \'1485704\',
                                               \'walletsaving\' => \'1488937\',
                                               \'FSC\'  => \'1488935\',
                                        );'."\n"
                                );

    fwrite($fw, "?>\n");    
    fclose($fw);

    if(!rename($file_tmp, $file_aff))
        throw new Exception ("MV file failed");
    unlink($file_lock);
	if($_GET["t"]=='json')
	{
		$result = array(
			"affurl" => array("status"=>1, "message"=>""),
			"affdeepurl" => array("status"=>1, "message"=>""),
			"affsid" => array("status"=>1, "message"=>""),
		);
		echo json_encode($result);
	}
}
catch (Exception $e) {
    echo $e->getMessage()."\n";
    unlink($file_lock);
    exit(1);
}


