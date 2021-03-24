<?php
include("db_connect.php");
include("functions.php");

$data = $_POST;

$login = strtolower($data["login"]);
$password = strtolower($data["password"]);
$password = strrev($password);
$password = md5("vu3el28f7nf5jaoxashf".$password."g1u46dma8d1o99claed3zq");
$rememberme = $data["rememberme"];

$loginExistence = checkLoginExistence($link, $login);
$userBanned = checkUserBanned($link, $_SERVER['REMOTE_ADDR']);
$userBannedDatetime = changeDateFormat(checkUserBannedDatetime($link, $_SERVER['REMOTE_ADDR']), "d-m-Y H:i");

if($userBanned){
  print_r("userBanned#".$userBannedDatetime);
}else if(!$loginExistence){
  print_r("incorrectLogin");
}else{
  $userAuthorizationData = getUserAuthorizationData($link, $login, $password);
  if(!$userAuthorizationData){
    print_r("incorrectPassword");
  }else{
    Authorizate($link, $userAuthorizationData);
    if($rememberme == "true") setcookie('rememberme', $login.'+'.$password, time() + 3600 * 24 * 31, "/");
    print_r("success");
  }
}
