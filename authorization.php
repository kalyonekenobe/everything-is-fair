<?php
  include("includes/header.php");
?>
<section>
  <div class="container">
    <div class="row authorization_container">
      <div class="col-12 col-sm-10 col-md-8 col-lg-7 authorization_form_container">
        <form class="authorization_form" method="post">
          <div class="authorization_links_container">
            <a class="authorization_links active sign_in">Авторизація</a>
            <a class="authorization_links sign_up">Реєстрація</a>
          </div>
          <ul class="registration_list">
            <li>
              <label for="reg_login">Логін <span>*</span></label>
              <input id="reg_login" type="text" name="reg_login" autocomplete="off" pattern="^[A-Za-z0-9]+$" title="Логін має містити тільки латинські літери та цифри!">
            </li>
            <li>
              <label for="reg_password">Пароль <span>*</span></label>
              <input id="reg_password" type="password" name="reg_password" autocomplete="off" pattern="^[A-Za-z0-9]+$" title="Пароль має містити тільки латинські літери та цифри!">
            </li>
            <li>
              <label for="reg_email">Електронна пошта <span>*</span></label>
              <input id="reg_email" type="email" name="reg_email" autocomplete="off">
            </li>
            <li>
              <label>Роль користувача <span>*</span></label>
              <span class="selector" selected_value="4">Учасник</span>
              <div class="selector-options yscroller">
                <div class="option" value="4">Учасник</div>
                <div class="option" value="2">Організатор змагань</div>
              </div>
            </li>
            <li>
              <input class="registration_submit button" type="submit" name="" value="Зареєструватись">
            </li>
          </ul>
          <ul class="authorization_list">
            <li>
              <label for="auth_login">Логін </label>
              <input id="auth_login" type="text" name="auth_login" autocomplete="off" pattern="^[A-Za-z0-9]+$" title="Логін має містити тільки латинські літери та цифри!">
            </li>
            <li>
              <label for="auth_password">Пароль </label>
              <input id="auth_password" type="password" name="auth_password" autocomplete="off" pattern="^[A-Za-z0-9]+$" title="Пароль має містити тільки латинські літери та цифри!">
            </li>
            <li>
              <div class="authorization_adding">
                <div>
                  <input id="remember_me" type="checkbox" name="">
                  <label for="remember_me">Запам'ятати мене</label>
                </div>
                <a class="forget_password">Забули пароль?</a>
              </div>
            </li>
            <li>
              <input class="authorization_submit button" type="submit" name="" value="Увійти">
            </li>
          </ul>
          <ul class="restoring_list">
            <li>
              <label for="restoring_email">Електронна пошта</label>
              <input id="restoring_email" type="email" name="" value="" autocomplete="off">
            </li>
            <li>
              <input class="restoring_submit button" type="submit" name="" value="Відновити пароль">
            </li>
          </ul>
          <ul class="registration_completed_list">
            <li>
              <img src="images/registration-completed.png" width="64px" height="64px">
            </li>
            <li>
              <h3>Реєстрацію завершено. Дякуємо!</h3>
            </li>
            <li>
              <a class="button" href="authorization.php">Ок</a>
            </li>
          </ul>
        </form>
      </div>
    </div>
  </div>
</section>
