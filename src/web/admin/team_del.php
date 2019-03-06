<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$team_id = intval($_GET['id']);

// 清空已经添加权限表的数据
$sql = "DELETE `privilege` FROM `privilege`
INNER JOIN `contest_team_user` ON `privilege`.user_id = `contest_team_user`.user_id
WHERE `privilege`.rightstr = CONCAT('c',`contest_team_user`.contest_id)
AND `contest_team_user`.team_id = ?";
pdo_query($sql, $rightstr, $team_id);

// 清除团队模式下设置的数据 比赛结束之后删除队伍数据不清除
// $sql = "DELETE FROM `contest_team` WHERE `team_id`=?";
// pdo_query($sql, $team_id);
// $sql = "DELETE FROM `contest_team_user` WHERE `team_id`=?";
// pdo_query($sql, $team_id);

// 删除团队
// $sql = "delete FROM `team_user` WHERE `team_id`=?";
// pdo_query($sql, $team_id);

$sql = "UPDATE `teams` SET is_delete= 'Y' WHERE team_id = ?";
pdo_query($sql, $team_id);

$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header('Location:team_list.php');
exit(0);
