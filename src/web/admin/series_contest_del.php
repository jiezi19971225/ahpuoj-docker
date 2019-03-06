<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
$series_id = intval($_GET['sid']);
$contest_id = $_GET['cid'];

$sql = "delete FROM `contest_series` WHERE `series_id`=? AND `contest_id`=?";
pdo_query($sql, $series_id, $contest_id);
$sql = "update `series` set contest_count=contest_count-1 where series_id=?";
pdo_query($sql, $series_id);

$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:series_list.php');
exit(0);
