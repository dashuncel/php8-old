<?php
include_once __DIR__.DIRECTORY_SEPARATOR.'lib.php';

readDest($dest);
session_start();

// обработка get-запроса
if (! empty($_GET['test'])) {
  $result=array_search($_GET['test'].".json", $filelist, true);
  if ($result === false) {
    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); 
    http_response_code(404);
    echo '<h2>Тест "'.$_GET['test'].'" не найден!</h2>';
    exit(); 
  } else {
    $data=file_get_contents($dest.$filelist[$result]);
    $jsonData=json_decode($data, JSON_UNESCAPED_UNICODE); 
    if (json_last_error() != 0) {
      echo "Ошибка чтения json файла ".json_last_error(); 
    }
  } 

  $_SESSION['titul'] = $jsonData[0]['титул'];
  $nametest=$jsonData[0]['Название'];
  $jsonData=$jsonData[0]['Вопросы'];
} else {
  header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); 
  http_response_code(404);
  echo '<h2>Cтраница не найдена!</h2>';
  exit();
}

$_SESSION['data']=$jsonData;

// заполнение формы в HTML
function fillForm() {
  global $jsonData;
  $answers=[];
  $r_answer='';
  $formStr='';

  foreach ($jsonData as $key => $question) {
    $name=$key;
    foreach ($question as $s_key => $qdata) {
      switch ($s_key) {
         case 'Вопрос':
           $question=$qdata;
         break;
         case 'Варианты':
           $answers=$qdata;
         break;
         case 'Ответ':
           $r_answer=$qdata;
         break;
      }    
    }
    $nom=++$key;
    $formStr.= "<label class=\"question\">$nom. $question</label>";
    $formStr.= "<ul class=\"answers\">";
    if (count(explode(",", $r_answer)) > 1) {
      foreach ($answers as $key => $ans) {
        $formStr.= "<li><input type=\"checkbox\" name=\"$name"."[]"."\" value=\"$key\" id=\"$name$key\"/><label for=\"$name$key\">$ans</label></li>";
      }  
    } else {
      foreach ($answers as $key => $ans) {
        $formStr.= "<li><input type=\"radio\" name=\"$name\" value=\"$key\" id=\"$name$key\"/><label for=\"$name$key\">$ans</label></li>";
      }
    }
    $formStr.= "</ul>";
  }  
  return $formStr;
}
?> 

<!DOCTYPE html>
<html>
<head>
  <title><?=$nametest?></title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="./css/gentest.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
</head>
<body>
   <?php echo getMainMenu(); ?>
   <form>
   <fieldset class='hidden'>
      <legend>Результат</legend>
      <div class='output'></div>
   </fieldset>
   </form>
   <form action="fileeeeeeeeeee.php" method="POST" enctype="application/json" class="mainform"> 
     <fieldset>
       <legend><?=$nametest?></legend>
       <label>Представьтесь, пожалуйста: <input type="text" name="fio" required></label><br/><br/>
       <?php echo fillForm(); ?>
       <br/><input type="submit" value="Проверить ответы" name="btn_check"><br/>
     </fieldset>
   </form>
   <script type="text/javascript">
     /* проверка формы на клиентской стороне */
   'use strict';
    const btn = document.querySelector("[type=submit]");
    const ans = document.querySelectorAll("[type=radio], [type=checkbox]");
    const quests=document.getElementsByClassName('question');
  
    Array.from(ans).forEach(a => {a.addEventListener('change',unsetErr)});

    $(document).ready(function(){
      $('.mainform').submit(function(event){
          event.preventDefault();
          $('.output').html = '';
          $('fieldset.hidden').removeClass('hidden');
          $('.question').each(function(i, val) { chkElement(val); }); 
          $('.error').each(function(i, val) { 
            $('.output').html('Внимание! Не выбраны ответы на некоторые вопросы. Заполните всю форму перед отправкой.');
            return;
          })
          if ($('.output').html() != '') { return false; };
          $.ajax({
            url: 'file.php',
            type:'post',
            data: $('.mainform').serialize(),
            success: function(result){
                $('.output').children().each(function(i, elem) { elem.detach(); });
                $('.output').html(result);
                $('.output').append("<a target=_blank href='big_pick.php'><img src='pick.php'></a>");
            }
          });
      });
    });

    function unsetErr(event) {
      event.target.parentElement.parentElement.previousElementSibling.classList.remove('error');
    }

    function chkElement(quest) {
      const li=quest.nextElementSibling.firstChild.firstChild.getAttribute('name');
      const grp=document.getElementsByName(li);
      let chked=Array.from(grp).filter(g => { return g.checked; }); 
      if (chked.length == 0) {
        quest.classList.add('error');
      }
      else {
        quest.classList.remove('error');
      }
    }
   </script>
</body>
</html>