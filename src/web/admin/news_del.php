<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$news_id = intval($_GET['id']);
$sql = "delete FROM `news` WHERE `news_id`=?";
pdo_query($sql, $news_id);
$sql = "select max(news_id) FROM `news`";
$result = pdo_query($sql);
$row = $result[0];
$max_id = $row[0];
$max_id++;
if ($max_id < 1000) {
    $max_id = 1000;
}

$sql = "ALTER TABLE news AUTO_INCREMENT = $max_id";
pdo_query($sql);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;

header('Location:news_list.php');
exit(0);
