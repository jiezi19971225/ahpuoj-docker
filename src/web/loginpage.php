<?php
$cache_time = 1;
require_once "./frontend-header.php";
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $errors = "<a href='logout.php'>请先退出登录，点击退出！</a>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(1);
}
require "template/" . $OJ_TEMPLATE . "/loginpage.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
