<?php
    
include_once __DIR__.DIRECTORY_SEPARATOR.'lib.php';
$result=[];
if(isset($_POST['btn']) && isset($_FILES)) {
  if (! file_exists($dest)) { mkdir($dest); }
  foreach($_FILES as $key => $val) {
    foreach($val['name'] as $sub_key => $filename) {
      
      // проверка существования файла:
      if (! file_exists($val['tmp_name'][$sub_key])) { 
        $result[]="Файл $filename по какой-то причине не загружен на сервер!<br/>";
        continue; 
      }
        
      // проверка расширения файла:
      preg_match($pattern, $filename, $matches); 
      if (count($matches) < 1) {
        $result[]="Файл $filename неверного формата<br/>";
        continue;
      }
      
      // загрузка и проверка загрузки файла в каталог:
      if (move_uploaded_file($val['tmp_name'][$sub_key], $dest.$filename)) {
        $result[]="Файл $filename успешно загружен в каталог $dest<br/>";
      } else {
        $result[]="Ошибка загрузки файла $filename в каталог $dest<br/>";
      }
    }
  }
  header('location: list.php');
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Генератор тестов</title>  
  <link rel="stylesheet" href="./css/gentest.css">
	<meta charset="utf-8">
</head>
<body>
  <?php echo getMainMenu(); ?>
  <form action="" method="POST" enctype="multipart/form-data">
    <fieldset>
    <legend>Загрузчик тестов</legend>
    <label>Файлы:  <input type="file" name="tests[]" multiple required></label><br/><br/>
    <input type="submit" value="Загрузить тесты" name="btn"><br/>
    <div class="farea hidden">
      <p>Список выбранных файлов:</p>
      <ul class="filelist">
      </ul>
      <textarea id="fileContent" class="hidden" rows="10" cols="180"></textarea>
    </div><br/>
    </fieldset>
  </form>
  <script type="text/javascript">
    'use strict';
    let forma=new FormData();
    document.querySelector('input[type=file]').addEventListener('change',chooseFile); // обработчик на поле - выбор файлов
    document.querySelector('.farea').addEventListener('click',readFile); // обработчик на див - чтение файла по ссылке

    // Обработчик события выбора файлов в форме:
    function chooseFile(event) {
      const fragment = document.createDocumentFragment();
      const divarea = document.querySelector('.farea');
      const ularea = document.querySelector('.filelist');
      const files = Array.from(event.target.files);
      Array.from(ularea.children).forEach(lishka => {ularea.removeChild(lishka);}); // удаляем прежние лишки
      divarea.classList.remove('hidden');
      files.forEach(file => {
        const a = document.createElement('a');
        const li = document.createElement('li');
        a.setAttribute("href","#");
        forma.append(file.name, file);
        a.innerHTML=file.name;
        li.appendChild(a);
        fragment.appendChild(li);
      });
      ularea.appendChild(fragment);
    }

    // обработчик события клика по ссылке с именем файла:
    function readFile(event) {  
      if (event.target.tagName != "A") { return; }
      event.preventDefault();
      const reader = new FileReader();
      const fileContent=document.getElementById("fileContent");
      let myfile=forma.get(event.target.textContent);
      fileContent.classList.remove('hidden');
      reader.addEventListener('load', event=> {
        fileContent.value = event.target.result;
      });
      reader.readAsText(myfile);
    }
  </script>
</body>
</html>
