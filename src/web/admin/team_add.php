<?php
require_once "admin-header.php";
require_once "js.php";
require_once "../include/check_post_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$name = $_POST['name'];

if (get_magic_quotes_gpc()) {
    $name = stripslashes($name);
}

$name = RemoveXSS($name);

$sql = "SELECT 1 FROM `teams` where name = ? AND is_delete = 'N'";
$result = pdo_query($sql, $name);

if (count($result) > 0) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "创建团队失败，团队名称必须唯一";
} else {
    $sql = "INSERT INTO teams(`name`,`reg_time`) VALUES(?,now())";
    if (!pdo_query($sql, $name)) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "创建团队失败，团队名称过长";
    } else {
        $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    }
}
header('Location:team_list.php');
exit(0);
