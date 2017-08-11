<?php 
 
$dest="tests".DIRECTORY_SEPARATOR; // директория сфайлами теста

$pattern="/^\w*\.json$/";
$filelist=[]; // список файлов
$dataForPick=[];
$jsonData=[];

// функция читает директорию с тестами
function readDest($destination) {
  $result=''; 
  if (! file_exists($destination)) { 
    mkdir($destination);
    $result="директория $destination не существует";
  }
  $cdir=scandir($destination);
  if (! $cdir) {
    $result="тесты не загружены";
  }
  if ($result) { return $result; }

  global $filelist;
  global $pattern;
  foreach ($cdir as $key => $value) { 
    if (! is_dir($value)) {
      preg_match($pattern, $value, $matches);
      if (count($matches) < 1) { continue; }
      $filelist[]=$value;
    } 
  }
}

// функция возвращает удобочитаемое название по имени файла
function getTestName($test) {
  $data=file_get_contents($test);
  $jsonData=json_decode($data, JSON_UNESCAPED_UNICODE); 
  $test_name='';
  if (empty($jsonData) || ! is_array($jsonData)) { 
    return $test_name; 
  }

  if (array_key_exists("0", $jsonData) && array_key_exists("Название", $jsonData[0])) { 
    $test_name=$jsonData[0]['Название'];
  }
  return $test_name;
}

// функция отстраивает главное меню
function getMainMenu() {
  $menu_str='';
  $menu=["admin" => "Администрирование",
         "list" => "Список тестов"
        ];

  if (!session_id()) session_start();
  $menu_str .= "<div class=\"login\">Здравствуй, {$_SESSION['user']} &nbsp;<a href='logout.php'>Выход</a></div>";

  $menu_str="<ul class=\"menu\">";
  foreach ($menu as $file => $item) { 
    $menu_str .= "<li><a href=\"$file.php\">$item</a></li>";
  }
  $menu_str .= "</ul>";

  return $menu_str;
}


