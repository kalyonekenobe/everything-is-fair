function getURLparams(){
  let queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  return urlParams;
}
function readURL(input){
  if(input.files && input.files[0]){
    let reader = new FileReader();
    reader.onload = function(event){
      $(".profile_photo img").attr("src", event.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}
function addMessage(className, title, text){
  $(".site_messages_container").append("<div class='site_message " + className + "'><h3>" + title + "</h3><img src='images/close.png' width='17px' height='17px' style='float: right; margin-top: -17px;'><p>" + text + "</p></div>");
  if($(".site_message").length > 4) $(".site_message:first-child").remove();
  let message = $(".site_message:last-child");
  message.fadeIn(500);
  setTimeout(function(){
    message.fadeOut(500);
  }, 3000);
  $(".site_message > img").click(function(){
    $(this).parent().remove();
  });
  $(".reload-page").click(function(){
    location.reload();
  });
}
function createLog(logData){
  logData['mainAction'] = "writeLog";
  $(document).ready(function(){
    $.ajax({
      type: "POST",
      url: "/includes/rating_handler.php",
      data: logData,
      cache: false,
      success: function(data){

      }
    });
  });
}
function showNotification(title, text){
  $(".site_notification, .darkback").fadeIn(500);
  $(".site_notification").css("display", "flex");
  $(".site_notification .title").html(title);
  $(".site_notification .text").html(text);
  $(".site_notification img, .site_notification .tbutton").click(function(){
    $(".site_notification, .darkback").fadeOut(500);
  });
}
$(document).ready(function(){
    CKEDITOR.editorConfig = function(config){
      config.toolbarGroups = [
        { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
    		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
    		{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
    		{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
    		'/',
    		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
    		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
    		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
    		{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
    		'/',
    		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
    		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
    		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
    		{ name: 'about', items: [ 'About' ] }
      ];
    };
    $("section").css("min-height", $(window).height() - $(".main_navigation").height() - $("footer").height() - $(".competitions_list_parameters").height() + "px");
    $(".selector").click(function(){
      let selectorOptionsList = $(this).parent().find(".selector-options");
      if(!$(this).hasClass("active")){
        $(this).addClass("active");
        let posX = $(this).position().left;
        let posY = $(this).position().top + $(this).innerHeight();
        selectorOptionsList.css({
          left: posX + "px",
          top: posY + "px"
        });
        selectorOptionsList.show();
        selectorOptionsList.width($(this).outerWidth() - 4 + "px");
        $(".selector-options > div").click(function(){
          let target = $(this).parent().parent().find("span.selector");
          let value = $(this).attr("value");
          let text = $(this).text();
          target.children("label").first().text(text);
          target.attr("selected_value", value);
        });
      }else{
        selectorOptionsList.hide();
        $(this).removeClass("active");
      }
    });
    $(".competitions_list_parameters .filters_section").click(function(){

    });
    let status = parseInt($(".change_certificates").attr("status"));
    if(status == 1){
      $(".competition_action input[name='competition_certificates']").val("Відсутні").addClass("disabled").prop("disabled", true);
    }else if(status == 2){
      $(".competition_action input[name='competition_certificates']").removeClass("disabled").attr("placeholder", "Відношення: 3:6:12").prop("disabled", false);
    }else if(status == 3){
      $(".competition_action input[name='competition_certificates']").removeClass("disabled").attr("placeholder", "Кількість: 10:20:30").prop("disabled", false);
    }
    $(".change_certificates").click(function(){
      let status = parseInt($(this).attr("status")) + 1;
      if(status == 4) status = 1;
      if(status == 1){
        $(".competition_action input[name='competition_certificates']").val("Відсутні").addClass("disabled").prop("disabled", true);
      }else if(status == 2){
        $(".competition_action input[name='competition_certificates']").val("").removeClass("disabled").attr("placeholder", "Відношення: 3:6:12").prop("disabled", false);
      }else if(status == 3){
        $(".competition_action input[name='competition_certificates']").val("").removeClass("disabled").attr("placeholder", "Кількість: 10:20:30").prop("disabled", false);
      }
      $(this).attr("status", status);
    });
    let tagsList = $(".competition_tags_container .container_content label");
    $(".selector[name='competition_tags']").click(function(){
      let target = $(this);
      let findTagsInput = $(".competition_tags_container .search_container input")[0];
      $(".competition_tags_container").fadeIn(200).css("display", "flex");
      $(".darkback").fadeIn(200);
      $("body").css("overflow", "hidden");
      findTagsInput.addEventListener("input", function(){
        let inputValue = $(this).val().toLowerCase();
        let newTagsList = [];
        for(let i = 0; i < tagsList.length; i++){
          let item = tagsList[i];
          if(tagsList[i].textContent.toLowerCase().search(inputValue) != -1)
            newTagsList.push(item);
        }
        $(".competition_tags_container .container_content").empty();
        for(let i = 0; i < newTagsList.length; i++){
          $(".competition_tags_container .container_content").append(newTagsList[i]);
        }
        if($(".competition_tags_container .container_content").children().length == 0){
          $(".competition_tags_container .container_content").append("<h4 class='empty-search'>На жаль за результатами Вашого запиту нічого не знайдено. Перевірте будь ласка правильність вводу.</h4>");
        }else{
          $(".competition_tags_container .container_content .empty-search").remove();
        }
        $(".container_content label").click(function(){
            $(this).toggleClass("active");
        });
      });
      $(".close-icon, .darkback, .accept_tags").click(function(){
        let competitionTags = document.querySelectorAll(".competition_tags_container .container_content label.active");
        let competitionTagsNames = [], competitionTagsId = [];
        for(let i = 0; i < competitionTags.length; i++){
          competitionTagsNames.push(competitionTags[i].innerText);
          competitionTagsId.push(competitionTags[i].getAttribute("value"));
        }
        let resultTagsString = competitionTagsNames.join(", ");
        let resultTagsIdString = competitionTagsId.join(", ");
        if(resultTagsString == "") resultTagsString = "Не обрано";
        target.children("label").first().text(resultTagsString);
        target.attr("selected_value", resultTagsIdString);
        $(".competition_tags_container, .darkback").fadeOut(200);
        $("body").attr("style", "");
      });
    });
    $(".container_content label").click(function(){
        $(this).toggleClass("active");
    });
    $(".competitions_list_parameters span").click(function(){
      if(!$(this).parent().parent().children("ul").is(":visible"))
        $(this).parent().parent().children("ul").slideDown(200);
      else
        $(this).parent().parent().children("ul").slideUp(200);
    });
    $(document).mouseup(function(e){
  		let competitionsParametersList = $(".competitions_list_parameters ul");
      let selectorOptionsList = $(".selector-options");
      let mobileMenu = $(".mobile_menu");
  		if(!competitionsParametersList.is(e.target)){
  			competitionsParametersList.slideUp(200);
  		}
      if(!mobileMenu.is(e.target)){
        mobileMenu.hide();
      }
      if(!selectorOptionsList.is(e.target) && !$(".selector").is(e.target)){
        selectorOptionsList.hide();
        $(".selector").removeClass("active");
      }
  	});
    if($("header").hasClass("full-header") || $(".main_navigation").hasClass("transparent")){
      $(window).scroll(function(){
        let offsetTop = document.body.scrollTop;
        if(offsetTop > 0 || document.documentElement.scrollTop > 0){
          $(".main_navigation").css({
            "background-color": "white",
            "transition": "0.2s ease-out",
            "box-shadow": "0 0 10px -3px silver"
          });
          $(".main_navigation a").css("color", "black");
          $(".burger_menu_icon img").attr("src", "images/burger_menu_black.png");
        }else{
          $(".main_navigation").css({
            "background-color": "transparent",
            "transition": "0.2s ease-out",
            "box-shadow": "none"
          });
          $(".main_navigation a").css("color", "white");
          $(".burger_menu_icon img").attr("src", "images/burger_menu_white.png");
        }

      });
    }else{
      if(!$(".main_navigation").hasClass("transparent")){
        $(".main_navigation").css({
          "background-color": "white",
          "transition": "0.2s ease-out",
          "box-shadow": "0 0 10px -3px silver"
        });
        $(".main_navigation a").css("color", "black");
        $(".burger_menu_icon img").attr("src", "images/burger_menu_black.png");
        $("header").css("height", $(".main_navigation").height() + "px");
        $("authorization_container").css("height", "calc(100vh - " + $(".main_navigation").height() + "px)");
      }
    }
    $(".filters_section, .sorting_section").mouseenter(function(){
      $("img", this).css("transform", "rotate(-15deg)");
    });
    $(".filters_section, .sorting_section").mouseleave(function(){
      $("img", this).css("transform", "rotate(0deg)");
    });
    if($(".competitions_list_parameters").is(":visible")){
      $(".main_page_container").css("margin-top", $(".competitions_list_parameters").height() + 30 + "px");
      if($(".filters_container").is(":visible"))
        $(".filters_container").css({
          "top": $(".competitions_list_parameters").height() + $(".competitions_list_parameters").position().top + 14 + "px",
          "max-height": $(window).height() - $(".competitions_list_parameters").height() - $(".competitions_list_parameters").position().top - 14 + "px"
        });
    }
    $(window).resize(function(){
      if($(".competitions_list_parameters").is(":visible")){
        $(".main_page_container").css("margin-top", $(".competitions_list_parameters").height() + 30 + "px");
        $(".filters_container").css({
          "top": $(".competitions_list_parameters").height() + $(".competitions_list_parameters").position().top + 14 + "px",
          "max-height": $(window).height() - $(".competitions_list_parameters").height() - $(".competitions_list_parameters").position().top - 14 + "px"
        });
      }
    });
    $(window).scroll(function(){
      let offsetTop = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
      let footerTop = $("footer").offset().top;
      let offsetBottom = footerTop - $(".personal_office_navbar").height() - 61;
      let newOffsetTop = Math.min(offsetTop, offsetBottom);
      if(offsetTop >= 0 && offsetTop < offsetBottom) $(".personal_office_navbar").css({"position": "fixed", "top": "61px"});
      else $(".personal_office_navbar").css({"position": "relative", "top": newOffsetTop + "px"});
    });
    $(".personal_office_navbar").mouseenter(function(){
      if(!$(".personal_office_navbar .hide").is(":visible")){
        if($(window).width() > 768){
          $(".personal_office_navbar .hide").css({display: "inline-flex", right: "0", opacity: "0"}).animate({right: "-25px", opacity: "1"}, 100);
        }
      }
    });
    $(".personal_office_navbar").mouseleave(function(){
      if($(".personal_office_navbar .hide").is(":visible")){
        $(".personal_office_navbar .hide").css({display: "inline-flex", right: "-25px", opacity: "1"});
        $(".personal_office_navbar .hide").animate({right: "0", opacity: "0"}, 100);
        setTimeout(function(){ $(".personal_office_navbar .hide").css("display", "none"); }, 100);
      }
    });
    $(".personal_office_navbar .hide").click(function(){
      if($(".personal_office_navbar .hide").text() != "❯"){
        $(".personal_office_navbar .hide").text("❯");
        $(".personal_office_sidebar, .personal_office_sidebar_container").css("min-width", "70px");
        $(".personal_office_navbar").css("width", "70px");
        $(".personal_office_navbar a span").hide(200);
      }else{
        $(".personal_office_navbar .hide").text("❮");
        $(".personal_office_sidebar, .personal_office_sidebar_container").css("min-width", "320px");
        $(".personal_office_navbar").css("width", "320px");
        $(".personal_office_navbar a span").show(200);
      }
    });
    $(".back").click(function(){
      let link = document.referrer;
      if(link == "" || link == location.href) link = "/";
      location.href = link;
    });
    $(".per-off-con, .tp_competitions, .cr_competitions").hide();
    $("." + $.cookie("selectedProfileContainer")).show();
    $("." + $.cookie("selectedCompetitionsContainer")).show();
    $(".personal_office_navbar a").removeClass("selected");
    $(".competitions_container_navbar a").removeClass("selected");
    $(".personal_office_navbar a[container-name=" + $.cookie("selectedProfileContainer") + "]").addClass("selected");
    $(".competitions_container_navbar a[container-name=" + $.cookie("selectedCompetitionsContainer") + "]").addClass("selected");
    $(".text_inputs").on("input", function(){
      if($(this).val().length === 0)
        $(this).css("border-bottom", "1px solid silver");
      else
        $(this).css("border-bottom", "1px solid black");
    });
    $(".project_title a, .header_title a").click(function(){
      $("html, body").animate({
        scrollTop: $("header").height() - $(".main_navigation").height()
      }, 500)
    });
    $(".auth .header h2").click(function(){
      $(".auth .header h2").removeClass("active");
      if($(this).hasClass("sign_in")){
        $(".registration_list").fadeOut(200);
        $(".restoring_list").fadeOut(200);
        $(".authorization_list").delay(200).fadeIn(200);
      }else if($(this).hasClass("sign_up")){
        $(".authorization_list").fadeOut(200);
        $(".restoring_list").fadeOut(200);
        $(".registration_list").delay(200).fadeIn(200);
      }
      $(this).addClass("active");
    });
    $(".forget_password").click(function(){
      $(".registration_list").fadeOut(200);
      $(".authorization_list").fadeOut(200);
      $(".restoring_list").delay(200).fadeIn(200);
    });
    $("a[href='personal_office.php']").click(function(){
      $.cookie("selectedProfileContainer", "personal_information_container");
      $.cookie("selectedCompetitionsContainer", "tp_competitions");
    })
    $(".authorization_submit").click(function(){
      $(".auth").find("span.auth_error").remove();
      let login = $("#auth_login").val();
      let password = $("#auth_password").val();
      let rememberme = $("#remember_me").is(":checked");
      let loginError = "", passwordError = "";
      if(login.length < 5) loginError = "Логін повинен складатися не менш ніж з 5 літер!";
      if(login.trim() === "") loginError = "Вкажіть логін!";
      if(password.length < 8) passwordError = "Пароль повинен складатися не менш ніж з 8 літер!";
      if(password.trim() === "") passwordError = "Вкажіть пароль!";
      if(loginError != "" || passwordError != ""){
        if(loginError != "")
          $("#auth_login").after("<span class='auth_error'>" + loginError + "</span>");
        if(passwordError != "")
          $("#auth_password").parent().after("<span class='auth_error'>" + passwordError + "</span>");
        return false;
      }else{
        $.ajax({
          type: "POST",
          url: "/includes/authorization.php",
          data: {
            login: login,
            password: password,
            rememberme: rememberme
          },
          cache: false,
          success: function(data){
            if(data == 'incorrectLogin')
              $("#auth_login").after("<span class='auth_error'>Невірний логін!</span>");
            else if(data == 'incorrectPassword')
              $("#auth_password").parent().after("<span class='auth_error'>Невірний пароль!</span>");
            else if(data == "success"){
              location.href = "index.php";
            }else{
              $("#auth_password").parent().after("<span class='auth_error'>Ваш аккаунт було заблоковано до " + data.split('#')[1] + "</span>");
            }
          }
        });
        return false;
      }
    });
    $(".registration_submit").click(function(){
      $(".auth span.auth_error").remove();
      let login = $("#reg_login").val();
      let password = $("#reg_password").val();
      let email = $("#reg_email").val();
      let role = $("#reg_role").attr("selected_value");
      let loginError = "", passwordError = "", emailError = "";
      if(login.length < 5) loginError = "Логін повинен складатися не менш ніж з 5 літер!";
      if(login.trim() === "") loginError = "Вкажіть логін!";
      if(password.length < 8) passwordError = "Пароль повинен складатися не менш ніж з 8 літер!";
      if(password.trim() === "") passwordError = "Вкажіть пароль!";
      if(email.trim() === "") emailError = "Вкажіть електронну пошту!";
      if(loginError != "" || passwordError != "" || emailError != ""){
        if(loginError != "")
          $("#reg_login").after("<span class='auth_error'>" + loginError + "</span>");
        if(passwordError != "")
          $("#reg_password").parent().after("<span class='auth_error'>" + passwordError + "</span>");
        if(emailError != "")
          $("#reg_email").after("<span class='auth_error'>" + emailError + "</span>");
        return false;
      }else{
        $.ajax({
          type: "POST",
          url: "/includes/registration.php",
          data: {
            login: login,
            password: password,
            email: email,
            role: role
          },
          cache: false,
          success: function(data){
            if(data == "loginExistence"){
              $("#reg_login").after("<span class='auth_error'>Користувач з таким логіном вже існує!</span>");
            }else{
              location.reload();
            }
          }
        });
        return false;
      }
    });
    $(".password_container img").click(function(){
      let inputId = $(this).attr("for");
      let inputStatus = $("#" + inputId).attr("type");
      if(inputStatus == "text"){
        $("#" + inputId).attr("type", "password");
        $(this).css("background-color", "white");
      }else{
        $("#" + inputId).attr("type", "text");
        $(this).css("background-color", "#E6E6E6");
      }
    });
    $(".restoring_submit").click(function(){
      $(".auth").find("span.auth_error").remove();
      let email = $("#restoring_email").val();
      let emailError = "";
      if(email.trim() === "") emailError = "Вкажіть електронну пошту!";
      if(emailError != ""){
        $("#restoring_email").after("<span class='auth_error'>" + emailError + "</span>");
        return false;
      }
    });
    $(".logout").click(function(){
      $.ajax({
        type: "POST",
        url: "/includes/logout.php",
        cache: false,
        success: function(data){}
      });
    });
    $(".personal_office_navbar a").click(function(e){
      $(".personal_office_navbar a").removeClass("selected");
      $(this).addClass("selected");
    });
    $(".save_profile").click(function(){
      let data = new Object();
      let profileContainer = $(".personal_information_container");
      data['email'] = profileContainer.find("input[name='email']").val();
      data['last_name'] = profileContainer.find("input[name='last_name']").val();
      data['first_name'] = profileContainer.find("input[name='first_name']").val();
      data['middle_name'] = profileContainer.find("input[name='middle_name']").val();
      if(profileContainer.find("input[name='phone']").val() != "")
        data['phone'] = "+" + profileContainer.find("input[name='phone']").attr("dial-code") + profileContainer.find("input[name='phone']").val().replace(/\s/g, '');
      else
        data['phone'] = "";
      data['sex'] = profileContainer.find("span[name='sex']").attr("selected_value");
      data['country'] = profileContainer.find("span[name='country']").attr("selected_value");
      data['birth_date'] = profileContainer.find("input[name='birth_date']").val();
      data['city'] = profileContainer.find("input[name='city']").val();
      data['address'] = profileContainer.find("input[name='address']").val();
      data['organization_name'] = profileContainer.find("input[name='organization_name']").val();
      let profileImage = $("#upload_profile_image").prop("files")[0];
      let profileData = JSON.stringify(data);
      let uploadData = new FormData();
      uploadData.append("image", profileImage);
      uploadData.append("uploadData", profileData);
      uploadData.append("action", "updateUserData");
      $.ajax({
        url: "includes/personal_office_handler.php",
        type: "POST",
        data: uploadData,
        contentType: false,
        processData: false,
        cache: false,
        success: function(data) {
          if(data == "success") location.reload();
        }
      });
    });
    $(".personal_office_navbar a").click(function(){
      let containerName = $(this).attr("container-name");
      if(containerName){
        $(".per-off-con").hide();
        $("." + containerName).show();
        $.cookie("selectedProfileContainer", containerName);
      }
    });
    $(".competitions_container_navbar a").click(function(){
      let containerName = $(this).attr("container-name");
      $(".competitions_container_navbar a").removeClass("selected");
      $(this).addClass("selected");
      $(".tp_competitions, .cr_competitions").fadeOut(200);
      $("." + containerName).delay(200).fadeIn(200);
      $.cookie("selectedCompetitionsContainer", containerName);
    });
    $(".competition_item_links a").click(function(){
      let className = $(this).attr("action");
      let competitionId = $(this).attr("competition-id");
      if(className == 'remove'){
        let title = "Видалення змагання";
        let message = "Після видалення змагання усі дані буде втрачено. Відновлення змагання буде неможливим. Переконайтеся, що Ви маєте вагомі причини зробити такий відповідальний крок. Ви дійсно хочете видалити змагання?";
        showNotification(title, message);
        $(".site_notification .button").click(function(){

          $.ajax({
            type: "POST",
            url: "includes/personal_office_handler.php",
            data: {
              competitionId: competitionId,
              requestName: "removeCompetition"
            },
            success: function(data){
              if(data == "deleted") location.reload();
            }
          });
        });
      }
    });
    $('#file-input').focus(function() {
      $('label').addClass('focus');
    }).focusout(function() {
      $('label').removeClass('focus');
    });
    $('#upload-container').on('drag dragstart dragend dragover dragenter dragleave drop', function(){
      return false;
    });
    $('#upload-container').on('dragover dragenter', function() {
      $('#upload-container').addClass('dragover');
    });
    $('#upload-container').on('dragleave', function(e) {
      $('#upload-container').removeClass('dragover');
    });
    $('#upload-container').on('dragleave', function(e) {
      let dx = e.pageX - $('#upload-container').offset().left;
      let dy = e.pageY - $('#upload-container').offset().top;
      if((dx < 0) || (dx > $('#upload-container').width()) || (dy < 0) || (dy > $('#upload-container').height())){
          $('#upload-container').removeClass('dragover');
      }
    });
    $('#upload-container').on('drop', function(e) {
      $('#upload-container').removeClass('dragover');
      let files = e.originalEvent.dataTransfer.files;
      if($(".file_names").html() != "Ім'я файлу: " + files[files.length - 1]["name"])
        $('.file_names').empty().append("Ім'я файлу: " + files[files.length - 1]["name"]);
    });
    $('#file-input').change(function() {
      let files = this.files;
      if($(".file_names").html() != "Ім'я файлу: " + files[files.length - 1]["name"])
        $('.file_names').empty().append("Ім'я файлу: " + files[files.length - 1]["name"]);
    });
    $(".add_competition, .save_competition").click(function(){
      let actionName = "createCompetition";
      let competitionId = 0;
      if($(this).hasClass("save_competition")) actionName = "saveCompetition", competitionId = getURLparams().get('competition-id');
      let maxFileSize = 5242880;
      let data = new Object();
      let errors = [];
      let competitionImage = $("#file-input").prop("files")[0];
      if(competitionImage){
        if(!((competitionImage.size <= maxFileSize) && ((competitionImage.type == 'image/png') || (competitionImage.type == 'image/jpeg')))){
          errors.push("Логотип змагання перевищує допустимий розмір або не є зображенням!");
        }
      }
      let competitionName = $(".competition_action_container input[name='competition_name']").val();
      let competitionOrganizer = $(".competition_action_container input[name='competition_organizer']").val();
      let competitionAgeRange = $(".competition_action_container input[name='min_age']").val() + "-" + $(".competition_action_container input[name='max_age']").val();
      let competitionParticipantsSex = $(".competition_action_container span[name='competition_participants_sex']").attr("selected_value");
      let competitionCertificates = "st::" + $(".competition_action_container .change_certificates").attr("status") + "&[" + $(".competition_action_container input[name='competition_certificates']").val() + "]";
      let competitionPrizes = $(".competition_action_container input[name='competition_prizes']").val();
      let competitionCountries = $(".competition_action_container span[name='competition_countries']").attr("selected_value");
      let competitionTags = $(".competition_action_container span[name='competition_tags']").attr("selected_value");
      let competitionDescription = CKEDITOR.instances.editor.getData();
      let competitionBegining = $(".competition_action_container input[name='competition_begining']").val();
      let competitionEnding = $(".competition_action_container input[name='competition_ending']").val();
      if(competitionName.trim() === "") errors.push("Вкажіть назву змагання!");
      if(competitionOrganizer.trim() === "") errors.push("Вкажіть організатора змагання!");
      if(competitionDescription.trim() === "") errors.push("Вкажіть опис змагання!");
      if(competitionBegining.trim() === "") errors.push("Вкажіть початок змагання!");
      if(competitionEnding.trim() === "") errors.push("Вкажіть кінець змагання!");
      if(competitionAgeRange.trim() === "") errors.push("Вкажіть вік учасників змагання!");
      if(competitionParticipantsSex.trim() === "") errors.push("Вкажіть стать учасників змагання!");
      if(competitionCountries.trim() === "") errors.push("Вкажіть країни, які можуть брати участь в змаганні!");
      if(competitionTags.trim() === "") errors.push("Вкажіть теги змагання!");
      data['competitionName'] = competitionName;
      data['competitionOrganizer'] = competitionOrganizer;
      data['competitionDescription'] = competitionDescription;
      data['competitionBegining'] = competitionBegining;
      data['competitionEnding'] = competitionEnding;
      data['competition_age_range'] = competitionAgeRange;
      data['competition_certificates'] = competitionCertificates;
      data['competition_participants_sex'] = competitionParticipantsSex;
      data['competition_prizes'] = competitionPrizes;
      data['competition_countries'] = competitionCountries;
      data['competition_tags'] = competitionTags;
      let competitionJudgesList = [], competitionJudges, competitionsCriteriaList = [], competitionCriteria, exLogins = [];
      let judgesList = document.querySelectorAll(".judges > div input.judges_password");
      let exLoginList = document.querySelectorAll(".judges > div .ex-login");
      let criteriaList = document.querySelectorAll(".criteria > div");
      for(let i = 0; i < exLoginList.length; i++){
        let exLogin = exLoginList[i].getAttribute("user-id");
        exLogins.push(exLogin);
      }
      for(let i = 0; i < judgesList.length; i++){
        let userPassword = judgesList[i].value;
        competitionJudgesList.push(userPassword);
        if(userPassword.trim() === "") errors.push("Пропущене значення паролю для члена журі");
      }
      for(let i = 0; i < criteriaList.length; i++){
        let criterionName = criteriaList[i].querySelectorAll(".criterion_name")[0].value;
        let criterionMinValue = criteriaList[i].querySelectorAll(".criterion_min_value")[0].value;
        let criterionMaxValue = criteriaList[i].querySelectorAll(".criterion_max_value")[0].value;
        if(criterionName.trim() === "" || criterionMinValue.trim() === "" || criterionMaxValue.trim() == "") errors.push("Наявні пусті поля для критеріїв оцінювання!");
        competitionsCriteriaList.push(new Object());
        competitionsCriteriaList[i]["criterionName"] = criterionName;
        competitionsCriteriaList[i]["criterionMinValue"] = criterionMinValue;
        competitionsCriteriaList[i]["criterionMaxValue"] = criterionMaxValue;
      }
      let exsistedFileNames = [];
      let exsistedFiles = document.querySelectorAll(".competition_work_files .no-file label");
      for(let i = 0; i < exsistedFiles.length; i++){
        let file = exsistedFiles[i].innerText;
        exsistedFileNames.push(competitionId + "&=&" + file);
      }
      exsistedFileNames = JSON.stringify(exsistedFileNames);
      data['exsisted_files'] = exsistedFileNames;
      competitionJudges = JSON.stringify(competitionJudgesList);
      exLogins = JSON.stringify(exLogins);
      data['ex_logins'] = exLogins;
      data['competitionJudges'] = competitionJudges;
      data['competitionCriteria'] = competitionsCriteriaList;
      data['competitionId'] = competitionId;
      if(errors.length == 0) createCompetition(competitionImage, JSON.stringify(data), actionName);
    });
    function createCompetition(competitionImage, competitionData, actionName){
      let data = new FormData();
      let competitionId = 0;
      data.append('competitionImage', competitionImage);
      data.append("actionName", actionName);
      data.append("competitionData", competitionData);
      let files = document.querySelectorAll(".competition_work_files .file-input");
      for(let i = 0; i < files.length; i++){
        let file = files[i].files[0];
        data.append("files[]", file);
      }
      $.ajax({
        url: "includes/personal_office_handler.php",
        type: "POST",
        data: data,
        contentType: false,
        processData: false,
        cache: false,
        success: function(data) {
          location.reload();
        }
      });
    }
    let findJudgeInput = document.getElementById("find-judge");
    let judgesListNames = document.querySelectorAll(".all_judges_list_item h4");
    let judgesList = document.querySelectorAll(".all_judges_list > .all_judges_list_item");
    $(".add_judges").click(function(){
      $(".judges").show();
      $(".judges").append("<div class='judges_list_item'><p type='text' class='judge_login'>Логін (автомaтично)</p><input type='text' class='judges_password' placeholder='Пароль'><img src='images/bin.png' class='remove'></div>")
    });
    $("body").delegate(".all_judges_list_item", "click", function(){
      if($('input', this).prop('checked') == false){
        $('input', this).prop('checked', true);
        let selectedJudgesList = [];
        for(let i = 0; i < judgesList.length; i++){
          let item = judgesList[i].cloneNode(true);
          if(judgesList[i].getElementsByTagName("input")[0].checked == true)
            selectedJudgesList.push(item);
        }
        $(".selected_judges_list").empty();
        for(let i = 0; i < selectedJudgesList.length; i++){
          $(".selected_judges_list").append(selectedJudgesList[i]);
        }
      }else{
        $('input', this).prop('checked', false);
        $(".all_judges_list > div[user-id=" + $(this).attr("user-id") + "] input").prop("checked", false);
        if($(this).parent().attr("class") == "selected_judges_list")
          $(this).remove();
        else
          $(".selected_judges_list > div[user-id=" + $(this).attr("user-id") + "]").remove();
      }
    });
    $("#upload-container").css("height", $("#upload-container").width() + "px");
    if(findJudgeInput){
      findJudgeInput.addEventListener("input", function(){
        let inputValue = $(this).val().toLowerCase();
        let newJudgesList = [];
        for(let i = 0; i < judgesList.length; i++){
          let item = judgesList[i];
          if(judgesListNames[i].textContent.toLowerCase().search(inputValue) != -1)
            newJudgesList.push(item);
        }
        $(".all_judges_list").empty();
        for(let i = 0; i < newJudgesList.length; i++){
          $(".all_judges_list").append(newJudgesList[i]);
        }
        if($(".all_judges_list").children().length == 0){
          $(".all_judges_list").append("<h4 class='empty-search'>На жаль за результатами Вашого запиту нічого не знайдено. Перевірте будь ласка правильність вводу.</h4>");
        }
        if($(".selected_judges_list").children().length == 0){
          $(".selected_judges_list").append("<h4 class='empty-search'>Жодного члена журі не вибрано.</h4>");
        }
      });
    }
    if(getURLparams().get("action") == "edit-competition"){
      $(".criteria, .judges").show();
    }
    $(".add_criteria").click(function(){
      if($(".criteria").find("h4")) $(".criteria h4").remove();
      $(".criteria").append("<div class='criterion'><input type='text' placeholder='Назва критерію' class='criterion_name'><input type='number' placeholder='Мін. бал' step='0.01' class='criterion_min_value'><input type='number' placeholder='Макс. бал' step='0.01' class='criterion_max_value'><img src='images/bin.png' class='remove_criteria' title='Вилучити зі списку'></div>");
      $(".criteria").show().css("disable", "flex");
      if($(".criteria").children().length == 0){
        $(".criteria").hide();
      }
    });
    $("body").delegate(".remove_judge", "click", function(){
      $(this).parent().remove();
      if($(".judges").children().length == 0){
        $(".judges").hide();
      }
    });
    $("body").delegate(".remove_criteria", "click", function(){
      $(this).parent().remove();
      if($(".criteria").children().length == 0){
        $(".criteria").hide();
      }
    });
    $(".competition_block_item").click(function(){
      let competitionId = $(this).attr("competition-id");
      location.href = "view_competition.php?competition-id=" + competitionId;
    });
    $(".tp_link").click(function(){
      let competitionId = $(this).attr("competition-id");
      let target = $(this);
      let logData = new Object();
      logData['action'] = "Реєстрація на змагання";
      $.ajax({
        url: "includes/personal_office_handler.php",
        type: "POST",
        data: {
          action: "registerCompetition",
          competitionId: competitionId
        },
        cache: false,
        success: function(data) {
          $(".competition_registration_message").show();
          if(data == "success"){
            target.closest(".competition_presentation").fadeOut(500);
            $(".competition_registration_success").delay(500);
            $(".competition_registration_success").fadeIn(500);
            logData['actionResult'] = true;
          }else{
            target.closest(".competition_presentation").fadeOut(500);
            $(".competition_registration_error").delay(500);
            $(".competition_registration_error").fadeIn(500);
            logData['actionResult'] = false;
          }
          createLog(logData);
        }
      });
    });
    $(".open_participants_table").click(function(){
      let competitionId = $(this).attr("competition-id");
      location.href = "assessment_table.php?competition-id=" + competitionId;
    });
    $(".user_value_cell").mouseenter(function(){
      let target = $(this);
      let posX = target.offset().left;
      let posY = target.offset().top;
      let commentaryMessage = $(this).attr("commentary");
      $(".hint img").attr('disable', 'true');
      if(commentaryMessage == ""){
        commentaryMessage = "<p style='color: red;' com='none'>Коментар відсутній</p>";
      }
      $(".hint > .commentary_text").html("<div>" + commentaryMessage + "</div>");
      $(".hint").show();
      $(".hint").css({
        left: posX - (155 - target.width() / 2) + "px",
        top: posY + 55 + "px"
      });
      $(".hint").stop().animate({
        opacity: 1
      }, 300);
    });
    $(".at_inputs").mouseenter(function(event){
      let target = $(this);
      let inputId = target.attr("id");
      let posX = target.offset().left;
      let posY = target.offset().top;
      let commentaryMessage = $(this).attr("commentary");
      if(!$(".at_inputs").is(":focus") && !$(".hint textarea").is(":focus")){
        $(".hint").attr("input_id", inputId);
        if(commentaryMessage == ""){
          commentaryMessage = "<p style='color: red;' com='none'>Коментар відсутній</p>";
          $(".hint img[action='remove']").attr('disable', 'true');
        }else $(".hint img[action='remove']").attr('disable', 'false');
        $(".hint > .commentary_text").html("<div>" + commentaryMessage + "</div>");
        $(".hint").show();
        $(".hint").css({
          left: posX - (160 - target.width() / 2) + "px",
          top: posY + 65 + "px"
        });
        $(".hint").stop().animate({
          top: "-=10px",
          opacity: 1
        }, 300);
      }
    });
    $(".at_inputs").focus(function(event){
      let target = $(this);
      let inputId = target.attr("id");
      let posX = target.offset().left;
      let posY = target.offset().top;
      let commentaryMessage = $(this).attr("commentary");
      $(".hint").attr("input_id", inputId);
      if(commentaryMessage == ""){
        commentaryMessage = "<p style='color: red;' com='none'>Коментар відсутній</p>";
        $(".hint img[action='remove']").attr('disable', 'true');
      }else $(".hint img[action='remove']").attr('disable', 'false');
      $(".hint > .commentary_text").html("<div>" + commentaryMessage + "</div>");
      $(".hint").show();
      $(".hint").css({
        left: posX - (160 - target.width() / 2) + "px",
        top: posY + 55 + "px"
      });
      $(".hint").stop().animate({
        opacity: 1
      }, 300);
    });
    $(".at_inputs").mouseleave(function(){
      if(!$(".at_inputs").is(":focus") && !$(".hint").is(":hover") && !$(".hint textarea").is(":focus"))
        $(".hint").css("opacity", "0").hide();
    });
    $(".at_inputs").blur(function(){
      if(!$(".hint").is(":hover") && !$(".hint textarea").is(":focus"))
        $(".hint").css("opacity", "0").hide();
    });
    $(window).mousedown(function(){
      if(!$(".hint").is(":hover") && !$(".at_inputs").is(":focus")) $(".hint").hide();
    });
    $(".at_inputs").on("input", function(){
      let maxVal = $(this).attr("max");
      let minVal = $(this).attr("min");
      let currVal = $(this).val();
      if(currVal == "") currVal = 0, $(this).val(0);
      else{
        currVal = Math.max(minVal, currVal);
        let newVal = Math.max(minVal, Math.min(currVal, maxVal));
        $(this).val(newVal);
      }
    });
    $(".at_inputs").focus(function(){
      let inputId = $(this).attr("id");
      let inputParams = inputId.split('--');
      let userId = inputParams[0];
      let criterionId = inputParams[1];
      let competitionId = inputParams[2];
      let criteriaNumber = inputParams[3];
      let inputValue = Math.max(0, $(this).val());
      $(this).unbind("focusout");
      $(this).unbind("keydown");
      $(this).focusout(function(){
        if(inputValue != $(this).val()){
          let summary = $(this).parent().parent().find("td:nth-last-child(3)").html();
          let newSummary = (parseFloat(summary) - parseFloat(inputValue) + parseFloat(Math.max(0, $(this).val()))).toFixed(5).toString();
          let cutIndex = 0;
          for(let i = 0; i < newSummary.length; i++){
            if(newSummary[i] == '.') cutIndex = i;
            if(cutIndex > 0 && newSummary[i] == '0'){
              cutIndex = i;
              break;
            }
          }
          if(newSummary[cutIndex] == '0')
            newSummary = newSummary.substr(0, cutIndex);
          else
            newSummary = newSummary.substr(0, newSummary.length);
          if(newSummary[newSummary.length - 1] == '.') newSummary = newSummary.substr(0, newSummary.length - 1);
          $(this).parent().parent().find("td:nth-last-child(3)").html(newSummary);
          inputValue = Math.max(0, $(this).val());
          $.ajax({
            url: "includes/personal_office_handler.php",
            type: "POST",
            data: {
              action: "updateEvaluationTable",
              userId: userId,
              criterionId: criterionId,
              criteriaNumber: criteriaNumber,
              competitionId: competitionId,
              value: inputValue
            },
            cache: false,
            success: function(data){
              if(data == "success"){
                let className = "sm_success";
                let title = "Інформацію оновлено!";
                let text = "Таблицю результатів учасників було оновлено. Для правильного відображення положення учасників у таблиці <a class='reload-page'>перезавантажте сторінку.</a>";
                addMessage(className, title, text);
              }
            }
          });
        }
      });
      $(this).keydown(function(event){
        if(event.which == 13){
          if(inputValue != $(this).val()){
            let summary = $(this).parent().parent().find("td:nth-last-child(3)").html();
            let newSummary = (parseFloat(summary) - parseFloat(inputValue) + parseFloat(Math.max(0, $(this).val()))).toFixed(5).toString();
            let cutIndex = 0;
            for(let i = 0; i < newSummary.length; i++){
              if(newSummary[i] == '.') cutIndex = i;
              if(cutIndex > 0 && newSummary[i] == '0'){
                cutIndex = i;
                break;
              }
            }
            if(newSummary[cutIndex] == '0')
              newSummary = newSummary.substr(0, cutIndex);
            else
              newSummary = newSummary.substr(0, newSummary.length);
            if(newSummary[newSummary.length - 1] == '.') newSummary = newSummary.substr(0, newSummary.length - 1);
            $(this).parent().parent().find("td:nth-last-child(3)").html(newSummary);
            inputValue = Math.max(0, $(this).val());
            $.ajax({
              url: "includes/personal_office_handler.php",
              type: "POST",
              data: {
                action: "updateEvaluationTable",
                userId: userId,
                criterionId: criterionId,
                criteriaNumber: criteriaNumber,
                competitionId: competitionId,
                value: inputValue
              },
              cache: false,
              success: function(data){
                if(data == "success"){
                  let className = "sm_success";
                  let title = "Інформацію оновлено!";
                  let text = "Таблицю результатів учасників було оновлено. Для правильного відображення положення учасників у таблиці <a class='reload-page'>перезавантажте сторінку.</a>";
                  addMessage(className, title, text);
                }
              }
            });
          }
        }
      });
    });
    $(".add_review").click(function(){
      $(".darkback").fadeIn(200);
      $(".add_review_form").fadeIn(200).css("display", "flex");
      $("body").css("overflow", "hidden");
      $(".darkback, .close-icon").click(function(){
        $(".add_review_form, .darkback").fadeOut(200);
        $("body").attr("style", "");
      });
      $(".send_review").click(function(){
        let logData = new Object();
        let author = $(".add_review_form input[name='author']").val();
        let text = $(".add_review_form textarea");
        logData['action'] = "Надсилання відгуку";
        logData['review_text'] = text[0].value;
        if(author != "" && text[0].value != ""){
          $.ajax({
            url: "includes/rating_handler.php",
            type: "POST",
            data: {
              actionName: "add_review",
              author: author,
              text: text[0].value
            },
            cache: false,
            success: function(data){
              if(data == "success"){
                logData['actionResult'] = true;
                createLog(logData);
                location.reload();
              }else{
                logData['actionResult'] = false;
                createLog(logData);
              }
            }
          });
        }
      });
    });
    if($(".personal_logs_container").is(":visible")){
      $(".personal_logs_container .logs_list .page_content").css({
        "max-height": $(window).innerHeight() - $(".logs_list .page_content").position().top - 61 + "px",
        "height": $(window).innerHeight() - $(".logs_list .page_content").position().top -61 + "px"
      });
    }
    $(".add_file_input").click(function(){
      let fileContainer = $("." + $(this).attr("file-container"));
      fileContainer.append("<div class='file-container-item'><div><img src='images/upload-image.png'><input id='work-file" + fileContainer.children().length + "' class='file-input' type='file' accept='video/mp4, video/x-m4v, video/*, image/*, audio/*, application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf'><label for='work-file" + fileContainer.children().length + "'>Завантажити файл</label></div><span class='remove'></span></div>");
      $(".file-container-item span.remove").click(function(){
        $(this).parent().remove();
      });
      $(".file-input").change(function(){
        let fileId = $(this).attr("id");
        let file = $(this)[0].files[0].name;
        let fileName = file.split('.').slice(0, -1).join('.');
        fileExtension = /^.+\.([^.]+)$/.exec(file)[1];
        $("img", $(this).parent()).attr("src", "images/extensions/" + fileExtension + ".png").css({width: "40px", height: "40px"});
        $(".competition_work_files label[for='" + fileId + "']").text(file).css("font-size", "17px");
      });
    });
    $(".file-container-item span.remove").click(function(){
      $(this).parent().remove();
    });
    $(".send_work").click(function(){
      let data = new FormData(), exsistedFileNames = [];
      let files = $(".competition_work .file-container-item"), filesData = [];
      let commentary = $(".competition_work textarea").val();
      for(let i = 0; i < files.length; i++){
        if(files[i].classList.contains("no-file")){
          let currentFileName = files[i].getElementsByTagName('label')[0].innerText;
          filesData.push(currentFileName);
          exsistedFileNames.push(currentFileName);
        }else{
          let currentFile = files[i].getElementsByClassName('file-input')[0];
          filesData.push(currentFile.files[0].name);
          data.append("files[]", currentFile.files[0]);
        }
      }
      let logData = new Object();
      logData['action'] = "Надсилання роботи учасника";
      logData['competition_id'] = getURLparams().get("competition-id");
      logData['files'] = JSON.stringify(filesData);
      data.append("exsisted_file_names", JSON.stringify(exsistedFileNames));
      data.append("action", "sendWork");
      data.append("competition_id", getURLparams().get("competition-id"));
      data.append("commentary", commentary);
      $.ajax({
        url: "includes/personal_office_handler.php",
        type: "POST",
        data: data,
        contentType: false,
        processData: false,
        cache: false,
        success: function(data){
          if(data == "success"){
            logData['actionResult'] = true;
            createLog(logData);
            location.reload();
          }else{
            let className = "sm_error";
            let title = "Інформація про змагання";
            let text = "Виникла помилка при надсиланні роботи! Спробуйте ще раз";
            logData['actionResult'] = false;
            createLog(logData);
            addMessage(className, title, text);
          }
        }
      });
    });
    $(".logs_list .log").click(function(){
      if(!$(".additional_data", this).is(":visible")){
        $(".logs_list .log .additional_data").hide();
        $(".additional_data", this).show();
      }else{
        $(".additional_data", this).hide();
      }
    });
    $(".refuse_competition").click(function(){
      const urlParams = getURLparams();
      const competitionId = urlParams.get('competition-id');
      let title = "Відмовитися від участі?";
      let text = "Шкода, що Ви покидаєте змагання. Після відмови, Ваші досягнення будуть втрачені. Ви дійсно хочете відмовитися від участі у цьому змаганні?";
      showNotification(title, text);
      $(".site_notification .button").click(function(){
        let logData = new Object();
        logData['action'] = "Відмова від участі у змаганні";
        $.ajax({
          url: "includes/personal_office_handler.php",
          type: "POST",
          data: {
            action: "refuseCompetition",
            competitionId: competitionId
          },
          cache: false,
          success: function(data){
            if(data == "success"){
              logData['actionResult'] = true;
              createLog(logData);
              location.reload();
            }else{
              logData['actionResult'] = false;
              createLog(logData);
            }
          }
        });
      });
    });
    $(".hint_buttons > img").click(function(){
      const urlParams = getURLparams();
      let action = $(this).attr("action");
      let inputId = $(".hint").attr("input_id");
      let inputParameters = inputId.split("--");
      let userId = inputParameters[0], criterionId = inputParameters[1];
      let competitionId = urlParams.get("competition-id");
      if(action == "edit"){
        $(".hint > .commentary_text > div p[com='none']").remove();
        let commentaryText = $(".hint > .commentary_text > div").text();
        $(".hint > .commentary_text").html("<div><textarea>" + commentaryText + "</textarea></div><a class='button' style='font-size: 14px;'>Зберегти зміни</a>");
        $(".hint a.button").click(function(){
          let message = $(".hint > .commentary_text > div > textarea").val();
          $.ajax({
            url: "includes/personal_office_handler.php",
            type: "POST",
            data: {
              action: "saveCommentary",
              userId: userId,
              competitionId: competitionId,
              criterionId: criterionId,
              message: message
            },
            cache: false,
            success: function(data){
              if(data == 'success'){
                let className = "sm_success";
                let title = "Додавання кометарю";
                let text = "Коментар успішно додано!";
                addMessage(className, title, text);
                $(".hint > .commentary_text").html("<div>" + message + "</div>");
                $("#" + inputId).attr("commentary", message);
                $(".hint img[action='remove']").attr('disable', 'false');
              }else{
                let className = "sm_error";
                let title = "Додавання кометарю";
                let text = "Виникла помилка. Спробуйте ще раз.";
                addMessage(className, title, text);
              }
             }
          });
        });
      }else if(action == 'remove' && $(this).attr('disable') == 'false'){
        let title = "Видалення коментарю";
        let text = "Уся інформація буде видалена. Ви дійсно хочете видалити коментар?";
        showNotification(title, text);
        $(".site_notification .button").click(function(){
          $.ajax({
            url: "includes/personal_office_handler.php",
            type: "POST",
            data: {
              action: "removeCommentary",
              userId: userId,
              competitionId: competitionId,
              criterionId: criterionId,
            },
            cache: false,
            success: function(data){
              if(data == "success"){
                $("#" + inputId).attr("commentary", "");
                $(".site_notification, .darkback").fadeOut(300);
              }
            }
          });
        });
      }
    });
    $(".competition_rating i").click(function(){
      let selected = $(this).hasClass("selected");
      let buttonId = $(this).hasClass("like");
      let competitionId = getURLparams().get("competition-id");
      let actionName = "";
      if(selected){
        actionName = "remove";
        $(this).removeClass("selected");
      }else{
        actionName = "add";
        $(".competition_rating i").removeClass("selected");
        $(this).addClass("selected");
      }
      if(buttonId == false) buttonId = "dislike";
      else buttonId = "like";
      $.ajax({
        url: "includes/rating_handler.php",
        type: "POST",
        data:{
          actionName: actionName,
          buttonId: buttonId,
          competitionId: competitionId
        },
        cache: false,
        success: function(data){
          location.reload();
        }
      });
    });
    $(".container_navbar a").click(function(){
      let currentContainerName = $(".container_navbar a.selected").attr("container-name");
      let target = $(this);
      let className = target.attr("class");
      if(className != "selected"){
          let containerName = $(this).attr("container-name");
          $("." + currentContainerName).fadeOut(200);
          setTimeout(function(){
            $("." + containerName).fadeIn(200);
            if(containerName == "competition_work") $("." + containerName).css("display", "flex");
          }, 200);
          $(".container_navbar .selected").attr("class", "");
          target.attr("class", "selected");
      }
    });
    $(".countries_container_item").click(function(){
      if(!$(this).hasClass("selected")){
        $(this).addClass("selected");
      }else{
        $(this).removeClass("selected");
      }
    });
    let countriesList = $(".countries_container .countries_container_item");
    $(".selector[name='competition_countries']").click(function(){
      let target = $(this);
      let findCountriesInput = $(".countries_container .search_container input")[0];
      $(".countries_container, .darkback").fadeIn(300);
      $("body").css("overflow", "hidden");
      $(".countries_container").css("display", "flex");
      findCountriesInput.addEventListener("input", function(){
        let inputValue = $(this).val().toLowerCase();
        let newCountriesList = [];
        for(let i = 0; i < countriesList.length; i++){
          let item = countriesList[i];
          if(countriesList[i].textContent.toLowerCase().search(inputValue) != -1)
            newCountriesList.push(item);
        }
        $(".countries_container .countries_container_item").remove();
        for(let i = 0; i < newCountriesList.length; i++){
          $(".countries_container .container_content").append(newCountriesList[i]);
        }
        if($(".countries_container .container_content").children().length == 0){
          $(".countries_container .container_content").append("<h4 class='empty-search'>На жаль за результатами Вашого запиту нічого не знайдено. Перевірте будь ласка правильність вводу.</h4>");
        }else{
          $(".countries_container .container_content .empty-search").remove();
        }
        $(".countries_container_item").click(function(){
          if(!$(this).hasClass("selected")){
            $(this).addClass("selected");
          }else{
            $(this).removeClass("selected");
          }
        });
      });
      $(".darkback, .close-icon, .accept_countries").click(function(){
        let selectedCountries = document.querySelectorAll(".countries_container_item.selected");
        let selectedCountriesNames = new Array(), selectedCountriesCodes = new Array();
        for(let i = 0; i < selectedCountries.length; i++){
          selectedCountriesNames.push(selectedCountries[i].innerText);
          selectedCountriesCodes.push(selectedCountries[i].getAttribute("value"));
        }
        let resultString = selectedCountriesNames.join(', ');
        let resultCodes = selectedCountriesCodes.join(', ');
        if(selectedCountries.length != 0){
          target.children("label").first().text(resultString);
          target.attr("selected_value", resultCodes);
        }else{
          target.children("label").first().text("Усі");
          target.attr("selected_value", "all");
        }
        $(".countries_container, .darkback").fadeOut(300);
        $("body").attr("style", "");
      });
      $(".select_all > lable, .select_all > input").click(function(){
        if(!$(this).parent().find("input").is(":checked"))
          $(".countries_container_item").removeClass("selected");
        else{
          $(".countries_container_item").addClass("selected");
        }
      });
    });
    $(".hide_filters_container").click(function(){
      $(".filters_container").stop();
      $(".filters_container").animate({top: "-100vh"}, 400);
      setTimeout(function(){
        $(".filters_container").hide();
      }, 400);
    });
    $(".filters_section").click(function(){
      $(".filters_container").stop();
      if(!$(".filters_container").is(":visible")){
        $(".filters_container").animate({"top": $(".competitions_list_parameters").height() + $(".competitions_list_parameters").position().top + 14 + "px"}, 400);
        $(".filters_container").css({
          "display": "flex",
          "max-height": $(window).height() - $(".competitions_list_parameters").height() - $(".competitions_list_parameters").position().top - 14 + "px"
        });
      }else{
        $(".filters_container").animate({top: "-100vh"}, 400);
        setTimeout(function(){
          $(".filters_container").hide();
        }, 400);
      }
    });
    $(".filters_container .buttons_container .button").click(function(){
      let competitionStatusCheckboxes = $(".cpf_status .checkbox_container.selected"), competitionStatus = [];
      for(let i = 0; i < competitionStatusCheckboxes.length; i++){
        let checkbox = competitionStatusCheckboxes[i];
        competitionStatus.push(checkbox.getAttribute("value"));
      }
      competitionStatus = competitionStatus.join(',');
      let competitionAgeRangeMin = $(".cpf_age_range input[name='min_age']").val();
      let competitionAgeRangeMax = $(".cpf_age_range input[name='max_age']").val();
      let competitionBegining = $(".cpf_time_range input[name='competition_begining']").val();
      let competitionEnding = $(".cpf_time_range input[name='competition_ending']").val();
      let competitionParticipantSex = $(".cpf_participant_sex .selector").attr("selected_value");
      let competitionCountries = $(".cpf_countries .selector").attr("selected_value");
      competitionCountries = competitionCountries.split(', ').join(',');
      let competitionTags = $(".cpf_tags .selector").attr("selected_value");
      competitionTags = competitionTags.split(', ').join(',');
      let competitionOrganizersCheckboxes = $(".cpf_organizers .checkbox_container.selected"), competitionOrganizers = [];
      for(let i = 0; i < competitionOrganizersCheckboxes.length; i++){
        let checkbox = competitionOrganizersCheckboxes[i];
        competitionOrganizers.push(checkbox.innerText);
      }
      competitionOrganizers = competitionOrganizers.join(',');
      let competitionRatingRangeMin = $(".cpf_rating_range input[name='min_rating']").val();
      let competitionRatingRangeMax = $(".cpf_rating_range input[name='max_rating']").val();
      let filtersData = new Object();
      filtersData['status'] = competitionStatus;
      filtersData['min_age'] = competitionAgeRangeMin;
      filtersData['max_age'] = competitionAgeRangeMax;
      filtersData['begining'] = competitionBegining;
      filtersData['ending'] = competitionEnding;
      filtersData['participant_sex'] = competitionParticipantSex;
      filtersData['countries'] = competitionCountries;
      filtersData['tags'] = competitionTags;
      filtersData['organizers'] = competitionOrganizers;
      filtersData['min_rating'] = competitionRatingRangeMin;
      filtersData['max_rating'] = competitionRatingRangeMax;
      filtersData = JSON.stringify(filtersData);
      let formData = new FormData();
      formData.append("data", filtersData);
      $.ajax({
        url: "/includes/filters_handler.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        success: function(data){
          let joiner = "", joiner2 = "";
          let search = getURLparams().get('q');
          if(search == null) search = "";
          if(data != "" || search != "") joiner = "?";
          if(search != "") search = 'q=' + search;
          if(search != "" && data != "") joiner2 = "&";
          location.href = "competitions.php" + joiner + search + joiner2 + data;
        }
      });
    });
    $(".search_section input[type='search']").focus(function(){
      $(this).keydown(function(event){
        if(event.which == 13){
          let url = location.href;
          url = url.split('?');
          if(url.length > 1){
            if(getURLparams().get("q")){
              url[1] = url[1].split('&');
              url[1].shift();
              url[1].join('&');
              if(url[1] != "")
                location.href = url[0] + '?q=' + $(this).val() + '&' + url[1];
              else
                location.href = url[0] + '?q=' + $(this).val();
            }else{
              if(url[1] != "")
                location.href = url[0] + '?q=' + $(this).val() + '&' + url[1];
              else
                location.href = url[0] + '?q=' + $(this).val();
            }
          }else{
            location.href = url[0] + '?q=' + $(this).val();
          }
        }
      });
    });
    $(".checkbox_container label").click(function(){
      let target = $(this).parent();
      let isSelected = target.hasClass("selected");
      let inputId = target.children("input[type='checkbox']").attr("id");
      let input = document.getElementById(inputId);
      if(isSelected == true){
        target.removeClass("selected");
        input.removeAttribute("checked");
      }else{
        target.addClass("selected");
        input.setAttribute("checked", "");
      }
    });
    $(".burger_menu_icon").click(function(){
      if(!$(".mobile_menu").is(":visible"))
        $(".mobile_menu").css("display", "flex");
      else
        $(".mobile_menu").hide();
    });
    $(".close-menu").click(function(){
      $(".mobile_menu").hide();
    });
    $(".send_feedback").click(function(){
      let author = $(".feedback_form input[name='feedback_author']").val();
      let text = $(".feedback_form textarea[name='feedback_text']").val();
      if(author.trim() != "" && text.trim() != ""){
        $.ajax({
          url: "includes/rating_handler.php",
          type: "POST",
          data:{
            actionName: "sendMessage",
            author: author,
            text: text
          },
          cache: false,
          success: function(data){
            if(data == 'success') location.reload();
          }
        });
      }
    });
    $(".user_messages .page_header a").click(function(){
      let containerName = $(this).attr("container-name");
      $(".user_messages .page_header a").removeClass("active");
      $(this).addClass("active");
      $(".outcome, .income").hide();
      $("." + containerName).show();
    });
    $(".ban_user").click(function(){
      let userIp = $(".dark_list_form input[name='user_ip']").val();
      let datetime = $(".dark_list_form input[name='ban_datetime']").val();
      if(userIp.trim() != "" && datetime.trim() != ""){
        $.ajax({
          url: "includes/rating_handler.php",
          type: "POST",
          data:{
            actionName: "banUser",
            userIp: userIp,
            datetime: datetime
          },
          cache: false,
          success: function(data){
            console.log(data);
            if(data == 'success') location.reload();
          }
        });
      }
    });
});
