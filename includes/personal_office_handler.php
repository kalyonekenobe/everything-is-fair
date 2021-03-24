<?php
session_start();
include("db_connect.php");
include("functions.php");

if($_POST['action'] == "updateUserData"){
  $data = (array)json_decode($_POST['uploadData']);
  $image = $_POST['image'];
  $email = $data['email'];
  $firstName = $data['first_name'];
  $lastName = $data['last_name'];
  $middleName = $data['middle_name'];
  $phone = $data['phone'];
  $sex = $data['sex'];
  $birthDate = $data['birth_date'];
  $country = $data['country'];
  $city = $data['city'];
  $address = $data['address'];
  $organizationName = $data['organization_name'];
  $userId = $_SESSION['auth_user_id'];

  $uploadImage = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['image']['name']));
	$uploadDirectory = '../uploads_images/';
	$newFileName = 'profile-image-'.rand(10, 10000).'.'.$uploadImage;
	$uploadFile = $uploadDirectory.$newFileName;
  if($_FILES['image']){
    if(@move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)){
      mysqli_query($link, "UPDATE users SET user_image='$newFileName', user_email='$email', user_first_name='$firstName', user_last_name='$lastName', user_middle_name='$middleName', user_phone='$phone', user_sex='$sex', user_birth_date='$birthDate', user_country='$country', user_city='$city', user_address='$address', user_organization_name='$organizationName' WHERE user_id=$userId");
      $_SESSION['auth_user_email'] = $email;
      $_SESSION['auth_user_first_name'] = $firstName;
      $_SESSION['auth_user_last_name'] = $lastName;
      $_SESSION['auth_user_middle_name'] = $middleName;
      $_SESSION['auth_user_phone'] = $phone;
      $_SESSION['auth_user_image_name'] = $newFileName;
      $_SESSION['auth_user_sex'] = $sex;
      $_SESSION['auth_user_birth_date'] = $birthDate;
      $_SESSION['auth_user_country'] = $country;
      $_SESSION['auth_user_city'] = $city;
      $_SESSION['auth_user_address'] = $address;
      $_SESSION['auth_user_organization_name'] = $organizationName;
      $_SESSION['auth_user_profile_filled'] = checkUserAccountOccupancy(getPersonalUserData($link));
      print_r("success");
    }else{
      print_r("Виникла помилка при оновленні профілю! Зверніться до адміністратора сайту для вирішення проблеми.");
    }
  }else{
    mysqli_query($link, "UPDATE users SET user_email='$email', user_first_name='$firstName', user_last_name='$lastName', user_middle_name='$middleName', user_phone='$phone', user_sex='$sex', user_birth_date='$birthDate', user_country='$country', user_city='$city', user_address='$address', user_organization_name='$organizationName' WHERE user_id=$userId");
    $_SESSION['auth_user_email'] = $email;
    $_SESSION['auth_user_first_name'] = $firstName;
    $_SESSION['auth_user_last_name'] = $lastName;
    $_SESSION['auth_user_middle_name'] = $middleName;
    $_SESSION['auth_user_phone'] = $phone;
    $_SESSION['auth_user_sex'] = $sex;
    $_SESSION['auth_user_birth_date'] = $birthDate;
    $_SESSION['auth_user_country'] = $country;
    $_SESSION['auth_user_city'] = $city;
    $_SESSION['auth_user_address'] = $address;
    $_SESSION['auth_user_organization_name'] = $organizationName;
    $_SESSION['auth_user_profile_filled'] = checkUserAccountOccupancy(getPersonalUserData($link));
    print_r("success");
  }
}

if($_POST['requestName'] == 'removeCompetition'){
  $competitionId = $_POST['competitionId'];
  mysqli_query($link, "DELETE FROM competitions WHERE competition_id=$competitionId");
  print_r("deleted");
}

if($_POST['requestName'] == 'getCometitionParameters'){
  $competitionId = $_POST['competitionId'];
  $competition = getCompetitionById($link, $competitionId);
  $competition['competition_begining'] = date_format(date_create($competition['competition_begining']), "d/m/Y h:i");
  $competition['competition_ending'] = date_format(date_create($competition['competition_ending']), "d/m/Y h:i");
  print_r(json_encode($competition));
}

if($_POST['action'] == "sendWork"){
  $competitionId = $_POST['competition_id'];
  $userId = $_SESSION['auth_user_id'];
  $commentary = $_POST['commentary'];
  $exsistedFileNames = $_POST['exsisted_file_names'];
  $files = []; $errors = false;

  for($i = 0; $i < count($_FILES['files']); $i++){
    $tmpFilePath = $_FILES['files']['tmp_name'][$i];
    if($tmpFilePath != ""){
      $uploadFileName = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['files']['name'][$i]));
      $uploadDirectory = '../uploads_files/';
      $newFileName = 'user-file-'.$userId.$competitionId.rand(100, 1000000).'.'.$uploadFileName;
      $uploadFile = $uploadDirectory.$newFileName;
      if(@move_uploaded_file($tmpFilePath, $uploadFile)){
        $files[] = $newFileName;
      }else $errors = true;
    }
  }

  $files = array_merge($files, (array)json_decode($exsistedFileNames));
  $files = json_encode($files, JSON_UNESCAPED_UNICODE);

  if(!$errors){
    $result = mysqli_query($link, "UPDATE participants SET participant_commentary='$commentary', participant_work_files='$files' WHERE competition_id=$competitionId AND user_id=$userId");
    print_r("success");
  }
}

if($_POST['actionName'] == "createCompetition"){
  $data = (array)json_decode($_POST['competitionData']);
  $criteriaNames = $criteriaMinValues = $criteriaMaxValues = [];
  foreach($data['competitionCriteria'] as $criterion){
    $criterion = (array)$criterion;
    $criteriaNames[] = $criterion['criterionName'];
    $criteriaMinValues[] = $criterion['criterionMinValue'];
    $criteriaMaxValues[] = $criterion['criterionMaxValue'];
  }
  $criteriaNames = "[".implode('| ', $criteriaNames)."]";
  $criteriaMinValues = "[".implode(', ', $criteriaMinValues)."]";
  $criteriaMaxValues = "[".implode(', ', $criteriaMaxValues)."]";
  $competitionCreatorId = $_SESSION['auth_user_id'];
  $competitionName = $data['competitionName'];
  $competitionOrganizer = $data['competitionOrganizer'];
  $competitionDescription = $data['competitionDescription'];
  $competitionBegining = date_format(date_create($data['competitionBegining']), "Y-m-d h:i:s");
  $competitionEnding = date_format(date_create($data['competitionEnding']), "Y-m-d h:i:s");
  $competitionAgeRange = $data['competition_age_range'];
  $competitionParticipantsSex = $data['competition_participants_sex'];
  $competitionCertificates = $data['competition_certificates'];
  $competitionPrizes = $data['competition_prizes'];
  $competitionCountries = "[".$data['competition_countries']."]";
  $competitionTags = "[".$data['competition_tags']."]";
  $competitionJudges = json_decode($data['competitionJudges']);
  $competitionJudgesList = [];

  $uploadImage = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['competitionImage']['name']));
	$uploadDirectory = '../uploads_images/';
	$newFileName = 'competition-image-'.rand(10, 10000).'.'.$uploadImage;
	$uploadFile = $uploadDirectory.$newFileName;

  if(@move_uploaded_file($_FILES['competitionImage']['tmp_name'], $uploadFile)){
		mysqli_query($link, "INSERT INTO competitions (creator_id, competition_name, competition_image, competition_begining, competition_ending, competition_description, competition_organizer, competition_age_range, competition_sex, competition_proportion_places, competition_prizes, competition_countries, competition_tags)
                         VALUES ('".$competitionCreatorId."', '".$competitionName."', '".$newFileName."', '".$competitionBegining."',
                                 '".$competitionEnding."', '".$competitionDescription."', '".$competitionOrganizer."', '".$competitionAgeRange."',
                                 '".$competitionParticipantsSex."', '".$competitionCertificates."', '".$competitionPrizes."', '".$competitionCountries."',
                                  '".$competitionTags."')");
    $result = mysqli_query($link, "SELECT competition_id FROM competitions ORDER BY competition_id DESC LIMIT 1");
    $newCompetitionId = mysqli_fetch_object($result)->competition_id;
    mysqli_query($link, "INSERT INTO assessment_criteria (competition_id, assessment_criteria_names, assessment_criteria_min_value, assessment_criteria_max_value)
                                VALUES ('".$newCompetitionId."', '".$criteriaNames."', '".$criteriaMinValues."', '".$criteriaMaxValues."')");
    foreach($competitionJudges as $judge){
      $password = strtolower($judge);
      $password = strrev($password);
      $password = md5("vu3el28f7nf5jaoxashf".$password."g1u46dma8d1o99claed3zq");
      $login = "Judge".$newCompetitionId.rand(1, 1000).rand(100, 100000);
      $userRoleId = 3;
      mysqli_query($link, "INSERT INTO users (user_login, user_password, user_role_id, judge_password) VALUES ('".$login."', '".$password."', '".$userRoleId."', '".$judge."')");
      $result = mysqli_query($link, "SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1");
      $newJudgeId = mysqli_fetch_object($result)->user_id;
      $competitionJudgesList[] = $newJudgeId;
    }
    $competitionJudgesList = "[".implode(", ", $competitionJudgesList)."]";
    mysqli_query($link, "INSERT INTO judges (competition_id, judges_id) VALUES('".$newCompetitionId."', '".$competitionJudgesList."')");
    $files = []; $errors = false;

    for($i = 0; $i < count($_FILES['files']); $i++){
      $tmpFilePath = $_FILES['files']['tmp_name'][$i];
      if($tmpFilePath != ""){
        $uploadFileName = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['files']['name'][$i]));
        $uploadDirectory = '../uploads_files/';
        $newFileName = $newCompetitionId.'&=&'.$_FILES['files']['name'][$i];
        $uploadFile = $uploadDirectory.$newFileName;
        if(@move_uploaded_file($tmpFilePath, $uploadFile)){
          $files[] = $newFileName;
        }else $errors = true;
      }
    }

    $files = json_encode($files, JSON_UNESCAPED_UNICODE);

    if(!$errors){
      mysqli_query($link, "UPDATE competitions SET competition_files='$files' WHERE competition_id=$newCompetitionId");
      print_r("success");
    }else{
      print_r("Виникла помилка при додаванні змагання! Зверніться до адміністратора сайту для вирішення проблеми.");
    }
  }else{
    print_r("Виникла помилка при додаванні змагання! Зверніться до адміністратора сайту для вирішення проблеми.");
  }
}

if($_POST['actionName'] == "saveCompetition"){
  $data = (array)json_decode($_POST['competitionData']);
  $criteriaNames = $criteriaMinValues = $criteriaMaxValues = [];
  $competitionId = $data['competitionId'];
  foreach($data['competitionCriteria'] as $criterion){
    $criterion = (array)$criterion;
    $criteriaNames[] = $criterion['criterionName'];
    $criteriaMinValues[] = $criterion['criterionMinValue'];
    $criteriaMaxValues[] = $criterion['criterionMaxValue'];
  }
  $criteriaNames = "[".implode('| ', $criteriaNames)."]";
  $criteriaMinValues = "[".implode(', ', $criteriaMinValues)."]";
  $criteriaMaxValues = "[".implode(', ', $criteriaMaxValues)."]";
  $competitionCreatorId = $_SESSION['auth_user_id'];
  $competitionName = $data['competitionName'];
  $competitionOrganizer = $data['competitionOrganizer'];
  $competitionDescription = $data['competitionDescription'];
  $competitionBegining = date_format(date_create($data['competitionBegining']), "Y-m-d h:i:s");
  $competitionEnding = date_format(date_create($data['competitionEnding']), "Y-m-d h:i:s");
  $competitionAgeRange = $data['competition_age_range'];
  $competitionParticipantsSex = $data['competition_participants_sex'];
  $competitionCertificates = $data['competition_certificates'];
  $competitionPrizes = $data['competition_prizes'];
  $competitionCountries = "[".$data['competition_countries']."]";
  $competitionTags = "[".$data['competition_tags']."]";
  $competitionExsistedLogins = json_decode($data['ex_logins']);
  $exsistedFileNames = json_decode($data['exsisted_files']);
  $competitionJudges = json_decode($data['competitionJudges']);
  $competitionJudgesList = [];

  $uploadImage = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['competitionImage']['name']));
	$uploadDirectory = '../uploads_images/';
	$newFileName = 'competition-image-'.rand(10, 10000).'.'.$uploadImage;
	$uploadFile = $uploadDirectory.$newFileName;

  if($uploadImage){
    if(@move_uploaded_file($_FILES['competitionImage']['tmp_name'], $uploadFile)){
      mysqli_query($link, "UPDATE competitions SET creator_id=$competitionCreatorId, competition_name='$competitionName', competition_image='$newFileName', competition_begining='$competitionBegining', competition_ending='$competitionEnding', competition_description='$competitionDescription', competition_organizer='$competitionOrganizer', competition_age_range='$competitionAgeRange', competition_sex='$competitionParticipantsSex', competition_proportion_places='$competitionCertificates',
                                  competition_prizes='$competitionPrizes', competition_countries='$competitionCountries', competition_tags='$competitionTags' WHERE competition_id=$competitionId");
      mysqli_query($link, "UPDATE assessment_criteria SET assessment_criteria_names='$criteriaNames', assessment_criteria_min_value='$criteriaMinValues', assessment_criteria_max_value='$criteriaMaxValues' WHERE competition_id=$competitionId");
      foreach($competitionJudges as $judge){
        $password = strtolower($judge);
        $password = strrev($password);
        $password = md5("vu3el28f7nf5jaoxashf".$password."g1u46dma8d1o99claed3zq");
        $login = "Judge".$competitionId.rand(1, 1000).rand(100, 100000);
        $userRoleId = 3;
        mysqli_query($link, "INSERT INTO users (user_login, user_password, user_role_id, judge_password) VALUES ('".$login."', '".$password."', '".$userRoleId."', '".$judge."')");
        $result = mysqli_query($link, "SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1");
        $newJudgeId = mysqli_fetch_object($result)->user_id;
        $competitionJudgesList[] = $newJudgeId;
      }
      $competitionJudgesList = array_merge($competitionExsistedLogins, $competitionJudgesList);
      $competitionJudgesList = "[".implode(", ", $competitionJudgesList)."]";
      mysqli_query($link, "UPDATE judges SET judges_id='$competitionJudgesList' WHERE competition_id=$competitionId");
      $files = []; $errors = false;

      for($i = 0; $i < count($_FILES['files']); $i++){
        $tmpFilePath = $_FILES['files']['tmp_name'][$i];
        if($tmpFilePath != ""){
          $uploadFileName = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['files']['name'][$i]));
          $uploadDirectory = '../uploads_files/';
          $newFileName = $competitionId.'&=&'.$_FILES['files']['name'][$i];
          $uploadFile = $uploadDirectory.$newFileName;
          if(@move_uploaded_file($tmpFilePath, $uploadFile)){
            $files[] = $newFileName;
          }else $errors = true;
        }
      }

      $files = array_merge($files, (array)json_decode($exsistedFileNames));
      $files = json_encode($files, JSON_UNESCAPED_UNICODE);

      if(!$errors){
        mysqli_query($link, "UPDATE competitions SET competition_files='$files' WHERE competition_id=$competitionId");
        print_r("success");
      }else{
        print_r("Виникла помилка при додаванні змагання! Зверніться до адміністратора сайту для вирішення проблеми.");
      }
    }else{
      print_r("Виникла помилка при додаванні змагання! Зверніться до адміністратора сайту для вирішення проблеми.");
    }
  }else{
    mysqli_query($link, "UPDATE competitions SET creator_id=$competitionCreatorId, competition_name='$competitionName', competition_begining='$competitionBegining', competition_ending='$competitionEnding', competition_description='$competitionDescription', competition_organizer='$competitionOrganizer', competition_age_range='$competitionAgeRange', competition_sex='$competitionParticipantsSex', competition_proportion_places='$competitionCertificates',
                                competition_prizes='$competitionPrizes', competition_countries='$competitionCountries', competition_tags='$competitionTags' WHERE competition_id=$competitionId");
    mysqli_query($link, "UPDATE assessment_criteria SET assessment_criteria_names='$criteriaNames', assessment_criteria_min_value='$criteriaMinValues', assessment_criteria_max_value='$criteriaMaxValues' WHERE competition_id=$competitionId");
    mysqli_query($link, "DELETE FROM evaluation_table WHERE competition_id=$competitionId");
    foreach($competitionJudges as $judge){
      $password = strtolower($judge);
      $password = strrev($password);
      $password = md5("vu3el28f7nf5jaoxashf".$password."g1u46dma8d1o99claed3zq");
      $login = "Judge".$competitionId.rand(1, 1000).rand(100, 100000);
      $userRoleId = 3;
      mysqli_query($link, "INSERT INTO users (user_login, user_password, user_role_id, judge_password) VALUES ('".$login."', '".$password."', '".$userRoleId."', '".$judge."')");
      $result = mysqli_query($link, "SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1");
      $newJudgeId = mysqli_fetch_object($result)->user_id;
      $competitionJudgesList[] = $newJudgeId;
    }
    $competitionJudgesList = array_merge($competitionExsistedLogins, $competitionJudgesList);
    $competitionJudgesList = "[".implode(", ", $competitionJudgesList)."]";
    mysqli_query($link, "UPDATE judges SET judges_id='$competitionJudgesList' WHERE competition_id=$competitionId");
    $files = []; $errors = false;

    for($i = 0; $i < count($_FILES['files']); $i++){
      $tmpFilePath = $_FILES['files']['tmp_name'][$i];
      if($tmpFilePath != ""){
        $uploadFileName = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['files']['name'][$i]));
        $uploadDirectory = '../uploads_files/';
        $newFileName = $competitionId.'&=&'.$_FILES['files']['name'][$i];
        $uploadFile = $uploadDirectory.$newFileName;
        if(@move_uploaded_file($tmpFilePath, $uploadFile)){
          $files[] = $newFileName;
        }else $errors = true;
      }
    }

    $files = array_merge($files, $exsistedFileNames);
    $files = json_encode($files, JSON_UNESCAPED_UNICODE);

    if(!$errors){
      mysqli_query($link, "UPDATE competitions SET competition_files='$files' WHERE competition_id=$competitionId");
      print_r("success");
    }else{
      print_r("Виникла помилка при додаванні змагання! Зверніться до адміністратора сайту для вирішення проблеми.");
    }
  }
}

if($_POST['action'] == 'registerCompetition'){
  $competitionId = intval($_POST['competitionId']);
  $userId = intval($_SESSION['auth_user_id']);
  $competitionUserId = "User".$competitionId.rand(100000, 1000000);
  $check = checkOutRegistration($link, $competitionId, $userId);
  $correctCUID = checkCompetitionUserId($link, $competitionUserId);
  if($check && !$correctCUID){
    mysqli_query($link, "INSERT INTO participants (user_id, competition_id, competition_user_id) VALUES ('".$userId."', '".$competitionId."', '".$competitionUserId."')");
    print_r("success");
  }
}

if($_POST['action'] == 'updateEvaluationTable'){
  $userId = $_POST['userId'];
  $criterionId = intval($_POST['criterionId']) - 1;
  $competitionId = $_POST['competitionId'];
  $judgeId = $_SESSION['auth_user_id'];
  $criteriaNumber = $_POST['criteriaNumber'];
  $value = doubleval($_POST['value']);
  $value = doubleval(number_format($value, 5, '.', ''));
  $summary = 0;
  $result = mysqli_query($link, "SELECT * FROM evaluation_table WHERE competition_id=$competitionId AND judge_id=$judgeId AND user_id=$userId");
  if(mysqli_num_rows($result) > 0){
    $result = mysqli_query($link, "SELECT evaluation_values FROM evaluation_table WHERE competition_id=$competitionId AND judge_id=$judgeId AND user_id=$userId");
    $values = mysqli_fetch_assoc($result)['evaluation_values'];
    $values = json_decode($values);
    $values[$criterionId] = $value;
    foreach ($values as $value) {
      $summary += $value;
    }
    $summary = number_format($summary, 5, '.', '');
    $values = json_encode($values);
    mysqli_query($link, "UPDATE evaluation_table SET evaluation_values='$values', summary=$summary WHERE competition_id=$competitionId AND judge_id=$judgeId AND user_id=$userId");
  }else{
    $values = array_fill(0, $criteriaNumber, 0);
    $values[$criterionId] = $value;
    foreach ($values as $value) {
      $summary += $value;
    }
    $summary = number_format($summary, 5, '.', '');
    $values = json_encode($values);
    mysqli_query($link, "INSERT INTO evaluation_table(competition_id, judge_id, user_id, evaluation_values, summary) VALUES ('".$competitionId."', '".$judgeId."', '".$userId."', '".$values."', '".$summary."')");
  }
  print_r("success");
}

if($_POST['action'] == "refuseCompetition"){
  $competitionId = $_POST['competitionId'];
  $userId = $_SESSION['auth_user_id'];
  $result = mysqli_query($link, "DELETE FROM participants WHERE user_id=$userId AND competition_id=$competitionId");
  print_r("success");
}

if($_POST['action'] == "saveCommentary"){
  $userId = $_POST['userId'];
  $competitionId = $_POST['competitionId'];
  $judgeId = $_SESSION['auth_user_id'];
  $criterionId = $_POST['criterionId'] - 1;
  $message = $_POST['message'];
  $criteriaNumber = count(getAllCompetitionCriteria($link, $competitionId));
  $result = mysqli_query($link, "SELECT * FROM evaluation_table WHERE competition_id=$competitionId AND judge_id=$judgeId AND user_id=$userId");
  $json = mysqli_fetch_object($result)->evaluation_commentaries;
  $json = str_replace("\n", "\\n", $json);
  $message = str_replace("'", "&apos;", $message);
  $message = str_replace('"', "&quot;", $message);
  $evaluationCommentaries = (array)json_decode($json);
  if(count($evaluationCommentaries) == 0){
    for($i = 0; $i < $criteriaNumber; $i++){
      if($i == $criterionId) $evaluationCommentaries[$i] = $message;
      else $evaluationCommentaries[$i] = "";
    }
  }else{
    for($i = 0; $i < count($evaluationCommentaries); $i++){
      if($i == $criterionId) $evaluationCommentaries[$i] = $message;
    }
  }
  $evaluationCommentaries = json_encode($evaluationCommentaries, JSON_UNESCAPED_UNICODE);
  mysqli_query($link, "UPDATE evaluation_table SET evaluation_commentaries='$evaluationCommentaries' WHERE user_id=$userId AND competition_id=$competitionId AND judge_id=$judgeId");
  print_r("success");
}

if($_POST['action'] == 'removeCommentary'){
  $userId = $_POST['userId'];
  $competitionId = $_POST['competitionId'];
  $judgeId = $_SESSION['auth_user_id'];
  $criterionId = $_POST['criterionId'] - 1;
  $result = mysqli_query($link, "SELECT * FROM evaluation_table WHERE competition_id=$competitionId AND judge_id=$judgeId AND user_id=$userId");
  $json = mysqli_fetch_object($result)->evaluation_commentaries;
  $json = str_replace("\n", "\\n", $json);
  $evaluationCommentaries = (array)json_decode($json);
  for($i = 0; $i < count($evaluationCommentaries); $i++){
    if($i == $criterionId) $evaluationCommentaries[$i] = "";
  }
  $evaluationCommentaries = json_encode($evaluationCommentaries, JSON_UNESCAPED_UNICODE);
  mysqli_query($link, "UPDATE evaluation_table SET evaluation_commentaries='$evaluationCommentaries' WHERE user_id=$userId AND competition_id=$competitionId AND judge_id=$judgeId");
  print_r("success");
}
