<?php
  session_start();
  include("includes/db_connect.php");
  include("includes/functions.php");
  include("includes/check_authorization_cookies.php");
  $userBanned = checkUserBanned($link, $_SERVER['REMOTE_ADDR']);
  if($userBanned && $_SESSION['auth_user'] == true) include("logout.php");
  $pageName = basename($_SERVER["PHP_SELF"]);
  $userAuthorized = $_SESSION["auth_user"];
  $headerClassName = getHeaderClassName($pageName);
  if($pageName == "assessment_table.php" && $_SESSION['auth_user_role_id'] != 3) header("location: index.php");
  if($pageName == "competition.php" && $_SESSION['auth_user_role_id'] > 2) header('location: index.php');
  if($pageName == "personal_office.php" && $_SESSION['auth_user'] == false) header('location: index.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Проект</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/grid.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="phone-input-plagin/build/css/intlTelInput.css">
    <link rel="stylesheet" href="fontawesome/css/all.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
    <script src="js/maskinput.js"></script>
    <script src="ckeditor/ckeditor.js"></script>
    <script src="phone-input-plagin/build/js/intlTelInput.js"></script>
    <script src="js/script.js"></script>
  </head>
  <body>
    <header class="<?= $headerClassName; ?>">
      <div class="main_navigation <?php if($pageName == "authorization.php"): ?>transparent<?php endif; ?>">
        <div class="burger_menu_icon">
          <img src="images/burger_menu_white.png">
        </div>
        <div class="navigation_links">
          <a href="/">Головна</a>
          <a href="competitions.php">Змагання</a>
          <a href="">Новини</a>
          <a href="about_us.php">Про нас</a>
          <a href="">Допомога</a>
        </div>
        <div class="navigation_right_links">
          <?php if($userAuthorized): ?>
            <a href="personal_office.php">Особистий кабінет</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="mobile_menu">
        <nav>
          <div><img src="images/close.png" class="close-menu"></div>
          <a href="/">Головна</a>
          <a href="competitions.php">Змагання</a>
          <a href="">Новини</a>
          <a href="about_us.php">Про нас</a>
          <a href="">Допомога</a>
          <?php if($userAuthorized): ?>
            <a href="personal_office.php">Особистий кабінет</a>
          <?php endif; ?>
        </nav>
      </div>
      <?php if($headerClassName == "full-header"): ?>
        <?php if(!$userAuthorized): ?>
          <div class="header_view">
            <div class="project_title">
              <div>
                <h2>У нас все чесно!</h2>
                <p><span>У нас все чесно</span> - унікальний інструмент для проведення онлайн-змагань. Створена платформа надає можливість користувачам показувати свої навички та таланти не виходячи з дому. Організатори можуть всього в декілька кроків створити нове змагання, додати членів журі та стати улюбленцями учасників різного віку. Все що для цього потрібно - пройти реєстрацію у формі праворуч та увійти у свій аккаунт. Нові емоції, знайомства, призи та популярність чекають на тебе. Приєднуйся до нас!</p>
              </div>
              <a class="wbutton">Розпочати знайомство</a>
            </div>
            <div class="auth">
              <div class="header">
                <h2 class="active sign_in">Авторизація</h2>
                <h2 class="sign_up">Реєстрація</h2>
              </div>
              <div class="authorization_list">
                <div class="content">
                  <ul>
                    <li>
                      <label>Логін</label>
                      <input id="auth_login" type="text">
                    </li>
                    <li>
                      <label>Пароль</label>
                      <div class="password_container">
                        <input id="auth_password" type="password">
                        <img src="/images/show-password.png" for="auth_password" title="Показати пароль">
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="auth_adding">
                  <div>
                    <input id="remember_me" type="checkbox" name="">
                    <label for="remember_me">Запам'ятати мене</label>
                  </div>
                  <a class="forget_password">Забули пароль?</a>
                </div>
                <div class="footer">
                  <a class="button authorization_submit">Увійти</a>
                </div>
              </div>
              <div class="registration_list">
                <div class="content">
                  <ul>
                    <li>
                      <label>Логін <span>*</span></label>
                      <input id="reg_login" type="text">
                    </li>
                    <li>
                      <label>Пароль <span>*</span></label>
                      <div class="password_container">
                        <input id="reg_password" type="password">
                        <img src="/images/show-password.png" for="reg_password" title="Показати пароль">
                      </div>
                    </li>
                    <li>
                      <label>Електронна пошта <span>*</span></label>
                      <input id="reg_email" type="email">
                    </li>
                    <li>
                      <label>Роль користувача <span>*</span></label>
                      <span id="reg_role" class="selector" selected_value="4"><label>Учасник змагань</label></span>
                      <div class="selector-options yscroller">
                        <div class="option" value="4">Учасник змагань</div>
                        <div class="option" value="2">Організатор змагань</div>
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="footer">
                  <a class="button registration_submit">Зареєструватись</a>
                </div>
              </div>
              <div class="restoring_list">
                <div class="content">
                  <ul>
                    <li>
                      <label for="restoring_email">Електронна пошта <span>*</span></label>
                      <input id="restoring_email" type="email">
                    </li>
                  </ul>
                </div>
                <div class="footer">
                  <a class="button restoring_submit">Відновити пароль</a>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="header_title">
            <label>У нас все чесно</label>
            <p>Онлайн платформа для оцінювання проектів</p>
            <a>Розпочати роботу</a>
          </div>
        <?php endif; ?>
      <?php endif; ?>
      <div class="darkback"></div>
      <div class="site_messages_container"></div>
      <div class="site_notification">
        <img src="images/close.png" width="20px" height="20px">
        <h3 class="title"></h3>
        <div>
          <p class="text"></p>
        </div>
        <div class="interaction_buttons_container">
          <a class="button">Підтвердити</a>
          <a class="tbutton">Закрити</a>
        </div>
      </div>
      <div class="hint">
        <div class="page_header">
          <h3>Коментар</h3>
          <div class="hint_buttons">
            <img src="images/edit.png" action="edit" disable="false" title="Редагувати">
            <img src="images/bin.png" action= "remove" disable="false" title="Видалити">
          </div>
        </div>
        <div class="commentary_text"></div>
      </div>
      <div class="add_review_form">
        <img src="images/close.png" class="close-icon">
        <h2>Додати відгук</h2>
        <div>
          <label>Ім'я <span>*</span></label>
          <input type="text" name='author' value="<?= $_SESSION['auth_user_first_name']; ?>">
        </div>
        <div>
          <label>Текст <span>*</span></label>
          <textarea></textarea>
        </div>
        <a class="button send_review">Надіслати</a>
      </div>
    </header>
