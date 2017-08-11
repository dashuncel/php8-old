<?php
require_once 'functions.php';

$errors = []; // массив ошибок
$nameButton = ""; // название кнопки на форме зависит от GET-параметра
$nameLink = ""; // название ссылки
$blockTime = 0; // время бокировки - запоминаем
static $counter_1 = 0;
static $counter_2 = 0;

define('MAX_BEF_captcha',5); // сколько попыток ввести неверный пароль есть у пользователя
define('MAX_BEF_LOCK',7); // сколько попыток ввести капчу есть у пользователя до блокировки
define('TIME_LOCK', 120 ); // время блокировки пользователя в секундах

if (isAuthorized()) {
    redirect('index','');
}

if (!session_id()) {
    session_start();
    echo $_SESSION['counter_1'] ."<br/>";
}

// проверяем, есть ли блокировка на клиенте?
if (isset($_COOKIE['block'])) {
    $timestamp = $blockTime - time();
    redirect('lock', "time?$timestamp");
}

//------ ПРОВЕРКА КАПЧИ --------:
if (isset($_POST['check'])) {
    checkCaptcha();
}

//------ АВТОРИЗАЦИЯ/РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ --------:
if (isset($_POST['enter'])) {
    foreach ($errors as $key => $err) {
        unset($errors['$key']);
    }

    $login = filter_input(INPUT_POST, 'login');
    $passwd = filter_input(INPUT_POST, 'password');

    //---регистрация:---
    if ($typeAltForm == ENTER['param']) {
        registration($login, $passwd);
    } //---авторизация:---
    elseif ($typeAltForm == REG['param']) {
        autorization($login, $passwd);
    }
}

//--авторизация:
function autorization($login, $passwd)
{
    global $errors;
    static $counter = 0;

    if ($login == "Гость") {
        $_SESSION['user'] = $login;
        redirect('list', ''); // пропускаем админскую страничку, сразу к списку тестов
    }

    if (login($login, $passwd)) {
        $_SESSION['user'] = $login;
        $counter = 0;
        redirect('admin', '');
    } else {
        $counter++;
        echo $counter . ' ' . $passwd . "<br/>";
        $errors[] = 'Неверный логин или пароль';
        if ($counter > MAX_BEF_LOCK) {
            echo '1'; // признак того что нам нао отобразить капчу!
        }
    }
}

//--регистрация:
function registration ($login, $passwd) {
    global $errors;

    $res = addUser($login, $passwd);
    if ($res === true) {
        $_SESSION['user'] = $login;
        redirect('admin', ''); // пропускаем админскую страничку, сразу к списку тестов
    }
    else
        $errors[] = $res;
}

//---проверка капчи:
function  checkCaptcha()
{
    static $counter = 0;

    if (Captcha::check($_POST['captcha'])) {
        $errors[] = 'Проверочный код введён верно!';
        $counter = 0;
    } else {
        $errors[] = 'Проверочный код введён неверно!';
        $counter++;
    }

    if ($counter > MAX_BEF_LOCK) {
        $blockTime = time();
        setcookie('block', '', time() + TIME_LOCK);
        redirect('lock', "time?120");
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="./css/index.css">
    <meta charset="utf-8">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
</head>
<body>
<div id="container">
    <form action="" method="POST" enctype="multipart/form-data" class="mainform">
        <p class="forgot"><a href="index.php?type=<?=$typeAltForm?>"><?= $nameLink ?></a></p>
        <label for="name">Логин:</label>
        <input type="name" name="login" required>
        <label for="username">Пароль:</label>
        <input type="password" name="password">
        <div id="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="lower">
            <input type="submit" name="enter" value=<?= $nameButton ?>>
        </div>
    </form>
    <form class="captcha hidden" action="" method="POST" enctype="multipart/form-data">
        <div class="captcha">
            <p><img src='gencaptcha.php' alt='Капча'/></p>
            <p>Проверочный код: <input type='text' name='captcha'/></p>
            <p><input type='submit' name='check' value='Отправить'/></p>
        </div>
    </form>
</div>
<script>
    'use strict';
    $('#lower input').click(function(event) { // надо вручную обработать результат, чтобы решить, отобразить капчу или нет
        event.preventDefault();
        .post("",
               $('.mainform').serialize(),
               function(data, result){
                   console.log(result, data);
            }
        );
    });
</script>
</body>
</html>



