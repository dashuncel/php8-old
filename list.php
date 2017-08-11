<?php

include_once __DIR__.DIRECTORY_SEPARATOR.'lib.php';

readDest($dest); // чтение директории и заполнение глобальной переменной $filelist

// генерирует liшки для html со списком файлов
function setList() {
  global $filelist;
  global $dest;

  foreach ($filelist as $test) {
    $test_name=getTestName($dest.$test);
    if (!$test_name) { continue; } // если в файле отсутствует имя, считаем что файл неверный.
  	$test=explode(".",$test)[0];
   	echo "<li><a href=\"test.php?test=$test\">$test_name</a></li>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Список загруженных тестов</title>
	<link rel="stylesheet" href="./css/gentest.css">
	<meta charset="utf-8">
</head>
<body>
  <?php echo getMainMenu(); ?>
  <ul class="filelist list">
  <?php 
    setList(); 
  ?>
  </ul>
</body>
</html>
