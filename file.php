<?php

session_start();
$jsonData = $_SESSION['data'];

$nom=1;
$results=[]; // массив с результатами

if ($_POST) {
  $right_answers=0;
  $total_questions=0;
  if (! empty($_POST['fio'])) {
    $name=$_POST['fio'];
  }
  foreach($jsonData as $key => $question) { // перебираем вопросы, есть ли ответ на вопрос?
    $total_questions++;
    if (! array_key_exists($key, $_POST)) {
      $results[]="$nom. Ответ не предоставлен. Правильный ответ: ".$jsonData[$key]['Ответ'];
      continue;
    }
    if (is_array($_POST[$key])) {
      $ans=implode($_POST[$key],","); // чекбоксы собираем из массива
    } else {
      $ans=$_POST[$key]; // радиокнопки - обычное значение
    }
    if ($jsonData[$key]['Ответ'] != $ans) {
      $results[]="$nom. Ответ неверный. Ваш ответ: $ans. Правильный ответ: ".$jsonData[$key]['Ответ'];
    } else {
      $results[]="$nom. Ответ $ans верный";
      $right_answers++;
    }
    $nom++;
  }

  $titul =  $_SESSION['titul'];
  if ($right_answers == $total_questions) {
    $text = "Поздравляю, $name!&Вы успешно прошли тестирование&и получаете звание&$titul";
  }
  else {
    $text = "Сожалеем, $name &Тестирование не пройден";
  }
  $_SESSION['text'] = $text;

  echo '<div class="res"><pre>'.implode($results, PHP_EOL).'</pre></div>';
}
