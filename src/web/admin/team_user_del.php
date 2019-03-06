<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$team_id = intval($_GET['tid']);
$user_id = $_GET['uid'];
// 应该删除contest_team_user表中相应的记录和privilege表的的记录
$sql = "DELETE `privilege` FROM `privilege` INNER JOIN `contest_team_user` ON `privilege`.user_id = `contest_team_user`.user_id
        WHERE `contest_team_user`.team_id = ?  AND `contest_team_user`.user_id = ?";
pdo_query($sql, $team_id, $user_id);

$sql = "DELETE FROM `contest_team_user` WHERE team_id = ? AND user_id = ?";
pdo_query($sql, $team_id, $user_id);

$sql = "DELETE FROM `team_user` WHERE `team_id`=? AND `user_id`=?";
pdo_query($sql, $team_id, $user_id);
$sql = "UPDATE `teams` SET user_count=user_count-1 WHERE team_id=?";
pdo_query($sql, $team_id);

$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header("Location:team_edit.php?id=$team_id");
exit(0);
