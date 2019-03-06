<?php require_once "admin-header.php";
require_once "../include/check_get_key.php";
$contest_id = intval($_GET['cid']);
if (!(isset($_SESSION[$OJ_NAME . '_' . "m$contest_id"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    exit();
}

$sql = "select `team_mode`,`start_time`,`end_time` FROM `contest` WHERE `contest_id`=?";
$result = pdo_query($sql, $contest_id);
$num = count($result);
if ($num < 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "竞赛&作业不存在";
    header('Location:contest_list.php');
    exit(0);
}

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

if ($row[0] == '0') {
    $sql = "UPDATE `contest` SET `team_mode`='1' WHERE `contest_id`=?";
    pdo_query($sql, $contest_id);
} else {
    $sql = "UPDATE `contest` SET `team_mode`='0' WHERE `contest_id`=?";
    pdo_query($sql, $contest_id);
    // 清除团队模式下设置的数据
    $sql = "DELETE FROM `contest_team` WHERE `contest_id`=?";
    pdo_query($sql, $contest_id);
    $sql = "DELETE FROM `contest_team_user` WHERE `contest_id`=?";
    pdo_query($sql, $contest_id);
}

$rightstr = "c$contest_id";

// 清空已经添加权限表的数据
$sql = "DELETE FROM `privilege` where `rightstr` = ?";
echo pdo_query($sql, $rightstr);

$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:contest_list.php');
exit(0);
