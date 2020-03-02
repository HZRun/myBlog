<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/5/27
 * Time: 1:46
 */
require ("connect.php");
date_default_timezone_set("Asia/Shanghai");
if($_POST['do']=="user_exist"){
    $sql = $dbh->prepare("SELECT * FROM blog_users where username = :username");
    $sql->bindParam(":username",$_POST['username']);
    $aa=$sql->execute();
    if($sql->fetchAll()){
        exit('false');
    }
    else exit('true');
}

if($_POST['do']=="register"){
    $sql = $dbh->prepare("INSERT into blog_users (username,password,avatar,mobile,email,create_time) 
                     values (:username, :password, :avatar, :mobile, :email, :create_time)");
    $psw = md5($_POST['password']);
    $sql->bindParam(':username',$_POST['username']);
    $sql->bindParam(':password',$psw);
    $sql->bindParam(':mobile',$_POST['phone']);
    $sql->bindParam(':email',$_POST['email']);
    $time = time();
    $ava = 'img/default.jpg';
    $sql->bindParam(':create_time',$time);
    $sql->bindParam(':avatar',$ava);
    $flag = $sql->execute();
    if($flag){
        echo json_encode(array("code"=>1));
    }
}