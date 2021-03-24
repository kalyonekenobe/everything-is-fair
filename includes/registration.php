<?php
include("db_connect.php");
include("functions.php");

$data = $_POST;

$login = strtolower($data["login"]);
$password = strtolower($data["password"]);
$password = strrev($password);
$password = md5("vu3el28f7nf5jaoxashf".$password."g1u46dma8d1o99claed3zq");
$email = strtolower($data["email"]);
$role = $data['role'];

$loginExistence = checkLoginExistence($link, $login);

if($loginExistence){
  print_r("loginExistence");
}else{
  $ip = $_SERVER["REMOTE_ADDR"];
  mysqli_query($link, "INSERT INTO users (user_login, user_password, user_email, user_role_id, user_ip)
                       VALUES ('".$login."', '".$password."', '".$email."', '".$role."', '".$ip."')");

  print_r("success");
}
