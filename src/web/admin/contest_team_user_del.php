<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
$contest_id = intval($_GET['cid']);
$team_id = intval($_GET['tid']);
$user_id = $_GET['uid'];
$rightstr = "c$contest_id";

$sql = "SELECT * FROM `contest` WHERE contest_id=?";
$result = pdo_query($sql, $contest_id);

if (count($result) != 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "竞赛不存在";
    header("Location:contest_add_team.php?cid=$contest_id");
    exit(0);
}
$row = $result[0];

$team_mode = $row['team_mode'];
if ($team_mode != '1') {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "无法访问该页面";
    header('Location:contest_list.php');
}

$sql = "DELETE FROM `contest_team_user` WHERE `contest_id`=? AND `team_id`=? AND `user_id`=?";
pdo_query($sql, $contest_id, $team_id, $user_id);
$sql = "DELETE FROM `privilege` WHERE `user_id`=? AND `rightstr`= ?";
pdo_query($sql, $user_id, $rightstr);

$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header("Location:contest_add_team.php?cid=$contest_id");
exit(0);
