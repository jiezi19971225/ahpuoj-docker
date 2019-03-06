<?php
require_once "admin-header.php";
require_once "../include/check_get_key.php";
$series_id = intval($_GET['sid']);
if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    exit();
}

$sql = "SELECT `team_mode` FROM `series` WHERE `series_id`=?";
$result = pdo_query($sql, $series_id);
$num = count($result);
if ($num < 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "竞赛&作业不存在";
    header('Location:series_list.php');
    exit(0);
}

$row = $result[0];
if ($row[0] == '0') {
    $sql = "UPDATE `series` SET `team_mode`='1' WHERE `series_id`=?";
    pdo_query($sql, $series_id);
} else {
    $sql = "UPDATE `series` SET `team_mode`='0' WHERE `series_id`=?";
    pdo_query($sql, $series_id);
}
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:series_list.php');
