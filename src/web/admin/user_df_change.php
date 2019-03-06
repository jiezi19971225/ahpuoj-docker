<?php require_once "admin-header.php";
require_once "../include/check_get_key.php";
$cid = $_GET['cid'];
echo $cid;
if (!(isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    exit();
}

$sql = "select `defunct` FROM `users` WHERE `user_id`=?";
$result = pdo_query($sql, $cid);
if (count($result) < 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "用户不存在";
    header('Location:user_list.php');
    exit(0);
}
$row = $result[0];
if ($row[0] == 'N') {
    $sql = "UPDATE `users` SET `defunct`='Y' WHERE `user_id`=?";
} else {
    $sql = "UPDATE `users` SET `defunct`='N' WHERE `user_id`=?";
}

pdo_query($sql, $cid);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:user_list.php');
exit(0);
