<?php require_once "admin-header.php";
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
$news_id = intval($_GET['id']);
$sql = "SELECT `defunct` FROM `news` WHERE `news_id`=?";
$result = pdo_query($sql, $news_id);
$row = $result[0];
$defunct = $row[0];

if ($defunct == 'Y') {
    $sql = "update `news` set `defunct`='N' where `news_id`=?";
} else {
    $sql = "update `news` set `defunct`='Y' where `news_id`=?";
}

pdo_query($sql, $news_id);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:news_list.php');
exit(0);
