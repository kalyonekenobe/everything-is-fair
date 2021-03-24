<?php
  include("includes/header.php");
  $competitions = getLatestCompetitions($link, 8);
  $reviews = getAllReviews($link);
?>
<section id="competitions">
  <div class="container main_page_container">
    <div class="row latest_competitions">
      <h2>Змагання, що тривають</h2>
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
                <h6><?= changeDateFormat($competition['competition_begining'], "d/m/Y h:i"); ?> - <?= changeDateFormat($competition['competition_ending'], "d/m/Y h:i"); ?></h6>
                <a class="button" href="view_competition.php?competition-id=<?= $competition['competition_id']; ?>">Детальніше</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="main_page_link">
        <a class="button" href="competitions.php">Перейти до всіх змагань</a>
      </div>
    </div>
    <div class="row reviews_container">
      <div class="col-12 reviews_container_content">
        <h2>Відгуки користувачів</h2>
        <h5>Платформу використовує велика кількість людей. Тому найкраще про неї розкажуть вони самі.</h5>
        <div class="reviews_container_items">
          <div class="middle_container">
            <div class="reviews_container_item secondary">
              <div class="header">
                <div class="header_image">
                  <img src="images/user.png">
                </div>
                <div class="header_content">
                  <h3><?= $reviews[0]['review_author']; ?></h3>
                  <h5><?= $reviews[0]['review_datetime']; ?></h5>
                </div>
              </div>
              <div class="content">
                <?= $reviews[0]['review_text']; ?>
              </div>
            </div>
            <div class="reviews_container_item main">
              <div class="header">
                <div class="header_image">
                  <img src="images/user.png">
                </div>
                <div class="header_content">
                  <h3><?= $reviews[1]['review_author']; ?></h3>
                  <h5><?= $reviews[1]['review_datetime']; ?></h5>
                </div>
              </div>
              <div class="content">
                <?= $reviews[1]['review_text']; ?>
              </div>
            </div>
            <div class="reviews_container_item secondary">
              <div class="header">
                <div class="header_image">
                  <img src="images/user.png">
                </div>
                <div class="header_content">
                  <h3><?= $reviews[2]['review_author']; ?></h3>
                  <h5><?= $reviews[2]['review_datetime']; ?></h5>
                </div>
              </div>
              <div class="content">
                <?= $reviews[2]['review_text']; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="create_review_container">
          <a class="wbutton add_review">Додати відгук</a>
        </div>
      </div>
    </div>
    <div class="row our_benefits">
      <h2>Переваги сервісу "У нас все чесно"</h2>
      <div class="col-12 col-sm-6 col-md-4">
        <img src="images/online.png" alt="" width="150px">
        <p>Можливість проведення онлайн-змагань</p>
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <img src="images/notes.png" alt="" width="130px" style="margin-bottom: 20px;">
        <p>Широкий вибір інструментів для оцінювання робіт учасників</p>
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <img src="images/easy-use.png" alt="" width="130px" style="margin-bottom: 20px;">
        <p>Зручність та функціональність</p>
      </div>
    </div>
    <div class="row feedbacks">
      <h2>Запитання та пропозиції</h2>
      <h5>Пропонуйте свої ідеї та задавайте запитання для вдосконалення цієї платформи</h5>
      <div class="feedback_form col-12 col-xl-8">
        <label>Ім'я</label>
        <input type="text" name="feedback_author" value="<?= $_SESSION['auth_user_first_name']; ?>">
        <label>Повідомлення</label>
        <textarea name="feedback_text"></textarea>
        <a class="button send_feedback">Надіслати</a>
      </div>
    </div>
  </div>
</section>
<?php
  include("includes/footer.php");
?>
