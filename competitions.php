<?php
  include("includes/header.php");
  $params = [];
  if($_GET['q'] != "") $params['q'] = $_GET['q'];
  $statusCheckboxes = [0, "selected", "selected", "selected"];
  if($_GET["status"] != ""){
    $params['status'] = $_GET['status'];
    $status = explode(',', $_GET['status']);
    $statusCheckboxes = [];
    foreach($status as $item){
      $statusCheckboxes[$item] = "selected";
    }
  }
  $minAge = 0;
  $maxAge = 150;
  if($_GET['age']){
    $params['min_age'] = explode("-", $_GET['age'])[0];
    $params['max_age'] = explode("-", $_GET['age'])[1];
    $minAge = $params['min_age'];
    $maxAge = $params['max_age'];
  }
  $begining = "";
  $ending = "";
  if($_GET['from'] != ""){
    $params['begining'] = $_GET['from'];
    $begining = $params['begining'];
  }
  if($_GET['to'] != ""){
    $params['ending'] = $_GET['to'];
    $ending = $params['ending'];
  }
  $genderName = "Не важливо";
  $gender = "";
  if($_GET['participant_gender'] != ""){
    $params['participant_sex'] = $_GET['participant_gender'];
    $genderName = $gender = $params['participant_sex'];
    if($gender == "") $genderName = "Не важливо";
    if($gender == "Будь-яка") $genderName = "Чоловіча та жіноча";
  }
  $selectedCountries = [];
  $countriesNames = "Усі";
  if($_GET['countries'] != ""){
    $params['countries'] = $_GET['countries'];
    $selectedCountries = explode(',', $_GET['countries']);
    $countriesNames = getCountryNamesById($link, $selectedCountries);
  }
  $selectedTags = [];
  $tagsNames = "Усі";
  if($_GET['tags'] != ""){
    $params['tags'] = $_GET['tags'];
    $selectedTags = explode(',', $_GET['tags']);
    $tagsNames = getTagsNamesById($link, $selectedTags);
  }
  $selectedOrganizers = [];
  if($_GET['organizers'] != ""){
    $params['organizers'] = $_GET['organizers'];
    $selectedOrganizers = explode(',', $_GET['organizers']);
  }
  $minRating = 0;
  $maxRating = 100;
  if($_GET['rating']){
    $params['min_rating'] = explode("-", $_GET['rating'])[0];
    $params['max_rating'] = explode("-", $_GET['rating'])[1];
    $minRating = $params['min_rating'];
    $maxRating = $params['max_rating'];
  }
  $competitions = getAllCompetitionsByParams($link, $params);
  $countries = getAllCountries($link);
  $competitionTags = getAllCompetitionTags($link);
  $organizers = getAllOrganizers($link);
?>
<section id="competitions">
  <div class="countries_container yscroller">
    <img src="images/close.png" class="close-icon">
    <h2>Список країн: </h2>
    <div class="search_container">
      <a class="search_button">Знайти</a>
      <input type="text" placeholder="Пошук">
    </div>
    <div class="container_content">
      <?php foreach($countries as $country): ?>
        <div class="countries_container_item col-4 col-lg-3 col-xl-2 <?php if(array_search($country['Code2'], $selectedCountries) !== false): ?> selected <?php endif; ?>" value="<?= $country['Code2']; ?>">
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
  <div class="competition_tags_container yscroller">
    <h2>Додати теги змагань</h2>
    <img src="images/close.png" class="close-icon">
    <div class="search_container">
      <a class="search_button">Знайти</a>
      <input type="text" placeholder="Пошук">
    </div>
    <div class="container_content">
      <?php foreach($competitionTags as $tag): ?>
        <label value="<?= $tag['tag_id']; ?>" <?php if(array_search($tag['tag_id'], $selectedTags) !== false): ?> class="active" <?php endif; ?>><?= $tag['tag_name']; ?></label>
      <?php endforeach; ?>
    </div>
    <div class="container_footer">
      <a class="accept_tags button">Додати</a>
    </div>
  </div>
  <div class="competitions_list_parameters">
    <div class="filters_section">
      <img src="images/filter.png" alt="">
      <label>Фільтри</label>
    </div>
    <div class="sorting_section">
      <img src="images/sorting.png" alt="">
      <label>Сортування</label>
    </div>
    <div class="search_section">
      <a><img src="images/loupe.png" alt=""><label>Пошук</label></a>
      <input type="search" value="<?= $_GET['q']; ?>">
    </div>
  </div>
  <div class="filters_container container">
    <div class="row close-section">
      <div class="page_header">
        <h2>Фільтри</h2>
        <div class="buttons_container">
          <a class="button">Зберегти зміни</a>
          <a class="tbutton hide_filters_container">Назад</a>
        </div>
      </div>
    </div>
    <div class="row yscroller">
      <div class="set col-12 col-md-6 col-xl-4 cpf_status">
        <h3>Поточний стан змагань</h3>
        <div class="checkbox_container <?= $statusCheckboxes[1]; ?>" value="1">
          <input type="checkbox" id="fch1" checked><label for="fch1">Ще не розпочалися</label>
        </div>
        <div class="checkbox_container <?= $statusCheckboxes[2]; ?>" value="2">
          <input type="checkbox" id="fch3" checked><label for="fch3">Завершені</label>
        </div>
        <div class="checkbox_container <?= $statusCheckboxes[3]; ?>" value="3">
          <input type="checkbox" id="fch2" checked><label for="fch2">Тривають</label>
        </div>
      </div>
      <div class="set col-12 col-md-6 col-xl-4 cpf_age_range">
        <h3>Вік учасників</h3>
        <div class="age_range">
          <label>Від: </label>
          <input type="number" name="min_age" value="<?= $minAge; ?>" min="0" max="150">
          <label>До: </label>
          <input type="number" name="max_age" value="<?= $maxAge; ?>" min="0" max="150">
        </div>
      </div>
      <div class="set col-12 col-md-6 col-xl-4 cpf_time_range">
        <h3>Період проведення</h3>
        <div>
          <label>Початок: </label>
          <input type="datetime-local" name="competition_begining" value="<?= $begining; ?>">
        </div>
        <div>
          <label>Закінчення: </label>
          <input type="datetime-local" name="competition_ending" value="<?= $ending; ?>">
        </div>
      </div>
      <div class="set col-12 col-md-6 col-xl-4 cpf_participant_sex">
        <h3>Стать учасників</h3>
        <span class="selector disabled" selected_value="<?= $gender; ?>"><label><?= $genderName; ?></label></span>
        <div class="selector-options yscroller">
          <div class="option" value="">Не важливо</div>
          <div class="option" value="Чоловіча">Чоловіча</div>
          <div class="option" value="Жіноча">Жіноча</div>
          <div class="option" value="Будь-яка">Чоловіча та жіноча</div>
        </div>
      </div>
      <div class="set col-12 col-md-6 col-xl-4 cpf_countries">
        <h3>Країни, які можуть брати участь</h3>
        <span class="selector disabled" selected_value="<?= $_GET['countries']; ?>" name="competition_countries"><label><?= $countriesNames; ?></label></span>
      </div>
      <div class="set col-12 col-md-6 col-xl-4 cpf_tags">
        <h3>Теги змагань</h3>
        <span class="selector disabled" selected_value="<?= $_GET['tags']; ?>" name="competition_tags"><label><?= $tagsNames; ?></label></span>
      </div>
      <div class="set col-12 col-md-6 col-lg-8 cpf_organizers">
        <h3>Організатори</h3>
        <div class="yscroller competition-organizers">
          <?php foreach($organizers as $organizer): ?>
            <div class="checkbox_container <?php if(array_search($organizer['competition_organizer'], $selectedOrganizers) !== false): ?> selected <?php endif; ?>" organizer-id="<?= $organizer['creator_id']; ?>">
              <input type="checkbox" id="fch-user-<?= $organizer['creator_id']; ?>">
              <label for="fch-user-<?= $organizer['creator_id']; ?>"><?= $organizer['competition_organizer']; ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="set col-12 col-md-6 col-lg-4 cpf_rating_range">
        <h3>Рейтинг змагань</h3>
        <div>
          <label>Мінімальний рейтинг (%): </label>
          <input type="number" step="1" min="0" max="100" value="<?= $minRating; ?>" name="min_rating">
        </div>
        <div>
          <label>Максимальний рейтинг (%): </label>
          <input type="number" step="1" min="0" max="100" value="<?= $maxRating; ?>" name="max_rating">
        </div>
      </div>
    </div>
    <div class="row hide-container">
      <p class="hide_filters_container"><img src="images/swipe.png">Сховати</p>
    </div>
  </div>
  <div class="container main_page_container" style="padding: 0px 0px 15px 0px;">
    <div class="row latest_competitions">
      <div class="latest_competitions_container">
        <?php foreach($competitions as $competition): ?>
          <div class="latest_competitions_item" competition-id='1'>
            <div>
              <img src="uploads_images/<?= $competition['competition_image']; ?>">
            </div>
            <div>
              <h3><?= $competition['competition_name']; ?></h3>
              <div class="status_bar">
                <div class="tags">
                  <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 3): ?><span class="yellow">Триває</span><?php endif; ?>
                  <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 1): ?><span class="green">Триває реєстрація</span><?php endif; ?>
                  <?php if(checkCompetitionStatus($link, $competition['competition_id']) == 2): ?><span class="gray">Завершено</span><?php endif; ?>
                  <?php if($competition['rating_percentage'] > 70): ?><span class="red">Популярне</span><?php endif; ?>
                  <span class="blue">Новинка</span>
                </div>
                <div class="rating">
                  <div class="votes_number">
                    <?= $competition['competition_votes']; ?> голосів
                  </div>
                  <div>
                    <div><?php if($competition['competition_votes'] > 0): ?><?= round($competition['rating_percentage'], 0); ?>%<?php else: ?>0%<?php endif; ?></div>
                    <div class="rating_bar">
                      <div class="likes_percentage" style="width: <?php if($competition['competition_votes'] > 0): ?><?= $competition['rating_percentage']; ?>%<?php else: ?>0<?php endif; ?>;"></div>
                      <div class="dislikes_percentage" style="width: <?php if($competition['competition_votes'] > 0): ?><?= 100 - $competition['rating_percentage']; ?>%<?php else: ?>0<?php endif; ?>;"></div>
                    </div>
                    <div><?php if($competition['competition_votes'] > 0): ?><?= 100 - round($competition['rating_percentage'], 0); ?>%<?php else: ?>0%<?php endif; ?></div>
                  </div>
                </div>
              </div>
              <div class="bottom">
                <h6>Дата проведення: <?= changeDateFormat($competition['competition_begining'], "d/m/Y h:i"); ?> - <?= changeDateFormat($competition['competition_ending'], "d/m/Y h:i"); ?></h6>
                <a class="button" href="view_competition.php?competition-id=<?= $competition['competition_id']; ?>">Детальніше</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php
  include("includes/footer.php");
?>
