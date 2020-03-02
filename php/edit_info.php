<?php
	include("./connect.php");
	switch ($_POST['do']) {
		case 'edit_info':
			# code...
			$username = $_POST['username'];
			$avatar = $_POST['avatar'];
			$mobile = $_POST['mobile'];
			$email = $_POST['email'];
			$github = $_POST['github'];
			$school = $_POST['school'];

			$sql = $dbh->prepare("UPDATE blog_users SET avatar = :avatar, mobile = :mobile, email = :email, school = :school, github = :github WHERE username = :username");
			$sql->bindParam(':username', $username);
			$sql->bindParam(':avatar', $avatar);
			$sql->bindParam(':mobile', $mobile);
			$sql->bindParam(':email', $email);
			$sql->bindParam(':school', $school);
			$sql->bindParam(':github', $github);

			$flag = $sql->execute();
			if($flag){  //如果执行成功返回信息
                echo json_encode(array("code"=>1,"msg"=>"修改成功", "avatar"=>$avatar));
            }
			break;
		
	}
?>