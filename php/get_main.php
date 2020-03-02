<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/1
 * Time: 13:31
 */
session_start();
date_default_timezone_set("Asia/Shanghai");
    include ("./connect.php"); //连接数据库文件
    switch ($_POST['do']){   //根据传过来的$_POST['do']的值，执行相应操作
        case "get_article":  //获取文章列表
            $username = $_POST['username'];
            if(!isset($_SESSION['username'])){
                $now_session = "no_login";
            }else{
                $now_session = $_SESSION['username'];
            }

            $each_page = 5;   //默认每页记录条数
            if(isset($_POST['page_num'])){  //$_POST['page_num']为第几页，，如果$_POST['page_num']没有被设置，即前端没指定那一页的内容，默认为1
                $page_num=$_POST['page_num'];
            }
            else{
                $page_num=1;
            }
            $start = $each_page*($page_num-1); //开始记录数
            $sql = $dbh->prepare("SELECT article_id FROM articles where username = :username");  //查询语句
            $sql->bindParam(':username', $username);
            $sql->execute();
            $bb= $sql->fetchAll();    //获取结果
            $amount=count($bb);    //数结果有多少个记录
            $sql = $dbh->prepare("SELECT article_id,username,abstract,title,create_time,read_num FROM articles where username = :username order by create_time desc limit $start, $each_page");//获取指定页数的记录的sql语句
            $sql->bindParam(':username', $username);
            $sql->execute();
            $aa = $sql->fetchAll();
            //var_dump($aa);
            $page_amount=ceil($amount/$each_page);
            if($page_num==$page_amount){
                $num = $page_amount%$each_page;
            }else{
                $num = $each_page;
            }
            $info['now_session'] = $now_session;
            $info['page_num']=$page_num;
            $info['amount']=$amount;
            $info['page_amount']=$page_amount;
            $info['$num'] =$num;
            $data['info']=$info;
            $data['res']=$aa;

            $get_comment = $dbh->prepare("SELECT article_id FROM comments where article_id = :article_id");
            foreach ($data['res'] as $k=>$v){ //循环改变时间格式，获取评论数
                $data['res'][$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
                $get_comment->bindParam(':article_id', $v['article_id']);
                $get_comment->execute();
                $data['res'][$k]['comment_num'] = count($get_comment->fetchAll());//查询comment表获得
                //$data['res'][$k]['comment_num'] = 20; //本来应该查询comment表获得，暂时写死
            }
            if($page_num>$page_amount){
                echo json_encode(array("code"=>0));
            }
            else{
                $data['code']=1;
                echo json_encode($data);  //将结果返回前端，echo可以看作固定写法，json_encode()把返回数据转换为json格式
            }

            break;
        case "delete_article":
            $username = $_POST['username'];
            if(!isset($_SESSION['username'])){
                $now_session = "no_login";
            }else{
                $now_session = $_SESSION['username'];
            }
            $article_id=$_POST['article_id'];
            $sql = $dbh->prepare("DELETE FROM articles WHERE article_id =:article_id and username = :username");//删除语句
            $sql->bindParam(':article_id',$article_id); //参数绑定
            $sql->bindParam(':username', $username);
            $flag0 = $sql->execute(); //执行操作

            // tags表和评论表也需要删除
            $sql = $dbh->prepare("DELETE FROM tags WHERE article_id =:article_id and username = :username");
            $sql->bindParam(':article_id',$article_id); //参数绑定
            $sql->bindParam(':username', $username);
            $flag1 = $sql->execute(); //执行操作
            $sql = $dbh->prepare("DELETE FROM comments WHERE article_id =:article_id");
            $sql->bindParam(':article_id',$article_id); //参数绑定
            $flag2 = $sql->execute(); //执行操作


            if($flag0 && $flag1 && $flag2){  //如果执行成功返回信息
                echo json_encode(array("code"=>1,"msg"=>"删除成功"));
            }
            break;

        case "get_list":
            $username = $_SESSION['username'];
            $sql = $dbh->prepare("SELECT distinct tag FROM tags where username=:username");//获取指定页数的记录的sql语句
            // sql语句 SELECT DISTINCT tag FROM tags WHERE username='mazong'
            $sql->bindParam(':username',$username);
            $sql->execute();
            $res['tag'] = $sql->fetchAll();
            //var_dump($tag);
            $res['code']=1;
            //$res['face_url']=
            echo json_encode($res);
            break;
        case "get_info":
            $username = $_POST['username'];
            if(!isset($_SESSION['username'])){
                $now_session = "no_login";
            }else{
                $now_session = $_SESSION['username'];
            }

            $sql = $dbh->prepare("SELECT * FROM articles where username=:username");//获取指定页数的记录的sql语句
            // sql语句 SELECT DISTINCT tag FROM tags WHERE username='mazong'
            $sql->bindParam(':username',$username);
            $sql->execute();
            $res = $sql->fetchAll();
            $data['username'] = $username;
            $data['now_session'] = $now_session;
            $data['article_num']=count($res);

            $sql = $dbh->prepare("SELECT * FROM followers where username = :username");
            $sql->bindParam(':username', $username);
            $sql->execute();
            $res = $sql-> fetchAll();

            $data['fans']= count($res);

            $sql = $dbh->prepare("SELECT * FROM followers where followers = :username");
            $sql->bindParam(':username', $username);
            $sql->execute();
            $res = $sql-> fetchAll();
            $data['follow']=count($res);

            // 暂时不动
            $data['comment_num'] = 0;

            // 阅读量
            $sql = $dbh->prepare("SELECT visited_num FROM blog_users where username = :username");
            $sql->bindParam(':username', $username);
            $sql->execute();
            $res = $sql-> fetch();
            $data['visited_num'] = $res;

            // 个人分类
            $sql = $dbh->prepare("select tag, count(tag) as num from tags where username = :username group by tag");
            $sql->bindParam(':username', $username);
            $sql->execute();
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);
            $rows = count($res);

            // for ($i=0; $i < rows; $i++) { 
            //     # code...
            //     $
            // }
            $data['cate'] = $res;

            // aside头像
            $sql = $dbh->prepare("select avatar from blog_users where username = :username");
            $sql->bindParam(':username', $username);
            $sql->execute();
            $res = $sql->fetch();
            $data['avatar'] = $res;

            //登录用户 是否关注了博主
            $focus=$dbh->prepare("SELECT id FROM followers where username = :username and followers = :followers");
            $focus->bindParam(':username', $username);
            $focus->bindParam(':followers', $now_session);
            $focus->execute();
            $focus_id = $focus-> fetch();
            if($focus_id){
                $data['is_follow']=true;
            }
            else{
                $data['is_follow']=false;
            }

            // 导航栏头像
            if ($now_session == $username) {
                # code...
                $sql = $dbh->prepare("select avatar from blog_users where username = :username");
                $sql->bindParam(':username', $username);
                $sql->execute();
                $res = $sql->fetch();
                $data['avatar-nav'] = $res;
            }else if($now_session == 'no_login'){
                $data['avatar-nav'] = 'img/no_login.png';
            }else if($now_session != $username){
                $sql = $dbh->prepare("select avatar from blog_users where username = :username");
                $sql->bindParam(':username', $now_session);
                $sql->execute();
                $res = $sql->fetch();
                $data['avatar-nav'] = $res;
            }
            

            // 归档
            $sql = $dbh->prepare("select create_time from articles where username = :username order by create_time desc");
            $sql->bindParam(':username', $username);
            $sql->execute();
            $res = $sql->fetchAll();
            $data['create_time'] = $res;

            // 热门文章：阅读量top 5
            $sql = $dbh->prepare("SELECT title, read_num, article_id FROM articles WHERE username = :username order by read_num desc limit 5");
            $sql->bindParam(':username', $username);
            $sql->execute();
            $res = $sql->fetchAll();
            $data['hot_articles'] = $res;


            // 学校和github
            $sql = $dbh->prepare("SELECT school FROM blog_users where username = :username");//获取指定页数的记录的sql语句
            $sql->bindParam(':username',$username);
            $sql->execute();
            $res = $sql->fetch();
            $data['school'] = $res;

            $sql = $dbh->prepare("SELECT github FROM blog_users where username = :username");//获取指定页数的记录的sql语句
            $sql->bindParam(':username',$username);
            $sql->execute();
            $res = $sql->fetch();
            $data['github'] = $res;

            // 手机
            $sql = $dbh->prepare("SELECT mobile FROM blog_users where username = :username");//获取指定页数的记录的sql语句
            $sql->bindParam(':username',$username);
            $sql->execute();
            $res = $sql->fetch();
            $data['mobile'] = $res;

            // email
            $sql = $dbh->prepare("SELECT email FROM blog_users where username = :username");//获取指定页数的记录的sql语句
            $sql->bindParam(':username',$username);
            $sql->execute();
            $res = $sql->fetch();
            $data['email'] = $res;
            echo json_encode($data);
            break;

        case "get_detail":
            // 评论的人
            if(!isset($_SESSION['username'])){
                $username = "no_login";
            }else{
                $username = $_SESSION['username'];
                $data['now_session'] = $_SESSION['username'];
            }

            $article_id = $_POST['article_id'];
            $sql = $dbh->prepare("SELECT * FROM articles where article_id=:article_id");
            $sql->bindParam(':article_id',$article_id);
            $sql->execute();
            $data['article'] = $sql->fetchAll();
            //var_dump($data['article']);
            // 评论区自己的头像
            if($username != "no_login"){
                $sql = $dbh->prepare("select avatar from blog_users where username = :username");
                $sql->bindParam(':username', $username);
                $sql->execute();
                $res = $sql->fetch();
                $data['avatar'] = $res;
            }else{
                $data['avatar'][0] = "img/no_login.png";
            }
            // 评论内容区的头像
            if($data['article']){
                $data['article'][0]['create_time'] = date('Y-m-d H:i:s', $data['article'][0]['create_time']);
                $sql2 = $dbh->prepare("SELECT * FROM comments where article_id=:article_id");
                $sql2->bindParam(':article_id',$article_id);
                $sql2->execute();
                $data['comments'] = $sql2->fetchAll();
                $comments_num = count($data['comments']);
                $sql_ava = $dbh->prepare("select avatar from blog_users where username=:username");
                if($comments_num){
                    foreach ($data['comments'] as $k=>$v){
                        $sql_ava->bindParam(':username',$v['username']);
                        $sql_ava->execute();
                        $avatar = $sql_ava->fetch();
                        //echo $avatar['avatar'];
                        $data['comments'][$k]['avatar']=$avatar['avatar'];
                        $data['comments'][$k]['comment_time'] = date('Y-m-d H:i:s', $v['comment_time']);
                    }
                }

                echo json_encode($data);
            }
            else{
                echo json_encode(array("code"=>0));
            }
            break;

    }

