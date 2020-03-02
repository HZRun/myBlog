<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "blog";
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
try{
    $dbh = new PDO($dsn, $user, $pass ,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}catch(PDOException $e){
    die("Error: ".$e->getMessage()."<br>");
}