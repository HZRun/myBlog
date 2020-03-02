
<?php
$username="mazong";
$school = "gdufs";
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
$res = $redis->lpush("school", $school);
$result = $redis->lRange("school",0,500);
echo json_encode(array("code"=>1,"msg"=>$result));

