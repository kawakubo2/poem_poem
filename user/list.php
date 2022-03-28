<?php
require_once '../DbManager';
require_once '../Encode.php';
require_once '../common/auth.php';

<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません。');
}
