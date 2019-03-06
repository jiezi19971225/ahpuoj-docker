<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
$id = intval($_GET['id']);

$sql = "delete FROM `contest_series` WHERE `series_id`=?";
pdo_query($sql, $id);
$sql = "delete FROM `series` WHERE `series_id`=?";
pdo_query($sql, $id);

$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:series_list.php');
exit(0);
