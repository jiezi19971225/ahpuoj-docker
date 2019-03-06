<?php	$cache_time = 10;
$OJ_CACHE_SHARE = false;
require_once "./frontend-header.php";
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $errors = "<a href=./loginpage.php>登录</a>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(0);
}

$sql = "SELECT `school`,`nick`,`email` FROM `users` WHERE `user_id`=?";
$result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
$row = $result[0];
require "template/" . $OJ_TEMPLATE . "/modifypage.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
