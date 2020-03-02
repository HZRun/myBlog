<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/11
 * Time: 21:43
 */


session_start();
include ("./connect.php"); //连接数据库文件
date_default_timezone_set("Asia/Shanghai");
//$Y = 2016;//获取年，示例，真实环境从前端获取数据
//$m = 8;//获取月，示例，真实环境从前端获取数据
//$month = $Y."-".$m;//当前年月
$month=$_POST['month'];
$month_start = strtotime($month);//指定月份月初时间戳
$month_end = mktime(23, 59, 59, date('m', strtotime($month))+1, 00);//指定月份月末时间戳
if(!isset($_SESSION['username'])){
    $now_session = "no_login";
}else{
    $now_session = $_SESSION['username'];
}
$username = $_POST['username'];
$time_sql=$dbh->prepare("select article_id,username, abstract, title, create_time,read_num from 
            articles where username=:username and create_time between :start_time and :end_time");
$time_sql->bindParam(':start_time',$month_start);
$time_sql->bindParam(':end_time',$month_end);
$time_sql->bindParam('username',$username);
$time_sql->execute();
$res = $time_sql->fetchAll();

$comment = $dbh->prepare("select count(*) as comment_num from comments where article_id=:article_id");
foreach ($res as $k=>$v){
    $comment->bindParam(':article_id',$v['article_id']);
    $comment->execute();
    $comment_res =$comment->fetch();
    $res[$k]['create_time']=date('Y-m-d H:i:s', $v['create_time']);
    $res[$k]['comment_num']= $comment_res['comment_num'];
}
$res['now_session'] = $now_session;
echo json_encode($res);