<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
$contest_id = intval($_GET['cid']);
$rightstr = "c$contest_id";
$user_id = $_GET['uid'];

$sql = "SELECT * FROM `contest` WHERE contest_id=?";
$result = pdo_query($sql, $contest_id);

if (count($result) != 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "竞赛不存在";
    header("Location:contest_add_user.php?cid=$contest_id");
    exit(0);
}
$row = $result[0];

$team_mode = $row['team_mode'];
if ($team_mode != '0') {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "无法访问该页面";
    header('Location:contest_list.php');
}

$sql = "delete FROM `privilege` WHERE `user_id`=? AND `rightstr`=?";
pdo_query($sql, $user_id, $rightstr);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header("Location:contest_add_user.php?cid=$contest_id");
exit(0);
