<?php
  include("includes/header.php");
  $action = $_GET['action'];
  $competitionId = $_GET['competition-id'];
  $competition = [];
  $competition['competition_judges_id'] = [];
  $competition['competition_criteria'] = [];
  if($competitionId){
    $competition = getCompetitionById($link, $competitionId);
  }
  if($action == 'create-competition')
    $actionName = "Створення змагання";
  else
    $actionName = "Редагування змагання";
  $competition['competition_certificates_status'] = max($competition['competition_certificates_status'], 1);
  if($competition['competition_certificates_value'] == "") $competition['competition_certificates_value'] = "Відсутні";
  if($competition['competition_prizes'] == "") $competition['competition_prizes'] = "Відсутні";
  if($competition['competition_sex'] == "") $competition['competition_sex'] = "Будь-яка";
  if($competition['competition_min_age'] == "") $competition['competition_min_age'] = 0;
  if($competition['competition_max_age'] == "") $competition['competition_max_age'] = 0;
  if($competition["competition_countries_names"] == "") $competition["competition_countries_names"] = "Усі";
  if(empty($competition["competition_countries"])) $competition["competition_countries"][] = "all";
  if($competition["competition_tags_names"] == "") $competition["competition_tags_names"] = "Не обрано";
  if(empty($competition["competition_tags"])) $competition["competition_tags"][] = "";
  $judges = getAllJudges($link);
  $userData = getPersonalUserData($link);
  $countries = getAllCountries($link);
  $competitionTags = getAllCompetitionTags($link);
  if($competitionId) $competition['competition_files'] = json_decode($competition['competition_files']);
?>
<section>
  <div class="container">
    <div class="row competition_action">
      <div class="page_header" style="padding: 10px 0 10px 0; border-bottom: 1px solid silver;">
        <h2><?= $actionName; ?></h2>
        <a class="button back">Назад</a>
      </div>
      <div class="row competition_action_container">
        <div class="col-12 col-lg-6">
          <label>Назва <span>*</span></label>
          <input type="text" name="competition_name" value="<?= $competition['competition_name']; ?>">
        </div>
        <div class="col-12 col-lg-6">
          <label>Організатор <span>*</span></label>
          <input type="text" name="competition_organizer" value="<?php if($competition['competition_organizer'] == ""): ?><?= $userData['user_organization_name']; ?><?php else: ?><?= $competition['competition_organizer']; ?><?php endif; ?>">
        </div>
        <div class="col-12 col-lg-3">
          <label>Дата та час початку <span>*</span></label>
          <input type="datetime-local" name="competition_begining" value="<?= changeDateFormat($competition['competition_begining'], "Y-m-d\TH:i"); ?>">
        </div>
        <div class="col-12 col-lg-3">
          <label>Дата та час закінчення <span>*</span></label>
          <input type="datetime-local" name="competition_ending" value="<?= changeDateFormat($competition['competition_ending'], "Y-m-d\TH:i"); ?>">
        </div>
        <div class="col-12 col-lg-3">
          <label>Вік учасників <span>*</span></label>
          <div class="age_range">
            <label>Від: </label>
            <input type="number" name="min_age" value="<?= $competition['competition_min_age']; ?>" min="0" max="150">
            <label>До: </label>
            <input type="number" name="max_age" value="<?= $competition['competition_max_age']; ?>" min="0" max="150">
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <label>Стать учасників <span>*</span></label>
          <span name="competition_participants_sex" class="selector disabled" selected_value="<?= $competition['competition_sex']; ?>"><label><?= $competition['competition_sex']; ?></label></span>
          <div class="selector-options yscroller">
            <div class="option" value="Чоловіча">Чоловіча</div>
            <div class="option" value="Жіноча">Жіноча</div>
            <div class="option" value="Будь-яка">Будь-яка</div>
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <label><div>Розподіл дипломів <a class="change_certificates" status="<?= $competition['competition_certificates_status']; ?>">Змінити</a></div></label>
          <input type="text" name="competition_certificates" class="disabled" disabled value="<?= $competition['competition_certificates_value']; ?>">
        </div>
        <div class="col-12 col-lg-3">
          <label>Призи</label>
          <input type="text" name="competition_prizes" value="<?= $competition['competition_prizes']; ?>">
        </div>
        <div class="col-12 col-lg-3">
          <label>Країни, які можуть брати участь <span>*</span></label>
          <span name="competition_countries" class="selector disabled" selected_value="<?= implode(", ", $competition['competition_countries']); ?>"><label><?= $competition['competition_countries_names']; ?></label></span>
          <div class="countries_container yscroller">
            <img src="images/close.png" class="close-icon">
            <h2>Список країн: </h2>
            <div class="search_container">
              <a class="search_button">Знайти</a>
              <input type="text" placeholder="Пошук">
            </div>
            <div class="container_content">
              <?php foreach($countries as $country): ?>
                <div class="countries_container_item col-4 col-lg-3 col-xl-2 <?php if(array_search($country['Code2'], $competition['competition_countries']) !== false): ?> selected <?php endif; ?>" value="<?= $country['Code2']; ?>">
                  <img src="images/countries/<?= strtolower($country['Code2']); ?>.png">
                  <h4><?= $country['title_ua']; ?></h4>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="container_footer">
              <div class="select_all">
                <label for="chk1">Обрати всі країни</label><input id="chk1" type="checkbox">
              </div>
              <a class="button accept_countries">Додати</a>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <label>Теги змагання <span>*</span></label>
          <span class="selector disabled" name="competition_tags" selected_value="<?= implode(", ", $competition['competition_tags']); ?>"><label><?= $competition["competition_tags_names"]; ?></label></span>
          <div class="competition_tags_container yscroller">
            <h2>Додати теги змагань</h2>
            <img src="images/close.png" class="close-icon">
            <div class="search_container">
              <a class="search_button">Знайти</a>
              <input type="text" placeholder="Пошук">
            </div>
            <div class="container_content">
              <?php foreach($competitionTags as $tag): ?>
                <label value="<?= $tag['tag_id']; ?>" <?php if(array_search($tag['tag_id'], $competition['competition_tags']) !== false): ?> class="active" <?php endif; ?>><?= $tag['tag_name']; ?></label>
              <?php endforeach; ?>
            </div>
            <div class="container_footer">
              <a class="accept_tags button">Додати</a>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <label>Члени журі <span>*</span></label>
          <div class="judges_container">
            <div class="judges">
              <?php if($competitionId): ?>
                <?php foreach($competition['competition_judges'] as $judge): ?>
                  <div class="judges_list_item">
                    <p class="ex-login" user-id="<?= $judge['user_id']; ?>">
                      <?= $judge['user_login'] ?>
                    </p>
                    <p>
                      <?= $judge['judge_password']; ?>
                    </p>
                  <img src="images/bin.png" class="remove_judge" title="Вилучити зі списку"></div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <a class="add_judges button">Додати члена журі</a>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <label>Критерії оцінювання (назва, мін. бал, макс. бал) <span>*</span></label>
          <div class="criteria_container">
            <div class="criteria">
              <?php foreach($competition['competition_criteria'] as $criterion): ?>
                <div class="criterion">
                  <input type="text" placeholder="Назва критерію" class="criterion_name" value="<?= $criterion['name']; ?>">
                  <input type="number" placeholder="Мін. бал" step="0.01" class="criterion_min_value" value="<?= $criterion['min_value']; ?>">
                  <input type="number" placeholder="Макс. бал" step="0.01" class="criterion_max_value" value="<?= $criterion['max_value']; ?>">
                  <img src="images/bin.png" class="remove_criteria" title="Вилучити зі списку">
                </div>
              <?php endforeach; ?>
            </div>
            <a class="add_criteria button">Додати критерій</a>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <label>Вкладені файли: (Фото, відео, аудіо, файли формату .docx, .doc, .xls, .xlsx, .ppt, .pptx, .txt, .pdf, zip-архіви)</label>
          <div class="competition_work_files cfiles">
            <?php if($competition['competition_files']): ?>
              <?php $it = 0; foreach($competition['competition_files'] as $file): ?>
                <div class='file-container-item no-file'>
                  <div>
                    <img src='images/extensions/<?= getExtension($file); ?>.png' style="width: 40px; height: 40px;">
                    <label for='work-file<?= $it; ?>'><?= explode("&=&", $file, 2)[1]; ?></label>
                  </div>
                  <span class="download"><a href="uploads_files/<?= $file; ?>" download></a></span>
                  <span class="remove"></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <a class="button add_file_input" file-container="cfiles">Додати файли</a>
        </div>
        <div class="col-12 col-lg-4 col-xl-3">
          <label>Логотип <span>*</span></label>
          <div id="upload-container">
            <img id="upload-image" src="images/upload.png">
            <div>
              <input id="file-input" type="file" name="file" value="" multiple>
              <label for="file-input">Оберіть файл <span>або перетягніть його сюди</span></label>
              <div class="file_names"></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-8 col-xl-9">
          <label>Опис <span>*</span></label>
          <textarea class="ckeditor" id="editor" name="competition_description"><?= $competition['competition_description']; ?></textarea>
        </div>
      </div>
      <?php if($action == "create-competition"): ?>
        <a class="add_competition button">Створити змагання</a>
      <?php else: ?>
        <a class="save_competition button">Зберегти зміни</a>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
  include("includes/footer.php");
?>
