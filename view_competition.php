<?php
  include("includes/header.php");
  $competitionId = $_GET['competition-id'];
  $userId = $_SESSION['auth_user_id'];
  $userIp = str_replace(".", "_", $_SERVER['REMOTE_ADDR']);
  $participantData = [];
  if($userId != ""){
    $competitionUserId = getCompetitionUserId($link, $userId, $competitionId);
    $userNotRegistered = checkOutRegistration($link, $competitionId, $userId);
    $participantRole = checkOutParticipantRole($link, $competitionId, $userId);
    if(!$userNotRegistered) $participantData = getParticipantData($link, $competitionId, $userId);
    if($participantRole == "participant"){
      $competitionJudgesValues = getAllJudgesValues($link, $userId, $competitionId);
    }
  }
  $competition = getCompetitionById($link, $competitionId);
  $competitionCountriesNames = $competitionTagsNames = [];
  foreach ($competition['competition_countries'] as $country) {
    $competitionCountriesNames[] = getCountryByCountryCode($link, $country)->title_ua;
  }
  foreach ($competition['competition_tags'] as $tag) {
    $competitionTagsNames[] = getTagById($link, $tag)->tag_name;
  }
  $competitionTable = getFullCompetitionTable($link, $competitionId);
  $competitionCriteria = getAllCompetitionCriteria($link, $competitionId);
  $competitionParticipants = getAllCompetitionParticipants($link, $competitionId);
  $competitionStatus = checkCompetitionStatus($link, $competitionId);
  if($userId) $competitionAviable = checkCompetititionComparement($link, $userId, $competitionId);
  $scolumns = $columns = $rows = $iterator = 0;
  $competitionStatusName = "";
  if($competitionStatus == 1)
    $competitionStatusName = "Триває реєстрація";
  else if($competitionStatus == 2)
    $competitionStatusName = "Завершено";
  else
    $competitionStatusName = "Триває";
?>
<section>
  <div class="container">
    <div class="row view_competition">
      <div class="col-12 view_competition_container">
        <div class="competition_presentation">
          <div class="page_header">
            <div>
              <div class="container_navbar">
                <nav>
                  <a class="selected" container-name="competition_info">Загальна інформація</a>
                  <a container-name="competition_evaluation_table">Таблиця учасників</a>
                  <a container-name="competition_news">Оголошення</a>
                  <?php if($userId != "" && !$userNotRegistered): ?>
                    <a container-name="competition_work">Моя робота</a>
                  <?php endif; ?>
                </nav>
              </div>
            </div>
            <div class="buttons_container">
              <div class="cell">
                <?php if($userNotRegistered == true && !$competitionAviable): ?>
                  <?php if($competitionStatus == 1 && $_SESSION['auth_user_profile_filled'] == true && $_SESSION['auth_user_role_id'] == 4): ?>
                    <a competition-id='<?= $competition['competition_id']; ?>' class="tp_link gbutton">Взяти участь</a>
                  <?php endif; ?>
                <?php elseif($userNotRegistered == false && $_SESSION['auth_user'] == true && $competitionStatus != 2): ?>
                  <a class="rbutton refuse_competition">Відмовитися від участі</a>
                <?php endif; ?>
              </div>
              <div class="cell">
                <a class="button back">Назад</a>
              </div>
            </div>
          </div>
          <div class="row competition_info competition_presentation_container">
            <h2>Детальна інформація</h2>
            <div class="col-12 col-lg-6 col-xl-5 view_competition_image">
              <img src="uploads_images/<?= $competition['competition_image']; ?>">
            </div>
            <div class="col-12 col-lg-6 col-xl-7">
              <h2 class="competition_name"><?= $competition['competition_name']; ?></h2>
              <h3><span>Рейтинг змагання: </span><?= $competition['competition_votes']; ?> голосів (<?php if($competition['competition_votes'] > 0): ?><?= round($competition['rating_percentage'], 2); ?><?php else: ?>0<?php endif; ?>%)</h3>
              <div class="competition_rating">
                <i class="fas fa-thumbs-up like <?php if($_COOKIE[$competitionId.":".$userIp.':like'] == 1): ?>selected<?php endif; ?>" title="Мені подобається"></i>
                <div class="rating_bar">
                  <div class="likes_percentage" style="width: <?php if($competition['competition_votes'] > 0): ?><?= $competition['rating_percentage']; ?>%<?php else: ?>0<?php endif; ?>;">
                    <span>
                      <?php if($competition['competition_votes'] > 0): ?><?= round($competition['rating_percentage'], 2); ?><?php else: ?>0<?php endif; ?>% (<?= $competition['competition_likes']; ?>)
                    </span>
                  </div>
                  <div class="dislikes_percentage" style="width: <?php if($competition['competition_votes'] > 0): ?><?= 100 - $competition['rating_percentage']; ?>%<?php else: ?>0<?php endif; ?>;">
                    <span>
                      <?php if($competition['competition_votes'] > 0): ?><?= round(100 - $competition['rating_percentage'], 2); ?><?php else: ?>0<?php endif; ?>% (<?= $competition['competition_dislikes']; ?>)
                    </span>
                  </div>
                </div>
                <i class="fas fa-thumbs-down dislike <?php if($_COOKIE[$competitionId.":".$userIp.':dislike'] == 1): ?>selected<?php endif; ?>" title="Мені не подобається"></i>
              </div>
              <h3><span>Статус:</span> <?= $competitionStatusName ?></h3>
              <h3><span>Організатор:</span> <?= $competition['competition_organizer']; ?></h3>
              <h3><span>Судді:</span> <?= $competition['competition_judges_names_list']; ?></h3>
              <h3 class="datetime"><span>Дата та час початку:</span> <?= changeDateFormat($competition['competition_begining'], "d/m/Y H:i"); ?></h3>
              <h3 class="datetime"><span>Дата та час завершення:</span> <?= changeDateFormat($competition['competition_ending'], "d/m/Y H:i"); ?></h3>
              <h3><span>Вік учасників:</span> <?= $competition['competition_age_range']." років"; ?> </h3>
              <h3><span>Стать учасників:</span> <?= $competition['competition_sex']; ?></h3>
              <h3><span>Країни, в яких проходить змагання:</span> <?= implode($competitionCountriesNames, ", "); ?></h3>
              <h3><span>Категорії, до яких відноситься змагання:</span> <?= implode($competitionTagsNames, ", "); ?></h3>
              <h3><span>Призи:</span> <?= $competition['competition_prizes']; ?></h3>
            </div>
            <h2>Опис змагання</h2>
            <div class="competition_description col-12">
              <?= $competition['competition_description']; ?>
            </div>
            <h2>Додаткові файли</h2>
            <div class="col-lg-6 col-12">
              <div class="competition_work_files">
                <?php if($competition['competition_files']): ?>
                  <?php $it = 0; foreach(json_decode($competition['competition_files']) as $file): ?>
                    <div class='file-container-item no-file'>
                      <div>
                        <img src='images/extensions/<?= getExtension($file); ?>.png' style="width: 40px; height: 40px;">
                        <label for='work-file<?= $it; ?>'><?= explode("&=&", $file)[1]; ?></label>
                      </div>
                      <span class="download"><a href="uploads_files/<?= $file; ?>" download></a></span>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="competition_evaluation_table xscroller">
            <?php if($participantRole == "participant"): ?>
              <h2>Ваш результат:</h2>
              <table class="assessment_table" style="margin-bottom: 20px;">
                <tr>
                  <th><?php if($competitionStatus != 2): ?>ID учасника <?php else: ?> ПІБ учасника <?php endif; ?></th>
                  <?php foreach($competitionCriteria as $criterion): ?>
                    <th class="criteria_th"><?= $criterion['name']; ?></th>
                  <?php endforeach; ?>
                  <th class="sm">Сума балів</th>
                  <th class="sm">Місце</th>
                  <th class="sm">Диплом</th>
                </tr>
                <tr>
                  <td><?php if($competitionStatus != 2): ?><?= $competitionUserId; ?><?php else: ?><?= getUserNameById($link, $userId); ?><?php endif; ?></td>
                  <?php foreach($competitionCriteria as $criterion): $columns++; ?>
                    <td style="background-color: <?= getRGBA(max(0, $competitionTable[$userId]['finalResult'][$columns - 1]), $criterion['min_value'], $criterion['max_value']); ?>;">
                      <span>
                        <?= max(0, $competitionTable[$userId]['finalResult'][$columns - 1]); ?>
                      </span>
                    </td>
                  <?php endforeach; $columns = 0; ?>
                  <td class="bl"><?= max(0, $competitionTable[$userId]['summary']); ?></td>
                  <td><?= $competitionTable[$userId]['place']; ?></td>
                  <td>-</td>
                </tr>
              </table>
              <h2>Оцінки від суддів та коментарі:</h2>
              <table class="assessment_table" style="margin-bottom: 20px;">
                <tr>
                  <th>ID члена журі</th>
                  <?php foreach($competitionCriteria as $criterion): ?>
                    <th class="criteria_th"><?= $criterion['name']; ?></th>
                  <?php endforeach; ?>
                  <th class="jd">Сума балів</th>
                </tr>
                <?php foreach($competitionJudgesValues as $judge): ?>
                  <tr>
                    <td><?= $judge['userData']['user_login']; ?></td>
                    <?php foreach($competitionCriteria as $criterion): $columns++; ?>
                      <td class="user_value_cell" commentary="<?= $judge['userAssessmentCommentaries'][$columns - 1]; ?>" style="background-color: <?= getRGBA(max(0, $judge['userAssessmentValues'][$columns - 1]), $criterion['min_value'], $criterion['max_value']); ?>;">
                        <span>
                          <?= max(0, $judge['userAssessmentValues'][$columns - 1]); ?>
                        </span>
                      </td>
                    <?php endforeach; $columns = 0; ?>
                    <td><?= max(0, $judge['summary']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </table>
            <?php endif; ?>
            <h2>Загальна таблиця учасників:</h2>
            <table class="assessment_table">
              <tr>
                <th><?php if($competitionStatus != 2): ?>ID учасника <?php else: ?> ПІБ учасника <?php endif; ?></th>
                <?php foreach($competitionCriteria as $criterion): ?>
                  <th class="criteria_th"><?= $criterion['name']; ?></th>
                <?php endforeach; ?>
                <th class="sm">Сума балів</th>
                <th class="sm">Місце</th>
                <th class="sm">Диплом</th>
              </tr>
              <?php foreach($competitionParticipants as $participant): $rows++; ?>
                <tr <?php if($participant['user_id'] == $userId): ?>style="border-top: 2px solid #354251; border-bottom: 2px solid #354251;"<?php endif; ?>>
                  <td <?php if($participant['user_id'] == $userId): ?> style="font-weight: bold; color: black; letter-spacing: 1px;" <?php endif; ?>><?php if($competitionStatus != 2): ?><?= getCompetitionUserId($link, $participant['user_id'], $competitionId); ?><?php else: ?><?= getUserNameById($link, $participant['user_id']); ?><?php endif; ?></td>
                  <?php foreach($competitionCriteria as $criterion): $columns++; ?>
                    <td style="background-color: <?= getRGBA(max(0, $competitionTable[$participant['user_id']]['finalResult'][$columns - 1]), $criterion['min_value'], $criterion['max_value']); ?>;">
                      <span>
                        <?= max(0, $competitionTable[$participant['user_id']]['finalResult'][$columns - 1]); ?>
                      </span>
                    </td>
                  <?php endforeach; $columns = 0; ?>
                  <td class="bl"><?= max(0, $competitionTable[$participant['user_id']]['summary']); ?></td>
                  <td><?= $competitionTable[$participant['user_id']]['place']; ?></td>
                  <?php
                    if($rows > $firstPlaces || $rows > $secondPlaces || $rows > $thirdPlaces){
                      if($rows > $firstPlaces) $firstPlaces = 1e9;
                      if($rows > $secondPlaces) $secondPlaces = 1e9;
                      if($rows > $thirdPlaces) $thirdPlaces = 1e9;
                      $iterator++;
                    }
                  ?>
                  <td class="<?= $placeClass[$iterator]; ?>">-</td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
          <div class="competition_news">
            <div class="competition_news_container">
              <div class="competition_post">
                <div class="page_header">
                  <h3>Оновлення таблиці результатів змагання</h3>
                  <span>12/09/21 19:30</span>
                </div>
                <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pulvinar purus scelerisque, mollis risus ut, bibendum lacus. Duis mattis justo lectus. Aenean cursus, felis et semper rutrum, orci metus posuere nisi, quis rutrum ipsum ex sit amet nunc. Maecenas at sapien vitae neque vestibulum eleifend. Integer bibendum, ipsum a semper eleifend, nisl tellus luctus tellus, ac vehicula felis leo nec velit. Nulla egestas placerat libero, sed commodo elit. In hac habitasse platea dictumst. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer eu sagittis quam, vel blandit leo. Quisque venenatis tortor diam, sollicitudin porttitor urna scelerisque a. Aliquam placerat imperdiet ante quis cursus.</p>
                </div>
                <div class="footer">
                  <a class="button" href="news?post_id=1">Читати далі »</a>
                </div>
              </div>
              <div class="competition_post">
                <div class="page_header">
                  <h3>Оновлення таблиці результатів змагання</h3>
                  <span>12/09/21 19:30</span>
                </div>
                <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pulvinar purus scelerisque, mollis risus ut, bibendum lacus. Duis mattis justo lectus. Aenean cursus, felis et semper rutrum, orci metus posuere nisi, quis rutrum ipsum ex sit amet nunc. Maecenas at sapien vitae neque vestibulum eleifend. Integer bibendum, ipsum a semper eleifend, nisl tellus luctus tellus, ac vehicula felis leo nec velit. Nulla egestas placerat libero, sed commodo elit. In hac habitasse platea dictumst. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer eu sagittis quam, vel blandit leo. Quisque venenatis tortor diam, sollicitudin porttitor urna scelerisque a. Aliquam placerat imperdiet ante quis cursus.</p>
                </div>
                <div class="footer">
                  <a class="button" href="news?post_id=1">Читати далі »</a>
                </div>
              </div><div class="competition_post">
                <div class="page_header">
                  <h3>Оновлення таблиці результатів змагання</h3>
                  <span>12/09/21 19:30</span>
                </div>
                <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pulvinar purus scelerisque, mollis risus ut, bibendum lacus. Duis mattis justo lectus. Aenean cursus, felis et semper rutrum, orci metus posuere nisi, quis rutrum ipsum ex sit amet nunc. Maecenas at sapien vitae neque vestibulum eleifend. Integer bibendum, ipsum a semper eleifend, nisl tellus luctus tellus, ac vehicula felis leo nec velit. Nulla egestas placerat libero, sed commodo elit. In hac habitasse platea dictumst. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer eu sagittis quam, vel blandit leo. Quisque venenatis tortor diam, sollicitudin porttitor urna scelerisque a. Aliquam placerat imperdiet ante quis cursus.</p>
                </div>
                <div class="footer">
                  <a class="button" href="news?post_id=1">Читати далі »</a>
                </div>
              </div><div class="competition_post">
                <div class="page_header">
                  <h3>Оновлення таблиці результатів змагання</h3>
                  <span>12/09/21 19:30</span>
                </div>
                <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pulvinar purus scelerisque, mollis risus ut, bibendum lacus. Duis mattis justo lectus. Aenean cursus, felis et semper rutrum, orci metus posuere nisi, quis rutrum ipsum ex sit amet nunc. Maecenas at sapien vitae neque vestibulum eleifend. Integer bibendum, ipsum a semper eleifend, nisl tellus luctus tellus, ac vehicula felis leo nec velit. Nulla egestas placerat libero, sed commodo elit. In hac habitasse platea dictumst. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer eu sagittis quam, vel blandit leo. Quisque venenatis tortor diam, sollicitudin porttitor urna scelerisque a. Aliquam placerat imperdiet ante quis cursus.</p>
                </div>
                <div class="footer">
                  <a class="button" href="news?post_id=1">Читати далі »</a>
                </div>
              </div><div class="competition_post">
                <div class="page_header">
                  <h3>Оновлення таблиці результатів змагання</h3>
                  <span>12/09/21 19:30</span>
                </div>
                <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pulvinar purus scelerisque, mollis risus ut, bibendum lacus. Duis mattis justo lectus. Aenean cursus, felis et semper rutrum, orci metus posuere nisi, quis rutrum ipsum ex sit amet nunc. Maecenas at sapien vitae neque vestibulum eleifend. Integer bibendum, ipsum a semper eleifend, nisl tellus luctus tellus, ac vehicula felis leo nec velit. Nulla egestas placerat libero, sed commodo elit. In hac habitasse platea dictumst. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer eu sagittis quam, vel blandit leo. Quisque venenatis tortor diam, sollicitudin porttitor urna scelerisque a. Aliquam placerat imperdiet ante quis cursus.</p>
                </div>
                <div class="footer">
                  <a class="button" href="news?post_id=1">Читати далі »</a>
                </div>
              </div>
            </div>
            <div class="page_numeration">
              <nav>
                <a>«</a>
                <a>1</a>
                <a>2</a>
                <a>3</a>
                <a class="active">4</a>
                <a>5</a>
                <a>6</a>
                <a>7</a>
                <a>8</a>
                <a>9</a>
                <a>»</a>
              </nav>
            </div>
          </div>
          <div class="competition_work">
            <div class="col-lg-6 col-12">
              <label>Ваш коментар до роботи: (нюанси, посилання, інструкція користувача тощо)</label>
              <textarea><?= $participantData['participant_commentary']; ?></textarea>
            </div>
            <div class="col-lg-6 col-12">
              <label>Файли: (Фото, відео, аудіо, файли формату .docx, .doc, .xls, .xlsx, .ppt, .pptx, .txt, .pdf, zip-архіви)</label>
              <div class="competition_work_files cwfiles">
                <?php if($participantData['participant_work_files']): ?>
                  <?php $it = 0; foreach($participantData['participant_work_files'] as $file): ?>
                    <div class='file-container-item no-file'>
                      <div>
                        <img src='images/extensions/<?= getExtension($file); ?>.png' style="width: 40px; height: 40px;">
                        <label for='work-file<?= $it; ?>'><?= $file; ?></label>
                      </div>
                      <span class="download"><a href="uploads_files/<?= $file; ?>" download></a></span>
                      <span class="remove"></span>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <a class="button add_file_input" file-container="cwfiles">Додати файли</a>
            </div>
            <div class="col-12 page_footer">
              <a class="button send_work">Надіслати роботу</a>
            </div>
          </div>
        </div>
        <div class="competition_registration_message" style="display: none;">
          <div class="competition_registration_success" style="display: none;">
            <img src="images/participation.svg">
            <h3>Дякуємо, що берете участь! Бажаємо Вам успіху!</h3>
            <a href="competitions.php" class="button">Повернутися на сторінку зі змаганнями</a>
          </div>
          <div class="competition_registration_error" style="display: none;">
            <img src="images/sad.png">
            <h3>На жаль виникли певні неполадки. Повторіть спробу.</h3>
            <a href="competitions.php" class="button">Повернутися на сторінку зі змаганнями</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
  include("includes/footer.php");
?>
