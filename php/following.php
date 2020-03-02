<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/8
 * Time: 10:37
 */
session_start();
require ("connect.php");
date_default_timezone_set("Asia/Shanghai");
switch ($_POST['do']){
    case "get_fans":
        $username = $_POST['username'];
        $sql = $dbh->prepare("SELECT * FROM followers where username = :username");
        $sql->bindParam(":username",$username);
        $flag=$sql->execute();
        $res = $sql->fetchAll();

        $ava = $dbh->prepare("SELECT * FROM blog_users where username = :username");
        $check =$dbh->prepare("SELECT * FROM followers where username = :username and followers = :followers");
        $fans_num = count($res);

        if(!isset($_SESSION['username'])){
            $now_session = "no_login";
        }else{
            $now_session = $_SESSION['username'];
        }
        foreach ($res as $k=>$v){
            $check->bindParam(':followers', $now_session);
            $check->bindParam(':username', $v['followers']);
            $sign = $check->execute();
            //echo $v['followers'];
            //var_dump($check->fetchAll());
            $aa= $check->fetch();
            // var_dump($aa);
            // echo $aa['id'];
            if($aa['id']>0){
                $res[$k]['is_follow']=true;
            }
            else{
                $res[$k]['is_follow']=false;
            }
            $ava->bindParam(":username",$v['followers']);
            $ava->execute();
            $avatar = $ava->fetch();
            $res[$k]['face_url'] = $avatar['avatar'];
            $res[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        echo json_encode(array("res"=>$res, "fans_num"=>$fans_num, "now_session"=>$now_session));
        break;

    case "get_focus":
        $username = $_POST['username'];
        $sql = $dbh->prepare("SELECT * FROM followers where followers = :username");
        $sql->bindParam(":username",$username);
        $flag=$sql->execute();
        $res=array();
        $res = $sql->fetchAll();
        $ava = $dbh->prepare("SELECT * FROM blog_users where username = :username");
        $check =$dbh->prepare("SELECT * FROM followers where username = :username and followers = :followers");
        $focus_num=count($res);
        if(!isset($_SESSION['username'])){
            $now_session = "no_login";
        }else{
            $now_session = $_SESSION['username'];
        }
        foreach ($res as $k=>$v){
            $check->bindParam(':followers', $now_session);
            $check->bindParam(':username', $v['username']);
            $sign = $check->execute();
            if($check->fetch()){
                $res[$k]['is_follow']=true;
            }
            else{
                $res[$k]['is_follow']=false;
            }
            $ava->bindParam(":username",$v['username']);
            $ava->execute();
            $avatar = $ava->fetch();
            $res[$k]['face_url'] = $avatar['avatar'];
            $res[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        echo json_encode(array("res"=>$res,"focus_num"=>$focus_num,"now_session"=>$now_session));
        break;

    case "focus":
        if(!isset($_SESSION['username'])){
            $data['now_session'] = "no_login";
            echo json_encode(array('now_session'=>$data, 'code'=>0,'msg'=>'未登录'));
        }
        else{
            $followers = $_SESSION['username'];
            $username = $_POST['username'];
            $time = time();

            $sql = $dbh->prepare("INSERT into followers (username,followers,create_time) 
                     values (:username, :followers, :create_time)");
            $sql->bindParam(':username',$username);
            $sql->bindParam(':followers',$followers);
            $sql->bindParam(':create_time',$time);
            $flag = $sql->execute();
            echo json_encode(array('code'=>1,'msg'=>'关注成功'));
        }
        break;

    case "no_focus":
        if(!isset($_SESSION['username'])){
            $data['now_session'] = "no_login";
            echo json_encode(array('now_session'=>$data,'code'=>0,'msg'=>'未登录'));
        }
        else{
            $followers = $_SESSION['username'];
            $username = $_POST['username'];
            $sql = $dbh->prepare("DELETE FROM followers WHERE username =:username and followers =:followers");//删除语句
            $sql->bindParam(':username',$username); //参数绑定
            $sql->bindParam(':followers',$followers); //参数绑定
            $flag = $sql->execute(); //执行操作
            if($flag){  //如果执行成功返回信息
                echo json_encode(array("code"=>1,"msg"=>"删除成功"));
            }
            break;
        }
}