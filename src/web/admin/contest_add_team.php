<?php
require_once "admin-header.php";
require_once "js.php";
if (isset($_GET['cid'])) {
    $contest_id = intval($_GET['cid']);
}
if (isset($_POST['contest_id'])) {
    $contest_id = intval($_POST['contest_id']);
}
if (!(isset($_SESSION[$OJ_NAME . '_' . "m$contest_id"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

if (isset($_POST['add_team_id'])) {
    require_once "../include/check_post_key.php";
    $contest_id = intval($_POST['contest_id']);
    $team_id = intval($_POST['add_team_id']);

    $sql = "SELECT * FROM `contest` WHERE contest_id=?";
    $result = pdo_query($sql, $contest_id);

    if (count($result) != 1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "竞赛不存在";
        header("Location:contest_list.php");
        exit(0);
    }
    $row = $result[0];

    $team_mode = $row['team_mode'];
    if ($team_mode != '1') {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "无法访问该页面";
        header('Location:contest_list.php');
    }

    // 判断团队是否存在
    $sql = "SELECT * FROM `teams` WHERE team_id = ? AND is_delete = 'N'";
    $result = pdo_query($sql, $team_id);
    if (count($result) < 1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "操作失败，该团队不存在";
        header("location:contest_add_team.php?cid=$contest_id");
        exit(0);
    }

    // 判断竞赛是否存在
    $sql = "SELECT * FROM `contest` WHERE contest_id = ?";
    $result = pdo_query($sql, $contest_id);
    if (count($result) < 1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "操作失败，该竞赛不存在";
        header("location:contest_add_team.php?cid=$contest_id");
        exit(0);
    }

    $sql = "INSERT INTO `contest_team`(contest_id,team_id) VALUES(?,?)";
    pdo_query($sql, $contest_id, $team_id);

    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("location:contest_add_team.php?cid=$contest_id");
    exit(0);

} else if (isset($_POST['ulist'])) {
    require_once "../include/check_post_key.php";

    $contest_id = intval($_POST['contest_id']);
    $team_id = intval($_POST['team_id']);
    $rightstr = "c$contest_id";

    $sql = "SELECT * FROM `contest` WHERE contest_id=?";
    $result = pdo_query($sql, $contest_id);

    if (count($result) != 1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "竞赛不存在";
        header("Location:contest_list.php");
        exit(0);
    }
    $row = $result[0];

    $team_mode = $row['team_mode'];
    if ($team_mode != '1') {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "无法访问该页面";
        header('Location:contest_list.php');
    }

    $pieces = explode("\n", trim($_POST['ulist']));
    if (count($pieces) > 0 && strlen($pieces[0]) > 0) {

        $sql_general = "INSERT INTO `contest_team_user`(`contest_id`,`team_id`,`user_id`) VALUES(?,?,?)";
        $sql_privilege_general = "INSERT INTO `privilege`(`user_id`,`rightstr`) VALUES (?,?)";
        for ($i = 0; $i < count($pieces); $i++) {
            $user_id = trim($pieces[$i]);
            $can_insert = true;

            // 判断竞赛&作业是否存在
            $sql = "SELECT 1 FROM `contest` WHERE contest_id = ?";
            $result = pdo_query($sql, $contest_id);
            if (count($result) < 1) {
                $can_insert = false;
            }

            // 判断用户是否存在且属于该团队
            $sql = "SELECT 1 FROM `users` INNER JOIN `team_user` ON `users`.user_id = `team_user`.user_id WHERE `team_user`.team_id=?";
            $result = pdo_query($sql, $team_id);
            if (count($result) < 1) {
                $can_insert = false;
            }

            // 判断用户是否注册了竞赛
            $sql = "SELECT 1 FROM `contest_team_user` WHERE contest_id = ? and user_id = ?";
            $result = pdo_query($sql, $contest_id, $user_id);
            if (count($result) > 0) {
                $can_insert = false;
            }

            // 插入数据库
            if ($can_insert) {
                pdo_query($sql_general, $contest_id, $team_id, $user_id);
                pdo_query($sql_privilege_general, $user_id, $rightstr);
            }
        }
    }
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("location:contest_add_team.php?cid=$contest_id");
    exit(0);

} else {
    require_once "../include/set_get_key.php";

    $contest_id = intval($_GET['cid']);
    $rightstr = "c$contest_id";

    $sql = "SELECT * FROM `contest` WHERE contest_id=?";
    $result = pdo_query($sql, $contest_id);

    if (count($result) != 1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "竞赛不存在";
        header("location:contest_list.php?cid=$contest_id");
        exit(0);
    }
    $row = $result[0];

    $team_mode = $row['team_mode'];
    if ($team_mode != '1') {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "无法访问该页面";
        header('Location:contest_list.php');
    }

    $title = htmlentities($row['title'], ENT_QUOTES, "UTF-8");

    $sql = "SELECT COUNT('user_id') AS ids FROM `privilege` INNER JOIN `users` ON `users`.user_id = `privilege`.user_id WHERE `privilege`.rightstr = ?";

    $result = pdo_query($sql, $rightstr);
    $row = $result[0];

    $ids = intval($row['ids']);

    $team_list = [];

    $team_attented_list = [];
    $team_attented_user_list = [];

    $sql = "SELECT * FROM `teams` WHERE `is_delete` = 'N' ORDER BY team_id DESC";
    $team_list = pdo_query($sql);

    $sql = "SELECT * FROM `teams` WHERE `team_id` IN (SELECT team_id FROM contest_team WHERE contest_id = ?) AND `is_delete` = 'N' ORDER BY team_id DESC";
    $team_attented_list = pdo_query($sql, $contest_id);
    foreach ($team_attented_list as $row) {
        $team_id = $row['team_id'];
        $sub_sql = "SELECT user_id, nick FROM `users` WHERE  users.user_id IN (SELECT user_id from `contest_team_user` WHERE contest_id = ? AND team_id = ?)";
        $team_attented_user_list[] = pdo_query($sub_sql, $contest_id, $row['team_id']);
    }

}
?>
<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title class="sub-page-title">竞赛&作业(团队赛)人员设置</title>
<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <?php require_once 'top_menubar.php';?>
        <?php require_once 'side_menubar.php';?>
        <div class="layui-body">
            <h3 class="sub-page-title">竞赛&作业(团队赛)人员设置</h3>
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p><b>竞赛&作业名称: <?php echo $title ?></b></p>
                        <p style="font-weight:bold;margin:20px 0 20px 0;">竞赛&作业人员总数: <?php echo $ids ?></p>
                        <form class="layui-form" method="POST" action="contest_add_team.php">
                            <div class="form-group">
                            <select name="add_team_id" lay-verify="" lay-search>
<?php
foreach ($team_list as $row) {
    echo "<option value='" . $row['team_id'] . "'>" . $row['name'] . "</option>";
}
?>
                                </select>
                            </div>
                            <input type="hidden" name="contest_id" value="<?php echo $contest_id ?>">
                            <?php require_once "../include/set_post_key.php";?>
                            <button type="submit" class="btn btn-primary btn-sm" style="margin-bottom: 10px;" lay-submit="">添加团队</button>
                        </form>


                        <div class="layui-collapse">
                        <?php foreach ($team_attented_list as $key => $row) {
    ?>
                            <div class="layui-colla-item">
                                <h2 class="layui-colla-title"><?php echo $row['name'] ?></h2>
                                <div class="layui-colla-content layui-show">
                                    <button type="button" class="btn btn-success btn-sm" style="margin-bottom: 10px;" onclick="addContestTeamUser(<?php echo $row['team_id'] ?>)">添加成员</button>
                                    <button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 10px;" onclick="addContestTeamAllUsers(<?php echo $row['team_id'] ?>)">添加全部成员</button>
                                    <button type="button" class="btn btn-danger btn-sm" style="margin-bottom: 10px;" onclick="deleteContestTeam(<?php echo $row['team_id'] . ",'" . $row['name'] . "'" ?>)">删除团队</button>
                                    <table class="table table-striped">
                                        <tr>
                                            <td>ID</td>
                                            <td>昵称</td>
                                            <td>操作</td>
                                        </tr>
                                        <?php foreach ($team_attented_user_list[$key] as $sub_row) {
        echo "<tr>";
        echo "<td>" . $sub_row['user_id'] . "</td>";
        echo "<td>" . $sub_row['nick'] . "</td>";
        echo "<td><a class='btn btn-sm btn-danger' href='javascript:void(0);' onclick=\"deleteContestTeamUser(" . $row['team_id'] . ",'" . $sub_row['user_id'] . "')\">删除</a></td>";
        echo "</tr>";
    }
    ?>
                                    </table>
                                </div>
                            </div>
                        <?php
}
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php require_once 'js.php';?>
    <script>
        layui.use(['element', 'layer','form'], function () {
            var element = layui.element,
                layer = layui.layer,
                form = layui.form;
                <?php
$status = flash_status_session();
if ($status == $OPERATOR_SUCCESS) {
    echo "layer.msg('操作成功');\n";
} else if ($status == $OPERATOR_FAILURE) {
    $msg = $_SESSION['error_message'];
    echo "layer.msg('$msg',{time:5000});\n";

}
?>
        });

        function addContestTeamUser(teamID){
            layer.open({
                type:1,
                area:['500px','750px'],
                title: '添加竞赛&作业成员',
                content:'<form method="POST" action="contest_add_team.php" class="admin-layer-form layui-form">'
                            +'<div class="form-group">'
                            +'<label for="ulist">添加竞赛&作业成员</label>'
                            +'<textarea name="ulist" rows="35" style="width:100%;" placeholder="每一行对应一个用户ID，若对应账号存在则加入团队，不存在则无视之"></textarea>'
                            +'</div>'
                            +'<input type="hidden" name="contest_id" value=<?php echo $contest_id ?>>'
                            +'<input type="hidden" name="team_id" value="'+teamID+'">'
                            +'<input type="hidden" name="postkey" value="<?php echo $_SESSION[$OJ_NAME . '_' . 'postkey'] ?>">'
                            +'<?php require "../csrf.php";?>'
                            +'<button class="btn btn-primary" type="submit" lay-submit="">提交</button>'
                        +'</form>'
            })
        }
        function addContestTeamAllUsers(teamID){
            layer.confirm('确定要将该团队全体成员加入到' + '<?php echo $title ?>中吗?已经在其他队伍中的人员将不会再添加！', { icon: 3, title: '提示', offset: '300px' }, function (index) {
                window.location.href = 'contest_add_team_all_users.php?cid=' + <?php echo $contest_id ?> + '&tid=' + teamID  + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
                layer.close(index);
            });
        }
        function deleteContestTeam(teamID,teamName){
            layer.confirm('确定要删除将' + teamName+ '从<?php echo $title ?>移除吗?', { icon: 3, title: '提示', offset: '300px' }, function (index) {
                window.location.href = 'contest_team_del.php?cid=' + <?php echo $contest_id ?> + '&tid=' +teamID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
                layer.close(index);
            });
        }
        function deleteContestTeamUser(teamID, userID) {
            layer.confirm('确定要删除将' + userID + '从<?php echo $title ?>移除吗?', { icon: 3, title: '提示', offset: '300px' }, function (index) {
                window.location.href = 'contest_team_user_del.php?cid=' + <?php echo $contest_id ?> + '&tid=' +teamID + '&uid=' + userID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
                layer.close(index);
            });
        }
    </script>
</body>

</html>