<?php

function getHeaderClassName($pageName){
  if($pageName == "index.php")
    $headerClassName = "full-header";
  else
    $headerClassName = "header";
  return $headerClassName;
}

function checkLoginExistence($link, $login){
  $result = mysqli_query($link, "SELECT user_login FROM users WHERE user_login='$login'");
  if(mysqli_num_rows($result) > 0)
    return true;
  else
    return false;
}

function getUserAuthorizationData($link, $login, $password){
  $result = mysqli_query($link, "SELECT * FROM users WHERE user_login='$login' AND user_password='$password'");
  if(mysqli_num_rows($result) == 0)
    return false;
  else{
    $userData = mysqli_fetch_object($result);
    return $userData;
  }
}

function Authorizate($link, $userAuthorizationData){
  $userBanned = checkUserBanned($link, $userAuthorizationData->ip);
  if(!$userBanned){
    session_start();
    $_SESSION['auth_user'] = true;
    $_SESSION['auth_user_id'] = $userAuthorizationData->user_id;
    $_SESSION['auth_user_login'] = $userAuthorizationData->user_login;
    $_SESSION['auth_user_password'] = $userAuthorizationData->user_password;
    $_SESSION['auth_user_email'] = $userAuthorizationData->user_email;
    $_SESSION['auth_user_phone'] = $userAuthorizationData->user_phone;
    $_SESSION['auth_user_address'] = $userAuthorizationData->user_address;
    $_SESSION['auth_user_birth_date'] = $userAuthorizationData->user_birth_date;
    $_SESSION['auth_user_first_name'] = $userAuthorizationData->user_first_name;
    $_SESSION['auth_user_middle_name'] = $userAuthorizationData->user_middle_name;
    $_SESSION['auth_user_last_name'] = $userAuthorizationData->user_last_name;
    $_SESSION['auth_user_sex'] = $userAuthorizationData->user_sex;
    $_SESSION['auth_user_country'] = $userAuthorizationData->user_country;
    $_SESSION['auth_user_city'] = $userAuthorizationData->user_city;
    $_SESSION['auth_user_organization_name'] = $userAuthorizationData->user_organization_name;
    $_SESSION['auth_user_role_id'] = $userAuthorizationData->user_role_id;
    $_SESSION['auth_user_image_name'] = $userAuthorizationData->user_image;
    $_SESSION['auth_user_ip'] = $userAuthorizationData->user_ip;
    $_SESSION['auth_user_profile_filled'] = checkUserAccountOccupancy(getPersonalUserData($link));
  }
}

function checkUserBanned($link, $userIp){
  $result = mysqli_query($link, "SELECT * FROM dark_list WHERE user_ip='$userIp' ORDER BY ban_time DESC");
  $datetime = mysqli_fetch_assoc($result)['ban_time'];
  if($datetime > date('Y-m-d H:i:s')) return true;
  else return false;
}

function checkUserBannedDatetime($link, $userIp){
  $result = mysqli_query($link, "SELECT * FROM dark_list WHERE user_ip='$userIp' ORDER BY ban_time DESC");
  $datetime = mysqli_fetch_assoc($result)['ban_time'];
  return $datetime;
}

function getFullBlackList($link){
  $blacklist = [];
  $result = mysqli_query($link, "SELECT * FROM dark_list");
  while($row = mysqli_fetch_assoc($result)){
    $blacklist[] = $row;
  }
  return $blacklist;
}

function getPersonalUserData($link){
  $personalUserData = [];
  $personalUserData['auth'] = $_SESSION['auth_user'];
  $personalUserData['id'] = $_SESSION['auth_user_id'];
  $personalUserData['login'] = $_SESSION['auth_user_login'];
  $personalUserData['password'] = $_SESSION['auth_user_password'];
  $personalUserData['email'] = $_SESSION['auth_user_email'];
  $personalUserData['phone'] = $_SESSION['auth_user_phone'];
  $personalUserData['address'] = $_SESSION['auth_user_address'];
  $personalUserData['birth_date'] = $_SESSION['auth_user_birth_date'];
  $personalUserData['first_name'] = $_SESSION['auth_user_first_name'];
  $personalUserData['middle_name'] = $_SESSION['auth_user_middle_name'];
  $personalUserData['last_name'] = $_SESSION['auth_user_last_name'];
  $personalUserData['role_id'] = $_SESSION['auth_user_role_id'];
  $personalUserData['user_image'] = $_SESSION['auth_user_image_name'];
  $personalUserData['sex'] = $_SESSION['auth_user_sex'];
  $personalUserData['country'] = $_SESSION['auth_user_country'];
  $personalUserData['city'] = $_SESSION['auth_user_city'];
  $personalUserData['user_organization_name'] = $_SESSION['auth_user_organization_name'];
  $personalUserData['role_name'] = getUserRoleName($link, $personalUserData['role_id']);
  $personalUserData['privileges'] = getAllUserPrivileges($link, $personalUserData['role_id']);
  $personalUserData['competitions']['created'] = getUserCreatedCompetitions($link, $personalUserData['id']);
  $personalUserData['competitions']['take_part'] = getUserTakePartCompetitions($link, $personalUserData['id']);
  $personalUserData['competitions']['judge'] = getUserJudgeCompetitions($link, $personalUserData['id']);
  $personalUserData['ip'] = $_SESSION['auth_user_ip'];
  return $personalUserData;
}

function getUserRoleName($link, $userRoleId){
  $result = mysqli_query($link, "SELECT * FROM user_roles WHERE user_role_id=$userRoleId");
  $userRole = mysqli_fetch_object($result);
  return $userRole->user_role_name;
}

function getAllUserPrivileges($link, $userRoleId){
  $privileges = [];
  $result = mysqli_query($link, "SELECT * FROM privileges WHERE user_role_id=$userRoleId");
  while($row = mysqli_fetch_assoc($result)){
    $privilegeName = $row['privilege_name'];
    $privileges[$privilegeName] = true;
  }
  return $privileges;
}

function getUserCreatedCompetitions($link, $userId){
  $competitions = [];
  $result = mysqli_query($link, "SELECT * FROM competitions WHERE creator_id=$userId ORDER BY competition_begining DESC");
  while($row = mysqli_fetch_assoc($result)){
    $competitions[] = $row;
  }
  return $competitions;
}

function getUserTakePartCompetitions($link, $userId){
  $competitions = [];
  $result = mysqli_query($link, "SELECT * FROM participants, competitions WHERE participants.user_id=$userId AND participants.competition_id=competitions.competition_id ORDER BY competitions.competition_begining DESC");
  while($row = mysqli_fetch_assoc($result)){
    $competitions[] = getCompetitionById($link, $row['competition_id']);
  }
  return $competitions;
}

function checkCompetitionUserId($link, $userId){
  $result = mysqli_query($link, "SELECT * FROM participants WHERE competition_user_id='$userId'");
  $correct = mysqli_num_rows($result);
  if($correct > 0) return 1;
  else return 0;
}

function getYearsLogsFolders(){
  $path = "admin/logs/";
  $dirContent = scandir($path, true);
  $folders = [];
  foreach($dirContent as $folder){
    if($folder != "." && $folder != "..") $folders[] = $folder;
  }
  return $folders;
}

function getMonthsLogsFolders($year){
  $path = "admin/logs/".$year.'/';
  $dirContent = scandir($path, true);
  $folders = [];
  foreach($dirContent as $folder){
    if($folder != "." && $folder != "..") $folders[] = $folder;
  }
  return $folders;
}

function getDaysLogsFolders($year, $month){
  $path = "admin/logs/".$year.'/'.$month.'/';
  $dirContent = scandir($path, true);
  $folders = [];
  foreach($dirContent as $folder){
    if($folder != "." && $folder != "..") $folders[] = str_replace(".txt", "", $folder);
  }
  return $folders;
}

function getAllEvents($year, $month, $day){
  $path = "admin/logs/".$year.'/'.$month.'/'.$day.".txt";
  $file = fopen($path, "a+") or die();
  $events = [];
  while(!feof($file)){
    $str = fgets($file);
    if($str != "") $events[] = (array)json_decode($str);
  }
  fclose($file);
  return $events;
}

function getMonthNameByNum($monthNum){
  if($monthNum == "01") return "Січень";
  if($monthNum == "02") return "Лютий";
  if($monthNum == "03") return "Березень";
  if($monthNum == "04") return "Квітень";
  if($monthNum == "05") return "Травень";
  if($monthNum == "06") return "Червень";
  if($monthNum == "07") return "Липень";
  if($monthNum == "08") return "Серпень";
  if($monthNum == "09") return "Вересень";
  if($monthNum == "10") return "Жовтень";
  if($monthNum == "11") return "Листопад";
  if($monthNum == "12") return "Грудень";
}

function getCompetitionUserId($link, $userId, $competitionId){
  $result = mysqli_query($link, "SELECT * FROM participants WHERE user_id=$userId AND competition_id=$competitionId");
  $competitionUserId = mysqli_fetch_object($result)->competition_user_id;
  return $competitionUserId;
}

function getUserJudgeCompetitions($link, $userId){
  $competitions = [];
  $result = mysqli_query($link, "SELECT * FROM judges, competitions WHERE judges.competition_id=competitions.competition_id ORDER BY competitions.competition_begining DESC");
  while($row = mysqli_fetch_assoc($result)){
    $competitionJudges = json_decode($row['judges_id']);
    foreach($competitionJudges as $judge){
      if($judge == $userId){
        $competitions[] = getCompetitionById($link, $row['competition_id']);
        break;
      }
    }
  }
  return $competitions;
}

function getCompetitionById($link, $competitionId){
  $competition = [];
  $result = mysqli_query($link, "SELECT * FROM competitions WHERE competition_id=$competitionId");
  $competition = (array)mysqli_fetch_assoc($result);
  $competition['competition_judges'] = getAllCompetitionJudges($link, $competitionId);
  $competition['competition_judges_id'] = $competition['competition_judges']['competition_judges_id'];
  unset($competition['competition_judges']['competition_judges_id']);
  $competition['competition_criteria'] = getAllCompetitionCriteria($link, $competitionId);
  $competition['competition_judges_names_list'] = getCompetitionJudgesNamesList($link, $competitionId);
  $competition['competition_tags'] = (array)json_decode($competition['competition_tags']);
  $competition['competition_countries'] = str_replace(["[", "]"], "", $competition['competition_countries']);
  $competition['competition_countries'] = explode(", ", $competition['competition_countries']);
  $competition['competition_countries_names'] = getCountryNamesById($link, $competition['competition_countries']);
  $competition['competition_tags_names'] = getTagsNamesById($link, $competition['competition_tags']);
  $competition['competition_min_age'] = explode("-", $competition['competition_age_range'])[0];
  $competition['competition_max_age'] = explode("-", $competition['competition_age_range'])[1];
  $competition['competition_certificates_status'] = explode('&', $competition['competition_proportion_places'])[0];
  $competition['competition_certificates_status'] = $competition['competition_certificates_status'][strlen($competition['competition_certificates_status']) - 1];
  $competition['competition_certificates_value'] = explode('&', $competition['competition_proportion_places'])[1];
  $competition['competition_certificates_value'] = str_replace(["[", "]"], "", $competition['competition_certificates_value']);
  $competition['competition_likes'] = intval(explode(":", $competition['competition_rating'])[0]);
  $competition['competition_dislikes'] = intval(explode(":", $competition['competition_rating'])[1]);
  $competition['competition_votes'] = $competition['competition_likes'] + $competition['competition_dislikes'];
  if($competition['competition_votes'] > 0) $competition['rating_percentage'] = 100 / $competition['competition_votes'] * $competition['competition_likes'];
  else $competition['rating_percentage'] = 0;
  return $competition;
}

function getCountryNamesById($link, $countriesId){
  $countriesNames = [];
  foreach ($countriesId as $country) {
    $result = mysqli_query($link, "SELECT * FROM country, countries_names WHERE countries_names.title_en=LOWER(country.Name) AND country.Code2='$country' ORDER BY country.Name");
    $row = (array)mysqli_fetch_object($result);
      $countriesNames[] = $row['title_ua'];
  }
  $countriesNames = implode(", ", $countriesNames);
  return $countriesNames;
}

function getCountryByCountryCode($link, $countryCode){
  $result = mysqli_query($link, "SELECT * FROM country, countries_names WHERE country.name=countries_names.title_en AND country.Code2='$countryCode'");
  $country = mysqli_fetch_object($result);
  return $country;
}

function getTagById($link, $tagId){
  $result = mysqli_query($link, "SELECT * FROM competition_tags WHERE tag_id=$tagId");
  $tag = mysqli_fetch_object($result);
  return $tag;
}

function getTagsNamesById($link, $tagsId){
  $tagsNames = [];
  foreach ($tagsId as $tag) {
    $result = mysqli_query($link, "SELECT * FROM competition_tags WHERE tag_id='$tag'");
    $row = (array)mysqli_fetch_object($result);
      $tagsNames[] = $row['tag_name'];
  }
  $tagsNames = implode(", ", $tagsNames);
  return $tagsNames;
}

function getUserById($link, $userId){
  $result = mysqli_query($link, "SELECT * FROM users WHERE user_id=$userId");
  $user = mysqli_fetch_assoc($result);
  return $user;
}

function getUserNameById($link, $userId){
  $result = mysqli_query($link, "SELECT * FROM users WHERE user_id=$userId");
  $user = mysqli_fetch_object($result);
  return $user->user_last_name." ".$user->user_first_name." ".$user->user_middle_name;
}

function getAllJudges($link){
  $users = [];
  $result = mysqli_query($link, "SELECT * FROM users WHERE user_role_id=3");
  while($row = mysqli_fetch_assoc($result)){
    $users[] = $row;
  }
  return $users;
}

function getAllOrganizers($link){
  $organizers = [];
  $result = mysqli_query($link, "SELECT * FROM competitions GROUP BY competition_organizer");
  while($row = mysqli_fetch_assoc($result)){
    $organizers[] = $row;
  }
  return $organizers;
}

function checkUserAccountOccupancy($userData){
  $filled = true;
  if($userData['role_id'] == 3 || $userData['role_id'] == 4){
    if($userData['user_image'] == "") $filled = false;
    if($userData['address'] == "") $filled = false;
    if($userData['sex'] == "") $filled = false;
    if($userData['birth_date'] == "") $filled = fasle;
  }
  if($userData['role_id'] != 2){
    if($userData['first_name'] == "") $filled = false;
    if($userData['last_name'] == "") $filled = false;
    if($userData['middle_name'] == "") $filled = false;
  }
  if($userData['role_id'] == 2){
    if($userData['user_organization_name'] == "") $filled = false;
  }
  if($userData['email'] == "") $filled = false;
  if($userData['phone'] == "") $filled = false;
  if($userData['country'] == "") $filled = false;
  if($userData['city'] == "") $filled = false;
  return $filled;
}

function getParticipantData($link, $competitionId, $userId){
  $result = mysqli_query($link, "SELECT * FROM participants WHERE user_id=$userId AND competition_id=$competitionId");
  $participant = (array)mysqli_fetch_object($result);
  $participant['participant_work_files'] = json_decode($participant['participant_work_files']);
  return $participant;
}

function getExtension($fileName){
  $extension = "";
  for($i = strlen($fileName) - 1; $i >= 0 && $fileName[$i] != "."; $i--){
    $extension .= $fileName[$i];
  }
  return strrev($extension);
}

function getAllCompetitions($link){
  $competitions = [];
  $result = mysqli_query($link, "SELECT * FROM competitions ORDER BY competition_id DESC");
  while($row = mysqli_fetch_assoc($result)){
    $row['competition_judges'] = getAllCompetitionJudges($link, $row['competition_id']);
    $row['competition_judges_names_list'] = getCompetitionJudgesNamesList($link, $row['competition_id']);
    $row['competition_criteria'] = getAllCompetitionCriteria($link, $row['competition_id']);
    $row['competition_tags'] = (array)json_decode($row['competition_tags']);
    $row['competition_countries'] = (array)json_decode($row['competition_countries']);
    $row['competition_likes'] = intval(explode(":", $row['competition_rating'])[0]);
    $row['competition_dislikes'] = intval(explode(":", $row['competition_rating'])[1]);
    $row['competition_votes'] = $row['competition_likes'] + $row['competition_dislikes'];
    if($row['competition_votes'] > 0) $row['rating_percentage'] = 100 / $row['competition_votes'] * $row['competition_likes'];
    else $row['rating_percentage'] = 0;
    $competitions[] = $row;
  }
  return $competitions;
}

function getAllCompetitionsByParams($link, $params = []){
  $competitions = [];
  if($params['q'] != "") $search = $params['q'];
  else $search = "";
  if($params['status'] != "") $status = explode(",", $params['status']);
  else $params['status'] = "1,2,3";
  if($params['min_age'] != "") $minAge = $params['min_age'];
  else $minAge = 0;
  if($params['max_age'] != "") $maxAge = $params['max_age'];
  else $maxAge = 150;
  $begining = $params['begining'];
  $ending = $params['ending'];
  $gender = $params['participant_sex'];
  if($params['countries'] != "") $countries = explode(",", $params['countries']);
  else $countries = "all";
  if($params['tags'] != "") $tags = explode(",", $params['tags']);
  else $tags = [];
  $organizers = implode("','", explode(',', $params['organizers']));
  if($params['min_rating']) $minRating = $params['min_rating'];
  else $minRating = 0;
  if($params['max_rating']) $maxRating = $params['max_rating'];
  else $maxRating = 100;
  $query = [];
  if($begining != "") $query[] = "competition_begining >= '$begining'";
  if($ending != "") $query[] = "competition_ending <= '$ending'";
  if($gender != "") $query[] = "competition_sex = '$gender'";
  if($organizers != "") $query[] = "competition_organizer IN ('$organizers')";
  if($search != "") $query[] = "competition_name LIKE '%$search%'";
  $query = implode(" AND ", $query);
  if($query != "") $query = "WHERE ".$query;
  $result = mysqli_query($link, "SELECT * FROM competitions $query ORDER BY competition_id DESC");
  while($row = mysqli_fetch_assoc($result)){
    $row['competition_judges'] = getAllCompetitionJudges($link, $row['competition_id']);
    $row['competition_judges_names_list'] = getCompetitionJudgesNamesList($link, $row['competition_id']);
    $row['competition_criteria'] = getAllCompetitionCriteria($link, $row['competition_id']);
    $row['competition_tags'] = (array)json_decode($row['competition_tags']);
    $row['competition_countries'] = explode(', ', str_replace(["[", "]"], "", $row['competition_countries']));
    $row['competition_likes'] = intval(explode(":", $row['competition_rating'])[0]);
    $row['competition_dislikes'] = intval(explode(":", $row['competition_rating'])[1]);
    $row['competition_votes'] = $row['competition_likes'] + $row['competition_dislikes'];
    if($row['competition_votes'] > 0) $row['rating_percentage'] = 100 / $row['competition_votes'] * $row['competition_likes'];
    else $row['rating_percentage'] = 0;
    $competitionAgeRange = explode("-", $row['competition_age_range']);
    $errors = false;
    if(count($status) > 0) if(array_search(checkCompetitionStatus($link, $row['competition_id']), $status) === false) $errors = true;
    if($competitionAgeRange[0] > $maxAge) $errors = true;
    if($competitionAgeRange[1] < $minAge) $errors = true;
    if($row['rating_percentage'] < $minRating) $errors = true;
    if($row['rating_percentage'] > $maxRating) $errors = true;
    if(gettype($countries) == "array"){
      if($row['competition_countries'][0] != "all")
        if(count(array_intersect($countries, $row['competition_countries'])) == 0) $errors = true;
    }
    if(count($tags) > 0) if(count(array_intersect($tags, $row['competition_tags'])) == 0) $errors = true;
    if(!$errors) $competitions[] = $row;
  }
  return $competitions;
}


function getLatestCompetitions($link, $limit){
  $competitions = [];
  $result = mysqli_query($link, "SELECT * FROM competitions ORDER BY competition_id DESC");
  $iterator = 0;
  while($row = mysqli_fetch_assoc($result)){
    $row['competition_judges'] = getAllCompetitionJudges($link, $row['competition_id']);
    $row['competition_judges_names_list'] = getCompetitionJudgesNamesList($link, $row['competition_id']);
    $row['competition_criteria'] = getAllCompetitionCriteria($link, $row['competition_id']);
    $row['competition_tags'] = (array)json_decode($row['competition_tags']);
    $row['competition_countries'] = (array)json_decode($row['competition_countries']);
    $row['competition_likes'] = intval(explode(":", $row['competition_rating'])[0]);
    $row['competition_dislikes'] = intval(explode(":", $row['competition_rating'])[1]);
    $row['competition_votes'] = $row['competition_likes'] + $row['competition_dislikes'];
    if($row['competition_votes'] > 0) $row['rating_percentage'] = 100 / $row['competition_votes'] * $row['competition_likes'];
    else $row['rating_percentage'] = 0;
    if(checkCompetitionStatus($link, $row['competition_id']) == 3){
      $competitions[] = $row;
      $iterator++;
    }
    if($iterator == $limit) break;
  }
  return $competitions;
}

function getAllCompetitionJudges($link, $competitionId){
  $judges = [];
  $result = mysqli_query($link, "SELECT * FROM judges WHERE competition_id=$competitionId");
  while($row = mysqli_fetch_assoc($result)){
    $competitionJudges = json_decode($row['judges_id']);
    $judges['competition_judges_id'] = $competitionJudges;
    foreach($competitionJudges as $judge){
      $judges[] = getUserById($link, $judge);
    }
  }
  return $judges;
}

function getCompetitionJudgesNumber($link, $competitionId){
  $result = mysqli_query($link, "SELECT * FROM judges WHERE competition_id=$competitionId");
  $judgesList = (array)mysqli_fetch_object($result);
  $judgesList = json_decode($judgesList['judges_id']);
  return count($judgesList);
}

function getAllCompetitionCriteria($link, $competitionId){
  $data = []; $criteria = [];
  $result = mysqli_query($link, "SELECT * FROM assessment_criteria WHERE competition_id=$competitionId");
  $row = mysqli_fetch_assoc($result);
  $row['assessment_criteria_names'] = substr($row['assessment_criteria_names'], 1, strlen($row['assessment_criteria_names']) - 2);
  $row['assessment_criteria_names'] = explode('| ', $row['assessment_criteria_names']);
  $data['criteria_names'] = $row['assessment_criteria_names'];
  $data['criteria_min_values'] = json_decode($row['assessment_criteria_min_value']);
  $data['criteria_max_values'] = json_decode($row['assessment_criteria_max_value']);
  for($i = 0; $i < count($data['criteria_names']); $i++){
    $criteria[$i]['name'] = $data['criteria_names'][$i];
    $criteria[$i]['min_value'] = $data['criteria_min_values'][$i];
    $criteria[$i]['max_value'] = $data['criteria_max_values'][$i];
  }
  return $criteria;
}

function getUserSummaryValue($link, $userId){
  $result = mysqli_query($link, "SELECT summary FROM evaluation_table WHERE user_id=$userId");
  if(mysqli_num_rows($result) == 0) return 0;
  else return mysqli_fetch_assoc($result)['summary'];
}

function getAllCompetitionParticipants($link, $competitionId){
  $participants = [];
  $result = mysqli_query($link, "SELECT * FROM participants WHERE competition_id=$competitionId ORDER BY user_id ASC");
  while($row = mysqli_fetch_assoc($result)){
    $summary = getUserSummaryValue($link, $row['user_id']);
    $row['summary'] = $summary;
    $participants[] = $row;
  }
  return $participants;
}

function changeDateFormat($date, $format){
  return date_format(date_create($date), $format);
}

function getCompetitionPlaces($link, $competitionId){
  $places = [];
  $result = mysqli_query($link, "SELECT competition_proportion_places FROM competitions WHERE competition_id=$competitionId");
  $places = mysqli_fetch_assoc($result);
  return $places;
}

function getCompetitionTable($link, $judgeId, $competitionId){
  $table = [];
  $result = mysqli_query($link, "SELECT * FROM evaluation_table WHERE competition_id=$competitionId AND judge_id=$judgeId");
  $places = getCompetitionPlaces($link, $competitionId);
  $participants = getAllCompetitionParticipants($link, $competitionId);
  while($row = mysqli_fetch_assoc($result)){
    $userId = $row['user_id'];
    $table[$userId]['values'] = json_decode($row['evaluation_values']);
    $table[$userId]['summary'] = 0;
    $table[$userId]['summary'] = $row['summary'];
    $table[$userId]['commentaries'] = json_decode($row['evaluation_commentaries']);
  }
  uasort($table, function($a, $b){
    return $a['summary'] < $b['summary'];
  });
  $it1 = 1; $it2 = 1;
  for($i = 0; $i < count($participants); $i++){
    $participant = $participants[$i];
    $userId = $participant['user_id'];
    if($i < count($participants) - 1){
      if($table[$participant['user_id']]['summary'] == $table[$participants[$i + 1]['user_id']]['summary']) $it2++;
      else{
        if($it1 != $it2){
          for($k = $it1; $k < $it2; $k++){
            $table[$participants[$k]['user_id']]['place'] = $it1.'-'.$it2;
          }
        }else $table[$participants[$i]['user_id']]['place'] = $it1;
        $it1 = $it2; $it1++; $it2++;
      }
    }else{
      if($it1 != $it2){
        for($k = $it1 - 1; $k < $it2; $k++){
          $table[$participants[$k]['user_id']]['place'] = $it1.'-'.$it2;
        }
      }else $table[$participants[$i]['user_id']]['place'] = $it1;
    }
  }
  return $table;
}

function checkOutRegistration($link, $competitionId, $userId){
  $result = mysqli_query($link, "SELECT * FROM participants WHERE user_id=$userId AND competition_id=$competitionId");
  if(mysqli_num_rows($result) == 0) return true;
  else return false;
}

function checkOutParticipantRole($link, $competitionId, $userId){
  $judges = getAllCompetitionJudges($link, $competitionId);
  foreach($judges as $judge) {
    if($judge['user_id'] == $userId) return "judge";
  }
  $competition = getCompetitionById($link, $competitionId);
  if($competition['creator_id'] == $userId) return "creator";
  $participants = getAllCompetitionParticipants($link, $competitionId);
  foreach($participants as $participant) {
    if($participant['user_id'] == $userId) return "participant";
  }
  return "";
}

function checkCompetititionComparement($link, $userId, $competitionId){
  $user = getUserById($link, $userId);
  $competition = getCompetitionById($link, $competitionId);
  $errors = false;
  $userAge = floor(((time() - strtotime(strval($user['user_birth_date']))) / 60 / 60 / 24 / 365));
  $startAge = explode("-", $competition['competition_age_range'])[0];
  $endAge = explode("-", $competition['competition_age_range'])[1];
  if($userAge < $startAge || $userAge > $endAge) $errors = true;
  $flag = false;
  foreach($competition['competition_countries'] as $country) {
    if($country == $user['user_country']){
      $flag = true;
      break;
    }
  }
  if($competition['competition_countries'][0] == "all") $flag = true;
  if($flag == false) $errors = true;
  if($competition['competition_sex'] != $user['user_sex'] && $competition['competition_sex'] != "Будь-яка") $errors = true;
  return $errors;
}

function checkCompetitionStatus($link, $competitionId){
  $result = mysqli_query($link, "SELECT * FROM competitions WHERE competition_id=$competitionId");
  $competition = (array)mysqli_fetch_object($result);
  $competitionBegining = $competition['competition_begining'];
  $competitionEnding = $competition['competition_ending'];
  $currentDatetime = date('Y-m-d H:i:s');
  if($competitionBegining > $currentDatetime)
    return 1;
  else if($competitionEnding < $currentDatetime)
    return 2;
  else
    return 3;
}

function getCompetitionJudgesNamesList($link, $competitionId){
  $judgesNames = [];
  $judges = getAllCompetitionJudges($link, $competitionId);
  foreach ($judges as $judge) {
    if($judge['user_first_name'] != "" && $judge['user_last_name'] != "" && $judge['user_middle_name'] != "")
      $judgesNames[] = $judge['user_last_name']." ".$judge['user_first_name']." ".$judge['user_middle_name'];
  }
  if($judgesNames)
    return implode($judgesNames, ", ");
  else
    return "Інформація поки що відсутня";
}

function getFullCompetitionTable($link, $competitionId){
  $table = [];
  $places = getCompetitionPlaces($link, $competitionId);
  $participants = getAllCompetitionParticipants($link, $competitionId);
  $judges = getAllCompetitionJudges($link, $competitionId);
  $judgesNumber = getCompetitionJudgesNumber($link, $competitionId);
  $result = mysqli_query($link, "SELECT * FROM evaluation_table WHERE competition_id=$competitionId");
  while($row = mysqli_fetch_assoc($result)){
    $userId = $row['user_id'];
    $table[$userId]['values'] = json_decode($row['evaluation_values']);
    for($i = 0; $i < count($table[$userId]['values']); $i++){
      $table[$userId]['finalSummary'][$i] += $table[$userId]['values'][$i];
    }
  }
  $it1 = 1; $it2 = 1;
  for($i = 0; $i < count($participants); $i++){
    $participant = $participants[$i];
    $userId = $participant['user_id'];
    for($j = 0; $j < count($table[$userId]['finalSummary']); $j++){
      $table[$userId]['finalResult'][$j] = round($table[$userId]['finalSummary'][$j] / $judgesNumber, 5);
      $table[$userId]['summary'] += $table[$userId]['finalResult'][$j];
    }
  }
  uasort($table, function($a, $b){
    return $a['summary'] < $b['summary'];
  });
  for($i = 0; $i < count($participants); $i++){
    $participant = $participants[$i];
    $userId = $participant['user_id'];
    if($i < count($participants) - 1){
      if($table[$participant['user_id']]['summary'] == $table[$participants[$i + 1]['user_id']]['summary']) $it2++;
      else{
        if($it1 != $it2){
          for($k = $it1; $k < $it2; $k++){
            $table[$participants[$k]['user_id']]['place'] = $it1.'-'.$it2;
          }
        }else $table[$participants[$i]['user_id']]['place'] = $it1;
        $it1 = $it2; $it1++; $it2++;
      }
    }else{
      if($it1 != $it2){
        for($k = $it1 - 1; $k < $it2; $k++){
          $table[$participants[$k]['user_id']]['place'] = $it1.'-'.$it2;
        }
      }else $table[$participants[$i]['user_id']]['place'] = $it1;
    }
  }
  print_r($competitionTable);
  return $table;
}

function getAllCountries($link){
  $countries = [];
  $result = mysqli_query($link, "SELECT * FROM country, countries_names WHERE countries_names.title_en=LOWER(country.Name) ORDER BY country.Name");
  while($row = mysqli_fetch_assoc($result)){
    $countries[] = $row;
  }
  return $countries;
}

function getAllCompetitionTags($link){
  $tags = [];
  $result = mysqli_query($link, "SELECT * FROM competition_tags");
  while($row = mysqli_fetch_assoc($result)){
    $tags[] = $row;
  }
  return $tags;
}

function getAllJudgesValues($link, $userId, $competitionId){
  $judgesValues = [];
  $result = mysqli_query($link, "SELECT * FROM judges WHERE competition_id=$competitionId");
  $object = mysqli_fetch_object($result);
  $judges = json_decode($object->judges_id);
  foreach ($judges as $judge) {
    $result = mysqli_query($link, "SELECT * FROM users WHERE user_id=$judge");
    $result1 = mysqli_query($link, "SELECT * FROM evaluation_table WHERE user_id=$userId AND judge_id=$judge AND competition_id=$competitionId");
    $table = mysqli_fetch_object($result1);
    $value = [];
    $value['userData'] = mysqli_fetch_assoc($result);
    $value['userAssessmentValues'] = json_decode($table->evaluation_values);
    $value['userAssessmentCommentaries'] = json_decode($table->evaluation_commentaries);
    $value['summary'] = $table->summary;
    $judgesValues[$judge] = $value;
  }
  return $judgesValues;
}

function getRGBA($value, $min, $max){
  $valueRange = $max - $min;
  $red = 255 - 255 / $valueRange * $value;
  $green = 255 / $valueRange * $value;
  return "rgba(".$red.", ".$green.", 0, 0.2)";
}

function getAllReviews($link){
  $reviews = [];
  $result = mysqli_query($link, "SELECT * FROM reviews ORDER BY review_datetime DESC LIMIT 3");
  while($row = mysqli_fetch_assoc($result)){
    $reviews[] = $row;
  }
  return $reviews;
}

function getReviewsByUserIp($link, $userIp){
  $reviews = [];
  $result = mysqli_query($link, "SELECT * FROM reviews WHERE review_author_ip='$userIp' ORDER BY review_datetime DESC");
  while($row = mysqli_fetch_assoc($result)){
    $reviews[] = $row;
  }
  return $reviews;
}

function getMessagesByUserIdOrIp($link, $userId, $userIp){
  $incomeMessages = [];
  $outcomeMessages = [];
  $result = mysqli_query($link, "SELECT * FROM messages WHERE sender_ip='$userIp' OR sender_id=$userId ORDER BY message_datetime DESC");
  while($row = mysqli_fetch_assoc($result)){
    $outcomeMessages[] = $row;
  }
  $result = mysqli_query($link, "SELECT * FROM messages WHERE recipient_id=$userId");
  while($row = mysqli_fetch_assoc($result)){
    $incomeMessages[] = $row;
  }
  $messages['income'] = $incomeMessages;
  $messages['outcome'] = $outcomeMessages;
  return $messages;
}
