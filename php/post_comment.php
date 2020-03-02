<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/3
 * Time: 11:28
 */
session_start();
date_default_timezone_set("Asia/Shanghai");
include ("./connect.php");
$article_id=$_POST['article_id'];
// 评论的人
if(!isset($_SESSION['username'])){
    $username = "no_login";
    echo json_encode(array("msg"=>"请先登录","code"=>0));
}else{
    $username = $_SESSION['username'];
    $content=$_POST['comment_content'];
	$create_time=time();
	$tag = "web";
	$sql = $dbh->prepare("INSERT into comments (username,article_id,comment_content,comment_time) 
	                         values ( :username, :article_id, :comment_content, :comment_time)");
	$sql->bindParam(':username',$username);
	$sql->bindParam(':article_id',$article_id);
	$sql->bindParam(':comment_content',$content);
	$sql->bindParam(':comment_time',$create_time);
	$flag1 = $sql->execute();

	if($sql){
	    echo json_encode(array("code"=>1));
	}
	// echo json_encode($data);
}





?>