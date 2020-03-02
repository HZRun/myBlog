<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/5/26
 * Time: 21:28
 */
session_start();
date_default_timezone_set("Asia/Shanghai");
if($_POST['do']=="user_exist"){
    include ("./connect.php");
    $sql = $dbh->prepare("SELECT * FROM blog_users where username = :username");
    $sql->bindParam(":username",$_POST['user']);
    $aa=$sql->execute();
    if($sql->fetchAll()){
        exit('true');
    }
    else exit('false');
}
if($_POST['do']=="login"){
    include("./connect.php");
    $sql =  $dbh->prepare("SELECT password FROM blog_users where username = :username");
    $sql->bindParam(":username",$_POST['user']);
    $sql->execute();
    $res = $sql->fetchAll();
    if($res[0]['password']==md5($_POST['psw'])){
        $_SESSION['username']=$_POST['user'];
        //echo $_SESSION['username'];
        echo json_encode(array("code"=>1));
    }
    else{
        $data['code']=0;
        echo json_encode($data);
    }
}