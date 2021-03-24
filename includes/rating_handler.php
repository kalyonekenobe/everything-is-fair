<?php
include("db_connect.php");
include("functions.php");


if($_POST['actionName'] == "add"){
  $competitionId = $_POST['competitionId'];
  $buttonId = $_POST['buttonId'];
  $userIP = $_SERVER['REMOTE_ADDR'];
  $userIP2 = str_replace(".", "_", $_SERVER['REMOTE_ADDR']);
  if($buttonId == "like"){
    $competition = getCompetitionById($link, $competitionId);
    $competitionLikes = $competition['competition_likes'] + 1;
    if($_COOKIE[$competitionId.":".$userIP2.':dislike'] == true){
      $competitionDislikes = $competition['competition_dislikes'] - 1;
      setcookie($competitionId.":".$userIP.":dislike", "", 0, "/");
    }else $competitionDislikes = $competition['competition_dislikes'];
    $newCompetitionRating = $competitionLikes.":".$competitionDislikes;
    mysqli_query($link, "UPDATE competitions SET competition_rating='$newCompetitionRating' WHERE competition_id=$competitionId");
    setcookie($competitionId.":".$userIP.":like", true, time() + 60 * 60 * 24 * 31 * 100, "/");
  }else{
    $competition = getCompetitionById($link, $competitionId);
    if($_COOKIE[$competitionId.":".$userIP2.':like'] == true){
      $competitionLikes = $competition['competition_likes'] - 1;
      setcookie($competitionId.":".$userIP.":like", "", 0, "/");
    }else $competitionLikes = $competition['competition_likes'];
    $competitionDislikes = $competition['competition_dislikes'] + 1;
    $newCompetitionRating = $competitionLikes.":".$competitionDislikes;
    mysqli_query($link, "UPDATE competitions SET competition_rating='$newCompetitionRating' WHERE competition_id=$competitionId");
    setcookie($competitionId.":".$userIP.":dislike", true, time() + 60 * 60 * 24 * 31 * 100, "/");
  }
}

if($_POST['actionName'] == "remove"){
  $competitionId = $_POST['competitionId'];
  $buttonId = $_POST['buttonId'];
  $userIP = $_SERVER['REMOTE_ADDR'];
  if($buttonId == "like"){
    $competition = getCompetitionById($link, $competitionId);
    $competitionLikes = $competition['competition_likes'] - 1;
    if($_COOKIE[$competitionId.":".$userIP2.':dislike'] == true){
      $competitionDislikes = $competition['competition_dislikes'] + 1;
      setcookie($competitionId.":".$userIP.":dislike", "", 0, "/");
    }else $competitionDislikes = $competition['competition_dislikes'];
    $newCompetitionRating = $competitionLikes.":".$competitionDislikes;
    mysqli_query($link, "UPDATE competitions SET competition_rating='$newCompetitionRating' WHERE competition_id=$competitionId");
    setcookie($competitionId.":".$userIP.":like", "", 0, "/");
  }else{
    $competition = getCompetitionById($link, $competitionId);
    if($_COOKIE[$competitionId.":".$userIP2.':like'] == true){
      $competitionLikes = $competition['competition_likes'] + 1;
      setcookie($competitionId.":".$userIP.":like", "", 0, "/");
    }else $competitionLikes = $competition['competition_likes'];
    $competitionDislikes = $competition['competition_dislikes'] - 1;
    $newCompetitionRating = $competitionLikes.":".$competitionDislikes;
    mysqli_query($link, "UPDATE competitions SET competition_rating='$newCompetitionRating' WHERE competition_id=$competitionId");
    setcookie($competitionId.":".$userIP.":dislike", "", 0, "/");
  }
}


if($_POST['actionName'] == "add_review"){
  $author = $_POST['author'];
  $text = $_POST['text'];
  $ip = $_SERVER["REMOTE_ADDR"];
  mysqli_query($link, "INSERT INTO reviews(review_author, review_text, review_author_ip) VALUES ('".$author."', '".$text."', '".$ip."')");
  print_r("success");
}

if($_POST['actionName'] == "banUser"){
  $userIp = $_POST['userIp'];
  $datetime = $_POST['datetime'];
  $currentDate = date('Y-m-d H:i:s');
  $result = mysqli_query($link, "SELECT * FROM dark_list WHERE ban_time > '$currentDate'");
  if(mysqli_num_rows($result) == 0)
    mysqli_query($link, "INSERT INTO dark_list(user_ip, ban_time) VALUES ('".$userIp."', '".$datetime."')");
  else
    mysqli_query($link, "UPDATE dark_list SET ban_time='$datetime' WHERE user_ip='$userIp'");
  print_r("success");
}

if($_POST['actionName'] == "sendMessage"){
  $author = $_POST['author'];
  $text = $_POST['text'];
  $ip = $_SERVER['REMOTE_ADDR'];
  if(isset($_SESSION['auth_user_id'])) $senderId = $_SESSION['auth_user_id'];
  else $senderId = 0;
  if(isset($_POST['recipient_id'])) $recipientId = $_POST['recipient_id'];
  else $recipientId = 59;
  mysqli_query($link, "INSERT INTO messages(message_author, message_text, sender_id, recipient_id, sender_ip) VALUES ('".$author."', '".$text."', '".$senderId."', '".$recipientId."', '".$ip."')");
  print_r("success");
}

if($_POST['mainAction'] == "writeLog"){
  $year = date("Y", time());
  $month = date("m", time());
  $day = date("d", time());
  $path = "../admin/logs/";
  $dirContent = scandir($path, true);
  $findFolder = array_search($year, $dirContent);
  if($findFolder === false) mkdir($path.$year);
  $path = $path.$year.'/';
  $dirContent = scandir($path, true);
  $findFolder = array_search($month, $dirContent);
  if($findFolder === false) mkdir($path.$month);
  $path = $path.$month.'/'.$day;
  date_default_timezone_set("UTC");
  $date = new DateTime('now', new DateTimeZone('Europe/Kiev'));
  $_POST['date'] = $date;
  $_POST['user_id'] = $_SESSION['auth_user_id'];
  $_POST['user_ip'] = $_SERVER['REMOTE_ADDR'];
  $file = fopen($path.".txt", "a+") or die();
  fputs($file, json_encode($_POST, JSON_UNESCAPED_UNICODE)."\r\n");
  fclose($file);
}
