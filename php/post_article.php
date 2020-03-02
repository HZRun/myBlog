<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/5/31
 * Time: 17:20
 */
session_start();
date_default_timezone_set("Asia/Shanghai");
    include ("./connect.php");
    $title=$_POST['title'];
    $abstract=$_POST['abstract'];
    $username=$_SESSION['username']  ; //$_SESSION['username'];
    $content=$_POST['content'];
    $create_time=time();
    $tag = "web";
    $sql = $dbh->prepare("INSERT into articles (username,title,abstract,content,create_time) 
                         values ( :username, :title, :abstract, :content, :create_time)");
    $sql->bindParam(':username',$username);
    $sql->bindParam(':title',$title);
    $sql->bindParam(':content',$content);
    $sql->bindParam(':abstract',$abstract);
    $sql->bindParam(':create_time',$create_time);
    $flag1 = $sql->execute();

    $article_id = $dbh->lastInsertId();
    //echo $article_id;

   //var_dump($_POST['tag']);
    if(isset($_POST['tag'])){
        foreach ($_POST['tag'] as $k=>$v){
            $sql2 = $dbh->prepare("INSERT into tags (username,article_id,tag)
                         values ( :username, :article_id, :tag)");
            $sql2->bindParam(':username',$username);
            $sql2->bindParam(':article_id',$article_id);
            $sql2->bindParam(':tag',$v);
            $flag2= $sql2->execute();
        }
    }
    else{

    }


    if($flag1){
        echo json_encode(array("code"=>1,"article_id"=>$article_id));
    }