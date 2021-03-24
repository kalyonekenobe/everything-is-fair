<?php
  include("includes/header.php");
  $userData = getPersonalUserData($link);
  $countries = getAllCountries($link);
  $yearsFolders = getYearsLogsFolders();
  if($_GET['year']) $monthsFolders = getMonthsLogsFolders($_GET['year']);
  if($_GET['month'] && $_GET['year']) $daysFolders = getDaysLogsFolders($_GET['year'], $_GET['month']);
  if($_GET['day'] && $_GET['month'] && $_GET['year']) $events = getAllEvents($_GET['year'], $_GET['month'], $_GET['day']);
  $userProfileFilled = $_SESSION['auth_user_profile_filled'];
  $messages = getMessagesByUserIdOrIp($link, $_SESSION['auth_user_id'], $_SERVER['REMOTE_ADDR']);
  $reviews = getReviewsByUserIp($link, $_SERVER['REMOTE_ADDR']);
  $blacklist = getFullBlackList($link);
?>
<section>
  <div class="personal_office">
    <div class="personal_office_row">
      <div class="personal_office_container">
        <div class="personal_office_sidebar_container">
          <div class="personal_office_sidebar">
            <nav class="personal_office_navbar selector_nav">
              <div class="main_buttons">
                <a container-name="personal_information_container" class="selected"><img src="images/profile.png" title="Профіль"><span>Профіль</span></a>
                <?php if($userData['role_id'] == 1): ?>
                  <a container-name="personal_logs_container"><img src="images/logs.png" title="Профіль"><span>Події</span></a>
                  <a container-name="personal_dark_list_container"><img src="images/dark_list.png" title="Чорний список"><span>Чорний список</span></a>
                <?php endif; ?>
                <a container-name="personal_competitions_container"><img src="images/competitions.png" title="Змагання"><span>Змагання</span></a>
                <?php if($userData['role_id'] == 3): ?>
                  <a container-name="judges_work"><img src="images/law.png" title="Суддівська робота"><span>Суддівська робота</span></a>
                <?php endif; ?>
                <a container-name="personal_messages_container"><img src="images/messages.png" title="Повідомлення"><span>Повідомлення</span></a>
                <a container-name="personal_reviews_container"><img src="images/reviews.png" title="Відгуки"><span>Відгуки</span></a>
                <a container-name=""><img src="images/friends.png" title="Друзі"><span>Друзі</span></a>
                <a container-name=""><img src="images/blog.png" title="Блог"><span>Блог</span></a>
              </div>
              <div class="footer_buttons"><a class="logout"><img src="images/logout.png" title="Вихід"><span>Вихід</span></a></div>
              <span class="hide">❮</span>
            </nav>
          </div>
        </div>
        <div class="personal_office_information_container">
          <div class="personal_information_container per-off-con">
            <h2>Особистий кабінет / Профіль</h2>
            <span class="profile_filled_info <?php if($userProfileFilled): ?>pf_success<?php else: ?>pf_error<?php endif; ?>"><?php if($userProfileFilled): ?>Інформація про користувача наявна у повному обсязі. Доступ до участі у змаганнях відкрито.<?php else: ?>Ви не надали інформацію про себе у повному обсязі або деякі дані некоректні. Заповніть профіль повністю, щоб мати можливість брати участь у змаганнях.<?php endif; ?></span>
            <div class="po-container">
              <div class="po-row">
                <div class="po-cell profile_photo_container">
                  <label>Фото профілю</label>
                  <div class="profile_photo">
                    <?php if($userData['user_image'] == ""): ?>
                      <img src="/images/man.png">
                    <?php else: ?>
                      <img src="/uploads_images/<?= $userData['user_image']; ?>">
                    <?php endif; ?>
                  </div>
                  <div class="upload_container">
                    <input id="upload_profile_image" type="file" name="image" accept="image/gif, image/jpeg, image/png" onchange="readURL(this);" multiple>
                    <label for="upload_profile_image">
                      <img <?php if($userData['image'] == ""): ?> src="/images/upload-image.png" <?php else: ?> src="uploads_images/<?= $userData['image']; ?>" <?php endif; ?>>
                        Завантажити зображення
                    </label>
                  </div>
                </div>
                <div class="po-cell profile_information_container">
                  <div class="profile_item col-12 col-xl-4">
                    <label>Логін</label>
                    <h3><?= $userData['login']; ?></h3>
                  </div>
                  <div class="profile_item col-12 col-xl-4">
                    <label>Роль користувача</label>
                    <h3><?= $userData['role_name']; ?></h3>
                  </div>
                  <div class="profile_item col-12 col-xl-4">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $userData['email']; ?>" required>
                  </div>
                  <?php if($userData['role_id'] != 2): ?>
                    <div class="profile_item col-12 col-xl-4">
                      <label>Прізвище</label>
                      <input type="text" name="last_name" value="<?= $userData['last_name']; ?>" required>
                    </div>
                    <div class="profile_item col-12 col-xl-4">
                      <label>Ім'я</label>
                      <input type="text" name="first_name" value="<?= $userData['first_name']; ?>" required>
                    </div>
                    <div class="profile_item col-12 col-xl-4">
                      <label>По-батькові</label>
                      <input type="text" name="middle_name" value="<?= $userData['middle_name']; ?>" required>
                    </div>
                  <?php else: ?>
                    <div class="profile_item col-12">
                      <label>Назва організації</label>
                      <input type="text" name="organization_name" value="<?= $userData['user_organization_name']; ?>">
                    </div>
                  <?php endif; ?>
                  <div class="profile_item col-12 col-xl-4 po-phone">
                    <label>Мобільний телефон</label>
                    <input type="tel" id="profile_phone" name="phone" value="<?= $userData['phone']; ?>" required>
                  </div>
                  <?php if($userData['role_id'] != 2): ?>
                    <div class="profile_item col-12 col-xl-4">
                      <label>Стать</label>
                      <?php if($userData['sex'] == ""): ?>
                        <span class="selector" name="sex" selected_value=""><label>Не визначено</label></span>
                      <?php else: ?>
                        <span class="selector" name="sex" selected_value="<?= $userData['sex']; ?>"><label><?= $userData['sex']; ?></label></span>
                      <?php endif; ?>
                      <div class="selector-options yscroller">
                        <div class="option" value="Чоловіча">Чоловіча</div>
                        <div class="option" value="Жіноча">Жіноча</div>
                      </div>
                    </div>
                    <div class="profile_item col-12 col-xl-4">
                      <label>Дата народження</label>
                      <input type="date" name="birth_date" value="<?= $userData['birth_date']; ?>">
                    </div>
                  <?php endif; ?>
                  <div class="profile_item col-12 col-xl-4">
                    <label>Країна</label>
                    <?php if($userData['country'] == ""): ?>
                      <span class="selector" name="country" selected_value=""><label>Не визначено</label></span>
                    <?php else: ?>
                      <span class="selector" name="country" selected_value="<?= $userData['country']; ?>"><label><?= getCountryByCountryCode($link, $userData['country'])->title_ua; ?></label></span>
                    <?php endif; ?>
                    <div class="selector-options yscroller">
                      <?php foreach($countries as $country): ?>
                        <div class="option" value="<?= $country['Code2']; ?>">
                          <img src="images/countries/<?= strtolower($country['Code2']); ?>.png">
                          <h4><?= $country['title_ua']; ?></h4>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <div class="profile_item col-12 col-xl-4">
                    <label>Місто</label>
                    <input type="text" name="city" value="<?= $userData['city']; ?>">
                  </div>
                  <?php if($userData['role_id'] != 2): ?>
                    <div class="profile_item col-12 col-xl-4">
                      <label>Адреса</label>
                      <input type="text" name="address" value="<?= $userData['address']; ?>">
                    </div>
                  <?php endif; ?>
                </div>
                <div class="footer col-12">
                  <a class="button save_profile">Зберегти зміни</a>
                </div>
              </div>
            </div>
          </div>
          <div class="personal_logs_container per-off-con">
            <?php if($_GET['year'] == ""): ?>
              <h2>Особистий кабінет / Події сайту</h2>
              <div class="logs_list">
                <div class="page_header">
                  <div>
                    <h3>Оберіть рік</h3>
                  </div>
                </div>
                <div class="page_content">
                  <?php foreach($yearsFolders as $folder): ?>
                    <a href="personal_office.php?year=<?= $folder; ?>"><img src="images/folder.png"><?= $folder; ?></a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php elseif($_GET['month'] == ""): ?>
              <h2>Особистий кабінет / Події сайту / <?= $_GET['year']; ?></h2>
              <div class="logs_list">
                <div class="page_header">
                  <div>
                    <h3>Оберіть місяць</h3>
                  </div>
                  <div class="buttons_container">
                    <a class="button" href="personal_office.php">Назад</a>
                  </div>
                </div>
                <div class="page_content">
                  <?php foreach($monthsFolders as $folder): ?>
                    <a href="<?= $_SERVER['REQUEST_URI']; ?>&month=<?= $folder; ?>"><img src="images/folder.png"><?= getMonthNameByNum($folder); ?></a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php elseif($_GET['day'] == ""): ?>
              <h2>Особистий кабінет / Події сайту / <?= $_GET['year']; ?> / <?= getMonthNameByNum($_GET['month']); ?></h2>
              <div class="logs_list">
                <div class="page_header">
                  <div>
                    <h3>Список подій</h3>
                  </div>
                  <div class="buttons_container">
                    <a class="button" href="personal_office.php?year=<?= $_GET['year']; ?>">Назад</a>
                  </div>
                </div>
                <div class="page_content">
                  <?php foreach($daysFolders as $folder): ?>
                    <a href="<?= $_SERVER['REQUEST_URI']; ?>&day=<?= $folder; ?>"><img src="images/folder.png"><?= $folder."/".$_GET['month']."/".$_GET['year']; ?></a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php else: ?>
              <h2>Особистий кабінет / Події сайту / <?= $_GET['year']; ?> / <?= getMonthNameByNum($_GET['month']); ?> / <?= $_GET['day']; ?></h2>
              <div class="logs_list">
                <div class="page_header">
                  <div>
                    <h3>Список подій</h3>
                  </div>
                  <div class="buttons_container">
                    <a class="button" href="personal_office.php?year=<?= $_GET['year']; ?>&month=<?= $_GET['month']; ?>">Назад</a>
                  </div>
                </div>
                <div class="page_content yscroller">
                  <?php foreach(array_reverse($events) as $event): ?>
                    <div class="log">
                      <span class="<?php if($event['actionResult'] == true): ?>log-success<?php else: ?>log-error<?php endif; ?>"><?= $event['action']; ?></span>
                      <span><?= changeDateFormat($event['date']->date, "d/m/Y H:i:s"); ?></span>
                      <div class="additional_data">
                        <h4>Додаткова інформація:</h4>
                        <?php if($event['user_id']): ?>
                          <div><label>ID користувача:</label> <?= $event['user_id']; ?></div>
                        <?php endif; ?>
                        <?php if($event['competition_id']): ?>
                          <div><label>ID змагання:</label> <?= $event['competition_id']; ?></div>
                        <?php endif; ?>
                        <?php if($event['review_text']): ?>
                          <div><label>Текст відгуку:</label> <?= $event['review_text']; ?></div>
                        <?php endif; ?>
                        <div><label>IP користувача:</label> <?= $event['user_ip']; ?></div>
                        <div><label>Назва події:</label> <?= $event['action']; ?></div>
                        <div><label>Статус події:</label> <?php if($event['actionResult'] == true): ?>Успішно<?php else: ?>Помилка<?php endif; ?></div>
                        <div><label>Дата та час:</label> <?= changeDateFormat($event['date']->date, "d/m/Y H:i:s"); ?> <?= $event['date']->timezone; ?></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <div class="personal_competitions_container per-off-con">
            <h2>Особистий кабінет / Змагання</h2>
            <div class="po-container">
              <div class="competitions_container_navbar">
                <nav>
                  <a container-name="tp_competitions" class="selected">Беру участь</a>
                  <a container-name="cr_competitions">Створені мною</a>
                </nav>
              </div>
              <div class="tp_competitions">
                <?php if($userData['competitions']['take_part']): ?>
                  <?php foreach($userData['competitions']['take_part'] as $competition): ?>
                    <div class="competition_item">
                      <h3>
                        <?= $competition['competition_name']; ?>
                        <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 3): ?><span class="yellow">Триває</span><?php endif; ?>
                        <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 1): ?><span class="green">Триває реєстрація</span><?php endif; ?>
                        <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 2): ?><span class="gray">Завершено</span><?php endif; ?>
                      </h3>
                      <p><span>Організатор: </span><?= $competition['competition_organizer']; ?></p>
                      <p>
                        <span>Дата проведення: </span>
                        <?= date_format(date_create($competition['competition_begining']), "d/m/Y h:i"); ?> - <?= date_format(date_create($competition['competition_ending']), "d/m/Y h:i"); ?>
                      </p>
                      <div class="competition_item_links">
                        <a class="button" href="view_competition.php?competition-id=<?= $competition['competition_id']; ?>">Детальніше</a>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="empty-search">Ви ще не взяли участь в жодному змаганні.</p>
                <?php endif; ?>
              </div>
              <div class="cr_competitions">
                <?php if($userData['privileges']['add_competitions']): ?>
                  <div class="create_competition_link">
                    <a href="competition.php?action=create-competition" class="button"><span>+</span>Створити змагання</a>
                  </div>
                  <?php if($userData['competitions']['created']): ?>
                    <?php foreach($userData['competitions']['created'] as $competition): ?>
                      <div class="competition_item">
                        <h3>
                          <?= $competition['competition_name']; ?>
                          <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 3): ?><span class="yellow">Триває</span><?php endif; ?>
                          <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 1): ?><span class="green">Триває реєстрація</span><?php endif; ?>
                          <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 2): ?><span class="gray">Завершено</span><?php endif; ?>
                        </h3>
                        <p><span>Організатор: </span><?= $competition['competition_organizer']; ?></p>
                        <p>
                          <span>Дата проведення: </span>
                          <?= date_format(date_create($competition['competition_begining']), "d/m/Y h:i"); ?> - <?= date_format(date_create($competition['competition_ending']), "d/m/Y h:i"); ?>
                        </p>
                        <div class="competition_item_links">
                          <a competition-id="<?= $competition['competition_id']; ?>" action='edit' class="button" href="competition.php?competition-id=<?= $competition['competition_id']; ?>&action=edit-competition">Редагувати</a>
                          <a competition-id="<?= $competition['competition_id']; ?>" action='remove' class="rbutton">Видалити</a>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="empty-search">Ви ще не створили жодного змагання.</p>
                  <?php endif; ?>
                <?php else: ?>
                  <p class="empty-search">У Вас немає прав на створення змагань</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="judges_work per-off-con">
            <h2>Особистий кабінет / Суддівська робота</h2>
            <div class="judges_competitions po-container">
              <?php if(checkUserAccountOccupancy($userData)): ?>
                <h4>Список змагань, які Ви оцінюєте: </h4>
                <?php if($userData['competitions']['judge']): ?>
                  <?php foreach($userData['competitions']['judge'] as $competition): ?>
                    <div class="competition_item">
                      <h3><?= $competition['competition_name']; ?></h3>
                      <p><span>Організатор: </span><?= $competition['competition_organizer']; ?></p>
                      <p>
                        <span>Дата проведення: </span>
                        <?= date_format(date_create($competition['competition_begining']), "d/m/Y h:i"); ?> - <?= date_format(date_create($competition['competition_ending']), "d/m/Y h:i"); ?>
                      </p>
                      <div class="competition_item_links">
                        <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 3): ?>
                          <a class="button open_participants_table" competition-id='<?= $competition['competition_id']; ?>'>Відкрити таблицю учасників</a>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="empty-search">Ви ще не судите жодне змагання.</p>
                <?php endif; ?>
              <?php else: ?>
                  <p class="empty-search">Для подальшого оцінювання учасників заповніть інофрмацію про себе.</p>
              <?php endif; ?>
            </div>
          </div>
          <div class="personal_messages_container per-off-con">
            <h2>Особистий кабінет / Повідомлення</h2>
            <div class="user_messages po-container">
              <div class="page_header">
                <a container-name="income" class="active">Вхідні</a>
                <a container-name="outcome">Надіслані</a>
              </div>
              <div class="income">
                <?php foreach($messages['income'] as $message): ?>
                  <div class="user_message">
                    <div class="details">
                      <div class="status">
                        <?php if($message['status'] == true): ?>
                          <span class="viewed">Прочитане</span>
                        <?php else: ?>
                          <span class="not_viewed">Непрочитане</span>
                        <?php endif; ?>
                      </div>
                      <div class="content">
                        <p><?= $message['message_text']; ?></p>
                      </div>
                    </div>
                    <div class="datetime">
                      <?= $message['message_datetime']; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="outcome">
                <?php foreach($messages['outcome'] as $message): ?>
                  <div class="user_message">
                    <div class="details">
                      <div class="status">
                        <?php if($message['status'] == true): ?>
                          <span class="viewed">Прочитане</span>
                        <?php else: ?>
                          <span class="not_viewed">Непрочитане</span>
                        <?php endif; ?>
                      </div>
                      <div class="content">
                        <p><?= $message['message_text']; ?></p>
                      </div>
                    </div>
                    <div class="datetime">
                      <?= $message['message_datetime']; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="personal_reviews_container per-off-con">
            <h2>Особистий кабінет / Відгуки</h2>
            <div class="user_reviews po-container">
              <?php foreach($reviews as $review): ?>
                <div class="user_message">
                  <div class="details">
                    <div class="content">
                      <p><?= $review['review_text']; ?></p>
                    </div>
                  </div>
                  <div class="datetime">
                    <?= $review['review_datetime']; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="personal_dark_list_container per-off-con">
            <h2>Особистий кабінет / Чорний список</h2>
            <div class="user_dark_list po-container">
              <div class="dark_list_form">
                <div>
                  <label>IP користувача: </label>
                  <input type="text" name="user_ip">
                </div>
                <div>
                  <label>Дата та час завершення блокування: </label>
                  <input type="datetime-local" name="ban_datetime">
                </div>
                <div>
                  <a class="button ban_user">Заблокувати</a>
                </div>
              </div>
              <div class="dark_list">
                <?php foreach($blacklist as $item): ?>
                  <div class="dark_list_item">
                    <div class="user_ip">
                      <p>IP користувача: <span><?= $item['user_ip']; ?></span></p>
                    </div>
                    <div class="datetime">
                      <p>Кінець блокування: <span><?= $item['ban_time']; ?></span></p>
                    </div>
                    <?php
                      if(checkUserBanned($link, $item['user_ip']) == true){
                        $banClass = "banned";
                        $banMessage = "Заблоковано";
                      }else{
                        $banClass = "not_banned";
                        $banMessage = "Термін блокування закінчився";
                      }
                    ?>
                    <div class="ban_status">
                      <p>Стан блокування: <span class="<?= $banClass; ?>"><?= $banMessage; ?></span></p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  let country = window.intlTelInput(document.getElementById("profile_phone"), {
    utilsScript: "phone-input-plagin/build/js/utils.js",
  });
  document.getElementById("profile_phone").addEventListener("countrychange", function(){
    this.value = "";
    let countryData = country.getSelectedCountryData();
    let countryCode = countryData.dialCode;
    if(countryData.iso2 == "ua") countryCode = countryCode.replace("0", "");
    document.getElementById("profile_phone").setAttribute("dial-code", countryCode);
  });
  let countryData = country.getSelectedCountryData();
  let countryCode = countryData.dialCode;
  if(countryData.iso2 == "ua") countryCode = countryCode.replace("0", "");
  document.getElementById("profile_phone").setAttribute("dial-code", countryCode);
</script>
<?php
  include("includes/footer.php");
?>
