<?php require_once "admin-header.php";
require_once "../include/check_get_key.php";
$cid = intval($_GET['cid']);
if (!(isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "没有权限";
    exit(1);
}

$sql = "select `private` FROM `contest` WHERE `contest_id`=?";
$result = pdo_query($sql, $cid);
$num = count($result);
if ($num < 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "竞赛&作业不存在";
    header('Location:contest_list.php');
    exit(0);
}
$row = $result[0];
if (intval($row[0]) == 0) {
    $sql = "UPDATE `contest` SET `private`='1' WHERE `contest_id`=?";
} else {
    $sql = "UPDATE `contest` SET `private`='0' WHERE `contest_id`=?";
}

pdo_query($sql, $cid);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:contest_list.php');
exit(0);
