<?php
require_once "admin-header.php";

if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once "../include/check_post_key.php";
} else {
    require_once "../include/check_get_key.php";
}

$plist = "";
sort($_POST['pid']);
foreach ($_POST['pid'] as $val) {
    if ($plist) {
        $plist .= ',' . intval($val);
    } else {
        $plist = $val;
    }

}

if (isset($_POST['enable']) && $plist) {
    $sql = "UPDATE `problem` SET defunct='N' WHERE `problem_id` IN ($plist)";
    pdo_query($sql);
} else if (isset($_POST['disable']) && $plist) {
    $sql = "UPDATE `problem` SET defunct='Y' WHERE `problem_id` IN ($plist)";
    pdo_query($sql);
} else {
    $id = intval($_GET['id']);
    $sql = "SELECT `defunct` FROM `problem` WHERE `problem_id`=?";
    $result = pdo_query($sql, $id);

    $row = $result[0];
    $defunct = $row[0];

    if ($defunct == 'Y') {
        $sql = "UPDATE `problem` SET `defunct`='N' WHERE `problem_id`=?";
    } else {
        $sql = "UPDATE `problem` SET `defunct`='Y' WHERE `problem_id`=?";
    }

    pdo_query($sql, $id);
}
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header("Location:problem_list.php");
exit(0);
