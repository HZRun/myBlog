
<?php
include("./connect.php");
        # code...
$username="mazong";//$_POST['username'];
$school = "gdufs";//$_POST['school'];


$sql = $dbh->prepare("UPDATE blog_users SET school = :school WHERE username = :username");
$sql->bindParam(':username', $username);
$sql->bindParam(':school', $school);
$flag = $sql->execute();
if($flag){  //如果执行成功返回信息
    echo json_encode(array("code"=>1,"msg"=>"修改成功"));
}




