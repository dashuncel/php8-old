<?php
define('USER_DATA_FILE', __DIR__ . '/json/user.json');
define('USER_PERM_FILE', __DIR__ . '/json/permission.json');

function login($login, $password)
{
    $user = getUser($login);
    if ($user && getPasswordHash($user['id'], $password) == $user['password']) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function isAuthorized()
{
    return !empty($_SESSION['user']);
}

function getUsers()
{
    if (!file_exists(USER_DATA_FILE)) {
        return [];
    }
    $data = file_get_contents(USER_DATA_FILE);
    $users = json_decode($data, true);
    if (!$users) {
        return [];
    }
    return $users;
}

function getUser($login)
{
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['login'] == $login) {
            return $user;
        }
    }

    return null;
}

function getCurrentUser()
{
    if (empty($_SESSION['user'])) {
        return null;
    }
    return $_SESSION['user'];
}

function getParam($name)
{
    if (!isset($_REQUEST[$name])) {
        return null;
    }
    return $_REQUEST[$name];
}

function isPost()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function logout()
{
    return session_destroy();
}

function redirect($action)
{
    header("Location: $action.php");
}

function getPasswordHash($id, $password)
{
    return md5($id . $password);
}

/*
 * добавление пользователя в список пользователей
 * считаем что id упорядочены и уникальны
 * */
function addUser($user, $password)
{
    $users = getUsers();
    echo "<pre>";
    print_r($users);
    echo "</pre>";

    $id = 0; // айди пользователя
    foreach ($users as $user) {
        $id = max($id, $user['id']);
    }
    $id++;
    echo $id;

    $users[] = ['id' => $id, 'login' => $user, 'password' => getPasswordHash($id, $password), 'name' => $user];
    file_put_contents(USER_DATA_FILE, json_encode($users), LOCK_EX);
    return true;
}