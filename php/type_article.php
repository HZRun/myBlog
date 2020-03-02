<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/10
 * Time: 16:19
 */
include ("./connect.php");
session_start();
date_default_timezone_set("Asia/Shanghai");
if(!isset($_SESSION['username'])){
            $now_session = "no_login";
}else{
    $now_session = $_SESSION['username'];
}
$tag=$_POST['tag'];
$username = $_POST['username'];
$type_sql=$dbh->prepare("select articles.article_id,articles.username, abstract, title, content,create_time,read_num from 
                 articles join tags on tag=:tag and articles.article_id=tags.article_id and articles.username=tags.username and articles.username=:username order by create_time desc");
$type_sql->bindParam(':tag',$tag);
$type_sql->bindParam('username',$username);
$type_sql->execute();
$res = $type_sql->fetchAll();

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
