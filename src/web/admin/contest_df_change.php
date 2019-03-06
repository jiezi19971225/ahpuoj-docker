<?php require_once "admin-header.php";
require_once "../include/check_get_key.php";
$cid = intval($_GET['cid']);
if (!(isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    exit();
}

$sql = "select `defunct` FROM `contest` WHERE `contest_id`=?";
$result = pdo_query($sql, $cid);
$num = count($result);
if ($num < 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "竞赛&作业不存在";
    header('Location:contest_list.php');
    exit(0);
}
$row = $result[0];
if ($row[0] == 'N') {
    $sql = "UPDATE `contest` SET `defunct`='Y' WHERE `contest_id`=?";
} else {
    $sql = "UPDATE `contest` SET `defunct`='N' WHERE `contest_id`=?";
}

pdo_query($sql, $cid);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:contest_list.php');
exit(0);
