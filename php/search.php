<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/8
 * Time: 14:37
 */
session_start();
include ("./connect.php"); //连接数据库文件
date_default_timezone_set("Asia/Shanghai");
switch ($_POST['do']) {   //根据传过来的$_POST['do']的值，执行相应操作
    case "search_article":  //获取文章列表
        $each_page = 5;   //默认每页记录条数
        if (isset($_POST['page_num'])) {  //$_POST['page_num']为第几页，，如果$_POST['page_num']没有被设置，即前端没指定那一页的内容，默认为1
            $page_num = $_POST['page_num'];
        } else {
            $page_num = 1;
        }
        $key = trim($_POST['key']);
        $start = $each_page * ($page_num - 1); //开始记录数
        $sql = $dbh->prepare("SELECT * FROM articles WHERE title LIKE :s_value ");  //查询语句
    //var_dump($sql);
        $p_value = '%'.$key.'%';  //构造字符串
    // echo $p_value;
        $sql->bindParam(':s_value',$p_value);   //绑定参数
        $sql->execute();
        $bb = $sql->fetchAll();    //获取结果
        //var_dump($bb);
        $amount = count($bb);    //数结果有多少个记录

        $sql = $dbh->prepare("SELECT article_id,username,abstract,title,create_time,read_num FROM articles WHERE title LIKE :s_value  order by create_time desc limit $start, $each_page");//获取指定页数的记录的sql语句
        //$sql->bindParam(':author',$author);
        $p_value = '%'.$key.'%';  //构造字符串
        $sql->bindParam(':s_value',$p_value);   //绑定参数
        $sql->execute();
        $aa = $sql->fetchAll();
        //var_dump($aa);
        $page_amount = ceil($amount / $each_page);
        if ($page_num == $page_amount) {
            $num = $page_amount % $each_page;
        } else {
            $num = $each_page;
        }
        $info['page_num'] = $page_num;
        $info['amount'] = $amount;
        $info['page_amount'] = $page_amount;
        $info['num'] = $num;
        $data['info'] = $info;
        $data['res'] = $aa;
        $get_comment = $dbh->prepare("SELECT * FROM comments where article_id = :article_id");
        foreach ($data['res'] as $k => $v) { //循环改变时间格式，获取评论数
            $data['res'][$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $get_comment->bindParam(':article_id', $v['article_id']);
            $get_comment->execute();
            $data['res'][$k]['comment_num'] = count($get_comment->fetchAll());//查询comment表获得
        }
        if ($page_num > $page_amount) {
            echo json_encode(array("code" => 0));
        } else {
            $data['code'] = 1;
            echo json_encode($data);  //将结果返回前端，echo可以看作固定写法，json_encode()把返回数据转换为json格式
        }

        break;


    case "search_user_article":  //获取博主文章列表
        $each_page = 5;   //默认每页记录条数
        if (isset($_POST['page_num'])) {  //$_POST['page_num']为第几页，，如果$_POST['page_num']没有被设置，即前端没指定那一页的内容，默认为1
            $page_num = $_POST['page_num'];
        } else {
            $page_num = 1;
        }
        $author = $_POST['author'];
        $key = trim($_POST['key']);
        $start = $each_page * ($page_num - 1); //开始记录数
        $sql = $dbh->prepare("SELECT * FROM articles WHERE title LIKE :s_value and username =:username");  //查询语句
        //var_dump($sql);
        $p_value = '%'.$key.'%';  //构造字符串
        $sql->bindParam(':s_value',$p_value);   //绑定参数
        $sql->bindParam(':username',$author);   //绑定参数
        $sql->execute();
        $bb = $sql->fetchAll();    //获取结果
//        var_dump($bb);
        $amount = count($bb);    //数结果有多少个记录

        $sql2 = $dbh->prepare("SELECT article_id,username,abstract,title,create_time,read_num FROM articles WHERE title LIKE :s_value and username =:username order by create_time desc limit $start, $each_page");//获取指定页数的记录的sql语句
        //$p_value = '%'.$key.'%';  //构造字符串
        $sql2->bindParam(':s_value',$p_value);   //绑定参数
        $sql2->bindParam(':username',$author);   //绑定参数
        $sql2->execute();
        $aa = $sql2->fetchAll();
//        var_dump($aa);
        $page_amount = ceil($amount / $each_page);
        if ($page_num == $page_amount) {
            $num = $page_amount % $each_page;
        } else {
            $num = $each_page;
        }
        $info['page_num'] = $page_num;
        $info['amount'] = $amount;
        $info['page_amount'] = $page_amount;
        $info['num'] = $num;
        $data['info'] = $info;
        $data['res'] = $aa;
        $get_comment = $dbh->prepare("SELECT * FROM comments where article_id = :article_id");
        foreach ($data['res'] as $k => $v) { //循环改变时间格式，获取评论数
            $data['res'][$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $get_comment->bindParam(':article_id', $v['article_id']);
            $get_comment->execute();
            $data['res'][$k]['comment_num'] = count($get_comment->fetchAll());//查询comment表获得
        }
        if ($page_num > $page_amount) {
            echo json_encode(array("code" => 0));
        } else {
            $data['code'] = 1;
            echo json_encode($data);  //将结果返回前端，echo可以看作固定写法，json_encode()把返回数据转换为json格式
        }
        break;


    case "search_user":  //获取搜索用户列表
        $key = $_POST['key'];
        $sql1 = $dbh->prepare("SELECT username,avatar,create_time,visited_num FROM blog_users WHERE username LIKE :s_value ");  //查询语句
        //var_dump($sql);
        $p_value = '%'.$key.'%';  //构造字符串
        $sql1->bindParam(':s_value',$p_value);   //绑定参数
        $sql1->execute();
        $bb = $sql1->fetchAll();    //获取结果
        $page_amount=count($bb);
        $data['res'] = $bb;
        $sql3 = $dbh->prepare("SELECT * FROM articles WHERE username =:username");  //查询语句
        $check =$dbh->prepare("SELECT * FROM followers where username = :username and followers = :followers");
        foreach($data['res'] as $k=>$v){
            $check->bindParam(':followers', $_SESSION['username']);
            $check->bindParam(':username', $v['username']);
            $sign = $check->execute();
            if($check->fetch()){
                $data['res'][$k]['is_follow']=true;
            }
            else{
                $data['res'][$k]['is_follow']=false;
            }
            $sql3->bindParam(':username',$v['username']);
            $sql3->execute();
            $data['res'][$k]['article_num'] = count($sql3->fetchAll());
        }
        if ($page_amount==0) {
            echo json_encode(array("code" => 0));
        } else {
            $data['code'] = 1;
            echo json_encode($data);  //将结果返回前端，echo可以看作固定写法，json_encode()把返回数据转换为json格式
        }
        break;


/*    case "search_user_article":  //获取博主文章列表
        $each_page = 5;   //默认每页记录条数
        if (isset($_POST['page_num'])) {  //$_POST['page_num']为第几页，，如果$_POST['page_num']没有被设置，即前端没指定那一页的内容，默认为1
            $page_num = $_POST['page_num'];
        } else {
            $page_num = 1;
        }
        $author = $_POST['author'];
        $key = $_POST['key'];
        $start = $each_page * ($page_num - 1); //开始记录数
        $sql = $dbh->prepare("SELECT * FROM articles WHERE title LIKE :s_value and username =:username");  //查询语句
        //var_dump($sql);
        $p_value = '%'.$key.'%';  //构造字符串
        $sql->bindParam(':s_value',$p_value);   //绑定参数
        $sql->bindParam(':username',$author);   //绑定参数
        $sql->execute();
        $bb = $sql->fetchAll();    //获取结果
        //var_dump($bb);
        $amount = count($bb);    //数结果有多少个记录
        if($amount<=0){
            echo json_encode(array("code" => 0));
        }

        $sql = $dbh->query("SELECT * FROM articles order by create_time desc limit $start, $each_page");//获取指定页数的记录的sql语句
        //$sql->bindParam(':author',$author);
        $aa = $sql->fetchAll();
        //var_dump($aa);
        $page_amount = ceil($amount / $each_page);
        if ($page_num == $page_amount) {
            $num = $page_amount % $each_page;
        } else {
            $num = $each_page;
        }
        $info['page_num'] = $page_num;
        $info['amount'] = $amount;
        $info['page_amount'] = $page_amount;
        $info['num'] = $num;
        $data['info'] = $info;
        $data['res'] = $aa;
        $sql = $dbh->prepare("SELECT * FROM articles WHERE username =:username");  //查询语句
        foreach($data['res'] as $k=>$v){
            $sql->bindParam(':username',$v['username']);
            $sql->execute();
            $data['res'][$k]['article_num'] = $sql->fetchAll();
        }
        if ($page_num > $page_amount) {
            echo json_encode(array("code" => 0));
        } else {
            $data['code'] = 1;
            echo json_encode($data);  //将结果返回前端，echo可以看作固定写法，json_encode()把返回数据转换为json格式
        }
        break;*/


}

