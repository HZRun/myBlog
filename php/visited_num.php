<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/8
 * Time: 18:38
 */

session_start();
include ("./connect.php"); //连接数据库文件
date_default_timezone_set("Asia/Shanghai");
switch ($_POST['do']){
    case "article_visit":
        $article_id = $_POST['article_id'];
        $username = $_POST['username'];
        $upda_user = $dbh->prepare("update blog_users set visited_num=visited_num+1 where username=:username");
        $upda_user->bindParam(':username',$username);
        $upda_user->execute();

        $upda_arti = $dbh->prepare("update articles set read_num=read_num+1 where article_id=:article_id");
        $upda_arti->bindParam(':article_id',$article_id);
        $upda_arti->execute();
        echo json_encode(array("code"=>1));
        break;
    case "page_visit":
        $username = $_POST['username'];
        $upda_user = $dbh->prepare("update blog_users set visited_num=visited_num+1 where username=:username");
        $upda_user->bindParam(':username',$username);
        $upda_user->execute();
        echo json_encode(array("code"=>1));
        break;



}