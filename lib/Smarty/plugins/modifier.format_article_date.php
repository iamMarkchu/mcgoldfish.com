<?php

function smarty_modifier_format_article_date($addtime){
   	$now = date("Y-m-d H:i:s");
   	$startdate=$addtime;
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
        return $month. "月前";
    }elseif ((int)$year <= 0 && (int)$month <= 0 && (int)$date > 0) {
       return $date. "天前";   
    }elseif ((int)$year <= 0 && (int)$month <= 0 && (int)$date <= 0 && (int)$hour > 0){
       return $hour. "小时前";
    }elseif ((int)$year <= 0 && (int)$month <= 0 && (int)$date <= 0 && (int)$hour <= 0 && $minute > 0){
       return $minute. "分钟前";
    }else{
       return "刚刚";
    }
}
