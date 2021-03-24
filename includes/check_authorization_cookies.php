<?php
if(!$_SESSION["auth_user"]){
  $rememberme = $_COOKIE["rememberme"];
  if(!empty($rememberme)){
    $login = substr($rememberme, 0, strpos($rememberme, '+'));
    $password = substr($rememberme, strpos($rememberme, '+') + 1, strlen($rememberme));
    $userAuthorizationData = getUserAuthorizationData($link, $login, $password);
    if(!empty($userAuthorizationData)){
      Authorizate($link, $userAuthorizationData);
    }
  }
}
