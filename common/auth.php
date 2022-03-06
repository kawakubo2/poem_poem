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