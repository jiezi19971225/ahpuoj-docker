<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$contest_id = intval($_GET['id']);

$sql = "SELECT start_time,end_time FROM `contest` WHERE contest_id=?";
$result = pdo_query($sql, $contest_id);
$row = $result[0];
$start_time = strtotime($row['start_time']);
$end_time = strtotime($row['end_time']);
$now = time();
if ($now > $start_time && $now < $end_time) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "正在进行中的竞赛&作业无法执行此操作";
    header('Location:contest_list.php');
    exit(0);
}

// 清空已经添加权限表的数据
$rightstr = "c$contest_id";
$sql = "DELETE FROM `privilege` WHERE `rightstr` = ?";
echo pdo_query($sql, $rightstr);

// 清除团队模式下设置的数据
$sql = "DELETE FROM `contest_team` WHERE `contest_id`=?";
pdo_query($sql, $cid);
$sql = "DELETE FROM `contest_team_user` WHERE `contest_id`=?";
pdo_query($sql, $cid);

// 删除系列赛关联数据
$sql = "DELETE FROM `contest_series` WHERE `contest_id`=?";
pdo_query($sql, $contest_id);

$sql = "DELETE FROM `contest_problem` WHERE `contest_id`=?";
pdo_query($sql, $contest_id);
$sql = "DELETE FROM `contest` WHERE `contest_id`=?";
pdo_query($sql, $contest_id);

$result = pdo_query($sql);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:contest_list.php');
exit(0);
