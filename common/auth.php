<?php
if (!isset($_SESSION)) {
    session_start();
}

function is_login() {
    if (isset($_SESSION['user'])) {
        return true;
    }
    return false;
}

function authenticate() {
    if (!is_login()) {
        header('Location: http://' . $_SERVER['HTTP_HOST']
            . '/poem_poem/login/login.php');
        exit();
    }
}

function authorize($user_id) {
    if ($user_id !== $_SESSION['user']['id']) {
        die('このページに対するアクセス権限がありません。');
    }
}

function get_login_name() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user']['name'];
    }
    return '';
}

function get_login_id() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user']['id'];
    }
    return null;
}