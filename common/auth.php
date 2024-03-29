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
        return $_SESSION['user']['username'];
    }
    return '';
}

function get_login_id() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user']['id'];
    }
    return null;
}

function is_admin() {
    if (!isset($_SESSION['user'])) return false;
    return $_SESSION['user']['role'] === 'admin';
}

function is_user() {
    return $_SESSION['user']['role'] === 'user';
}

function is_active() {
    return $_SESSION['user']['active'] == 1;
}
