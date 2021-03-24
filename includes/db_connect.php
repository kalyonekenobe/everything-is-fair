<?php
  $DATABASE = 'project_db';
  $HOST = '127.0.0.1';
  $USER = 'root';
  $PASSWORD = '';

  $link = mysqli_connect($HOST, $USER, $PASSWORD);

  if(!$link) die("Виникла помилка при підключенні до бази диних");

  $DATABASE_SELECT = mysqli_select_db($link, $DATABASE);

  if(!$DATABASE_SELECT) die("Виникла помилка при підключенні до бази диних".mysqli_error($link));

  mysqli_query($link, "SET NAMES utf-8");
