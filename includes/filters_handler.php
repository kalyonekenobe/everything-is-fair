<?php
include("db_connect.php");
include("functions.php");

$data = (array)json_decode($_POST['data']);
$query = [];

if($data['status'] != "") $query[] = "status=".$data['status'];
$minAge = $data['min_age'];
$maxAge = $data['max_age'];
$query[] = "age=".$minAge."-".$maxAge;
if($data['begining'] != "") $query[] = "from=".$data['begining'];
if($data['ending'] != "") $query[] = "to=".$data['ending'];
if($data['participant_sex'] != "") $query[] = "participant_gender=".$data['participant_sex'];
if($data['countries'] != "" && $data['countries'] != "all") $query[] = "countries=".$data['countries'];
if($data['tags'] != "") $query[] = "tags=".$data['tags'];
if($data['organizers'] != "") $query[] = "organizers=".$data['organizers'];
$minRating = $data['min_rating'];
$maxRating = $data['max_rating'];
$query[] = "rating=".$minRating."-".$maxRating;

$query = implode("&", $query);
print_r($query);
