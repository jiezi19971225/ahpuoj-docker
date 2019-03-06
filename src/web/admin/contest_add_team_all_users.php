<?php require_once "admin-header.php";
require_once "../include/check_get_key.php";
$contest_id = intval($_GET['cid']);
$team_id = intval($_GET['tid']);
if (!(isset($_SESSION[$OJ_NAME . '_' . "m$contest_id"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    header("Location:contest_list.php");
    exit();
}

$sql = "SELECT * FROM `contest` WHERE contest_id=?";
$result = pdo_query($sql, $contest_id);

if (count($result) != 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "竞赛不存在";
    header("Location:contest_add_team.php?cid=$contest_id");
    exit(0);
}
$row = $result[0];

$team_mode = $row['team_mode'];
if ($team_mode != '1') {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "无法访问该页面";
    header('Location:contest_list.php');
}

// 验证团队是否属于该竞赛
$sql = "SELECT 1 FROM `contest_team` where contest_id = ? and team_id = ?";
$result = pdo_query($sql, $contest_id, $team_id);
if (count($result) < 1) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "参数错误";
    header("Location:contest_add_team.php?cid=$contest_id");
}

// 这SQL是真的不好写 还是我太菜了。。。
$sql = "INSERT IGNORE INTO `contest_team_user`(`contest_id`,`team_id`,`user_id`)
SELECT `contest_team`.contest_id,`contest_team`.team_id,`team_user`.user_id
FROM `contest_team` INNER JOIN `team_user` ON `contest_team`.team_id=`team_user`.team_id
WHERE `contest_team`.contest_id = ? AND `contest_team`.team_id = ?
AND `team_user`.user_id NOT IN
(SELECT user_id FROM `contest_team_user` INNER JOIN `teams` ON `contest_team_user`.team_id = `teams`.team_id WHERE `contest_id` = ? AND `teams`.is_delete='N')";

pdo_query($sql, $contest_id, $team_id, $contest_id);
$rightstr = "c$contest_id";

$sql = "INSERT IGNORE INTO `privilege`(`user_id`,`rightstr`) SELECT `user_id`,'$rightstr' from `team_user` where `team_user`.team_id=?";
pdo_query($sql, $team_id);

$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
header("Location:contest_add_team.php?cid=$contest_id");
exit(0);
