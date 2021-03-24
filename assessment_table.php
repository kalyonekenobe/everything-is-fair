<?php
  include("includes/header.php");
  $competitionId = $_GET['competition-id'];
  $judgeId = $_SESSION['auth_user_id'];
  $competitionTable = getCompetitionTable($link, $judgeId, $competitionId);
  $competitionCriteria = getAllCompetitionCriteria($link, $competitionId);
  $competitionParticipants = getAllCompetitionParticipants($link, $competitionId);
  $scolumns = $columns = $rows = $iterator = 0;
  $firstPlaces = $secondPlaces = $thirdPlaces = 1e9;
  $participantData = [];
  if($_GET['user-id'] != "") $participantData = getParticipantData($link, $competitionId, $_GET['user-id']);
?>
<section>
  <div class="container">
    <div class="row view_competition assessment_table_container" style="padding-left: 15px; padding-right: 15px;">
      <?php if($_GET['user-id'] == ""): ?>
        <div class="page_header" style="margin: 0 5px 10px 5px;">
          <div class="container_navbar">
            <nav>
              <a class='selected' container-name='view_competition_container'>Таблиця учасників</a>
              <a container-name='competition_posts'>Оголошення</a>
            </nav>
          </div>
          <a href="personal_office.php" class="button">Назад</a>
        </div>
        <div class="col-12 view_competition_container xscroller">
          <div class="page_header">
            <h2>Таблиця учасників</h2>
          </div>
          <table class="assessment_table">
            <tr>
              <th><?php if($competitionStatus != 2): ?>ID учасника <?php else: ?> ПІБ учасника <?php endif; ?></th>
              <th class="sm">Робота</th>
              <?php foreach($competitionCriteria as $criterion): ?>
                <th class="criteria_th"><?= $criterion['name']; ?></th>
              <?php endforeach; ?>
              <th class="sm">Сума балів</th>
              <th class="sm">Місце</th>
              <th class="sm">Диплом</th>
            </tr>
            <?php foreach($competitionParticipants as $participant): $rows++; ?>
              <tr>
                <td><?php if($competitionStatus != 2): ?><?= getCompetitionUserId($link, $participant['user_id'], $competitionId); ?><?php else: ?><?= getUserNameById($link, $participant['user_id']); ?><?php endif; ?></td>
                <td><a href="assessment_table.php?competition-id=<?= $competitionId; ?>&user-id=<?= $participant['user_id']; ?>" class="common_link">Робота учасника</a></td>
                <?php foreach($competitionCriteria as $criterion): $columns++; ?>
                  <td>
                    <input type="number" class="at_inputs" id='<?= $participant['user_id']; ?>--<?= $columns; ?>--<?= $competitionId; ?>--<?= count($competitionCriteria); ?>'
                           step="0.01" min="<?= $criterion['min_value']; ?>" max="<?= $criterion['max_value']; ?>"
                           value="<?= max(0, $competitionTable[$participant['user_id']]['values'][$columns - 1]); ?>"
                           commentary="<?= $competitionTable[$participant['user_id']]['commentaries'][$columns - 1]; ?>"
                           title="Натисніть для роботи з коментарем"
                    >
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
                  <td>-</td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
        <div class="col-12 competition_posts" style="display: none;">
          <div class="competition_news">
            <div class="page_header">
              <h2>Останні новини змагання</h2>
              <a class="button">Додати новину</a>
            </div>
          </div>
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
      <?php else: ?>
        <div class="page_header" style="margin: 0 5px 10px 5px;">
          <div class="container_navbar">
            <nav>
              <a class='selected' container-name="competition_work">Робота учасника</a>
            </nav>
          </div>
          <a class="button back">Назад</a>
        </div>
        <div class="competition_work" style="display: flex; width: 100%;">
          <div class="page_header" style="padding-left: 5px;">
            <h2>Робота учасника <span style="font-weight: bold;"><?= getCompetitionUserId($link, $_GET['user-id'], $competitionId); ?></span></h2>
          </div>
          <div class="col-lg-6 col-12">
            <label>Коментар учасника до роботи: (нюанси, посилання, інструкція користувача тощо)</label>
            <?php if($participantData['participant_commentary'] != ""): ?>
              <p style="font: 17px exo;"><?= $participantData['participant_commentary']; ?></p>
            <?php else: ?>
              <h3 class="not-found">Відсутній</h3>
            <?php endif; ?>
          </div>
          <div class="col-lg-6 col-12">
            <label>Файли: (Фото, відео, аудіо, файли формату .docx, .doc, .xls, .xlsx, .ppt, .pptx, .txt, .pdf, zip-архіви)</label>
            <div class="competition_work_files">
              <?php if($participantData['participant_work_files']): ?>
                <?php $it = 0; foreach($participantData['participant_work_files'] as $file): ?>
                  <div class='file-container-item no-file'>
                    <div>
                      <img src='images/extensions/<?= getExtension($file); ?>.png' style="width: 40px; height: 40px;">
                      <label for='work-file<?= $it; ?>'><?= $file; ?></label>
                    </div>
                    <span class="download"><a href="uploads_files/<?= $file; ?>" download></a></span>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <h3 class="not-found">Відсутні</h3>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
  include("includes/footer.php");
?>
