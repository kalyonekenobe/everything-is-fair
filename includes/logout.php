<?php
  session_start();
  $_SESSION = array();
  setcookie('rememberme','',0,'/');
  header("location: ../index.php");
