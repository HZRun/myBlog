<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/10
 * Time: 17:07
 */
session_start();
include ("./connect.php"); //连接数据库文件
date_default_timezone_set("Asia/Shanghai");
switch ($_POST['do']){
    case "get_edit_article":
        $article=$_POST['article_id'];
        $username = $_POST['username'];
        $arti=$dbh->prepare("select article_id, content,title from articles where article_id = :article_id");
        $arti->bindParam(':article_id',$article);
        $arti->execute();
        $res['article']=$arti->fetch();

        $tag=$dbh->prepare("select tag from tags where article_id = :article_id");
        $tag->bindParam(':article_id',$article);
        $tag->execute();
        $article_tags=$tag->fetchAll();

        $tag_array = array();
        foreach ($article_tags as $k=>$v){
        $tag_array[$k]=$article_tags[$k]['tag'];
        }

        $all_tag=$dbh->prepare("select distinct tag from tags where username = :username");
        $all_tag->bindParam(':username',$username);
        $all_tag->execute();
        $arti_all_tags=$all_tag->fetchAll();
        foreach($arti_all_tags as $k=>$v){
            if(in_array($v['tag'],$tag_array)){
                $arti_all_tags[$k]['is_select']=true;
            }
            else{
                $arti_all_tags[$k]['is_select']=false;
            }
        }
        $res['tags']=$arti_all_tags;
        echo json_encode($res);
        break;
    case "submit_edit":
        $article_id = $_POST['article_id'];
        $title=$_POST['title'];
        $abstract=$_POST['abstract'];
        $username=$_POST['username'] ; //$_SESSION['username'];
        $content=$_POST['content'];
        if(isset($_POST['tags'])){
            $tags = $_POST['tags'];
        }
        else{
            $tags = array();
        }
        $create_time=time();
        $sql = $dbh->prepare("update articles set title=:title,abstract=:abstract
                   ,content=:content,create_time=:create_time where article_id=:article_id");
        $sql->bindParam(':article_id',$article_id);
        $sql->bindParam(':title',$title);
        $sql->bindParam(':content',$content);
        $sql->bindParam(':abstract',$abstract);
        $sql->bindParam(':create_time',$create_time);
        $flag1 = $sql->execute();

        $o_tag=$dbh->prepare("select tag from tags where article_id=:article_id");
        $o_tag->bindParam(':article_id',$article_id);
        $o_tag->execute();
        $old_tag=$o_tag->fetchAll();

        $tag_list=array();
        $del_tag=$dbh->prepare("DELETE FROM tags WHERE article_id =:article_id and tag=:tag");
        $del_tag->bindParam(':article_id',$article_id);
        foreach ($old_tag as $k=>$v){
            if(!in_array($v['tag'],$tags)){
                $del_tag->bindParam(':tag',$v['tag']);
                $del_tag->execute();
            }
            else{
                array_push($tag_list,$v['tag']);
            }
        }
        $ins_tag=$dbh->prepare("INSERT into tags (username,article_id,tag)
                         values ( :username, :article_id, :tag)");
        $ins_tag->bindParam(':username',$username);
        $ins_tag->bindParam(':article_id',$article_id);
        foreach ($tags as $k=>$v){
            if(!in_array($v,$tag_list)){
                $ins_tag->bindParam(':tag',$v);
                $ins_tag->execute();
            }
        }
        echo json_encode(array("msg"=>"编辑成功"));
        break;

}
