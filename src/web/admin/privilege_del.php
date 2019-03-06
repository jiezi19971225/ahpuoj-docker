<?php
require_once "admin-header.php";
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
$user_id = $_GET['uid'];
$rightstr = $_GET['rightstr'];
$sql = "delete from `privilege` where user_id=? and rightstr=?";
pdo_query($sql, $user_id, $rightstr);
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:privilege_list.php');
exit(0);
