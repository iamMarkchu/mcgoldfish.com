<?php 
function dump($array){
	echo "<pre>";
	print_r($array);
}
function format_date_V2($datetime){
	$now = date("Y-m-d H:i:s");
    $startdate=$datetime;
    $enddate=$now;
    if($enddate < $startdate ){
        return "";
    }
    $year=floor((strtotime($enddate)-strtotime($startdate))/(86400*365));
    $month=floor((strtotime($enddate)-strtotime($startdate))%(86400*365)/(86400*30));
    $date=floor((strtotime($enddate)-strtotime($startdate))%(86400*365)%(86400*30)/86400);
    $hour=floor((strtotime($enddate)-strtotime($startdate))%(86400*365)%(86400*30)%86400/3600);
    $minute=floor((strtotime($enddate)-strtotime($startdate))%(86400*365)%(86400*30)%86400%3600/60);
    $second=floor((strtotime($enddate)-strtotime($startdate))%(86400*365)%(86400*30)%86400%3600%60);
    //die($year." 年 ".$month." 月 ".$date." 日 ".$hour." 时 ".$minute ." 分 " .$second ."秒");
    if((int)$year > 0){
        return $year."年前";
    }elseif ((int)$year <= 0 && (int)$month > 0) {
        return $month."月前";
    }elseif ((int)$year <= 0 && (int)$month <= 0 && (int)$date > 0) {
        return $date."天前";
    }elseif ((int)$year <= 0 && (int)$month <= 0 && (int)$date <= 0 && (int)$hour > 0){
        return $hour."小时前";
    }elseif ((int)$year <= 0 && (int)$month <= 0 && (int)$date <= 0 && (int)$hour <= 0 && $minute > 0){
        return $minute."分钟前";
    }else{
        return "刚刚";
    }
}
function rand_one_img($imgList){
    shuffle($imgList);
    return "http://img.mcgoldfish.com/touxiang/".$imgList[0];
}
function goto_404(){
    $client_ip = $_SERVER['REMOTE_ADDR'];
    $client_agent = $_SERVER['HTTP_USER_AGENT'];
    $url = $_SERVER['REQUEST_URI'];
    $visittime = date('Y-m-d H:i:s');
    $f = fopen(INCLUDE_ROOT. 'storage/404.txt', 'a');
    fwrite($f, $client_ip."\t".$client_agent."\t".$url."\n");
    include_once FRONT_DIR. '404.php';
    exit;
}

function init_weibo_url(){
    session_start();
    $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
    return $o->getAuthorizeURL( WB_CALLBACK_URL );
}
