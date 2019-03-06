<?php
require_once "admin-header.php";
require_once "js.php";
require_once "../include/check_post_key.php";
if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$name = $_POST['name'];
$team_mode = $_POST['team_mode'];

if (get_magic_quotes_gpc()) {
    $name = stripslashes($name);
}

$name = RemoveXSS($name);

$sql = "SELECT 1 FROM series where name = ?";
$result = pdo_query($sql, $name);

if (count($result) > 0) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "系列赛名称必须唯一";
} else {
    $sql = "INSERT INTO series(`name`,`team_mode`) VALUES(?,?)";
    pdo_query($sql, $name, $team_mode);
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
}
header('Location:series_list.php');
exit(0);
