<?php
require_once "admin-header.php";
require_once "js.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

if (isset($_POST['name'])) {
    require_once "../include/check_post_key.php";
    $team_id = intval($_POST['team_id']);
    $name = $_POST['name'];
    $name = RemoveXSS($name);
    if (get_magic_quotes_gpc()) {
        $name = stripslashes($name);
    }

    $sql = "SELECT name FROM `teams` WHERE `team_id`=?";
    $result = pdo_query($sql, $team_id);
    $row = $result[0];
    $old_name = $row['name'];
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    if ($name != $old_name) {
        $sql = "UPDATE `teams` SET `name`=? WHERE `team_id`=?";
        if (!pdo_query($sql, $name, $team_id)) {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "修改团队名称失败，团队名称必须唯一";
        }
    }
    header("location:team_edit.php?id=$team_id");
    exit(0);

} else if (isset($_POST['ulist'])) {
    require_once "../include/check_post_key.php";
    $team_id = intval($_POST['team_id']);

    $pieces = explode("\n", trim($_POST['ulist']));
    if (count($pieces) > 0 && strlen($pieces[0]) > 0) {
        echo count($pieces);
        $sql_general = "INSERT INTO `team_user`(`team_id`,`user_id`) VALUES (?,?)";
        $sql_update_count = "UPDATE `teams` SET user_count = user_count+1 where team_id=?";
        for ($i = 0; $i < count($pieces); $i++) {
            $user_id = trim($pieces[$i]);
            $can_insert = true;

            // 判断用户是否存在
            $sql = "SELECT 1 FROM `users` WHERE user_id = ?";
            $result = pdo_query($sql, $user_id);
            if (count($result) < 1) {
                $can_insert = false;
            }

            // 判断用户是否已经在团队中
            $sql = "SELECT 1 FROM `team_user` WHERE team_id = ? and user_id = ?";
            $result = pdo_query($sql, $team_id, $user_id);
            if (count($result) > 0) {
                $can_insert = false;
            }

            if ($can_insert) {
                pdo_query($sql_general, $team_id, $user_id);
                pdo_query($sql_update_count, $team_id);
            }
        }
    }
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("location:team_edit.php?id=$team_id");
    exit(0);

} else {
    $team_id = intval($_GET['id']);
    $sql = "SELECT * FROM `teams` WHERE team_id=?";
    $result = pdo_query($sql, $team_id);

    if (count($result) != 1) {
        header("location:404.php");
        exit(0);
    }
    $row = $result[0];
    $name = htmlentities($row['name'], ENT_QUOTES, "UTF-8");

    $sql = "SELECT COUNT('user_id') AS ids FROM `team_user` INNER JOIN `users` ON `users`.user_id = `team_user`.user_id WHERE `team_user`.team_id = ?";
    $result = pdo_query($sql, $team_id);
    $row = $result[0];

    $ids = intval($row['ids']);
    $idsperpage = 10;
    $pages = max(intval(ceil($ids / $idsperpage)), 1);

    if (isset($_GET['page'])) {$page = intval($_GET['page']);} else { $page = 1;}

    $pagesperframe = 5;
    $frame = intval(ceil($page / $pagesperframe));

    $spage = ($frame - 1) * $pagesperframe + 1;
    $epage = min($spage + $pagesperframe - 1, $pages);

    $sid = ($page - 1) * $idsperpage;

    if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
        $keyword = $_GET['keyword'];
        $keyword = "%$keyword%";
        $sql = "SELECT * FROM `users` INNER JOIN `team_user` ON `users`.user_id = `team_user`.user_id WHERE `team_user`.team_id=? AND (`users`.user_id like ? OR `users`.nick like ?)";
        $result = pdo_query($sql, $team_id, $keyword, $keyword);
    } else {
        $sql = "SELECT * FROM `users` INNER JOIN `team_user` ON `users`.user_id = `team_user`.user_id WHERE `team_user`.team_id=?";
        $result = pdo_query($sql, $team_id);
    }
}
?>

    <!DOCTYPE html>

    <html>
    <?php require_once 'admin-header.php'?>
    <title class="sub-page-title">编辑团队</title>

    <body class="layui-layout-body">
        <div class="layui-layout layui-layout-admin">
            <?php require_once 'top_menubar.php';?>
            <?php require_once 'side_menubar.php';?>
            <div class="layui-body">
                <h3 class="sub-page-title">编辑团队</h3>
                <div class="container">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form method=POST action="team_edit.php" class="form-inline layui-form">
                                <div class="form-group">
                                    <label for="name">团队名称</label>
                                    <input class="form-control" type="text" name="name" id="name" value='<?php echo $name ?>' lay-verify="required" placeholder="请输入团队名称">
                                </div>
                                <?php require_once "../include/set_post_key.php";?>
                                <input type='hidden' name='team_id' value=<?php echo $team_id ?>>
                                <button class="btn btn-primary" type="submit" lay-submit="">修改</button>
                            </form>
                            <p style="font-weight:bold;margin:20px 0 20px 0;">团队人员总数:
                                <?php echo $ids ?>
                            </p>
                            <button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 10px;" onclick="addUser()">添加成员</button>
                            <form class="form-inline admin-search-bar" action="team_edit.php?id=<?php echo $team_id ?>">
                                <div class="form-group">
                                    <input type="hidden" name="id" value=<?php echo $team_id ?>>
                                    <input class="form-control" name="keyword" placeholder="请输入用户名或昵称搜索">
                                </div>
                                <button class="btn btn-primary" type="submit">搜索</button>
                            </form>
                            <table class="table table-striped">
                                <thead>
                                    <tr class="toprow">
                                        <td>ID</td>
                                        <td>昵称</td>
                                        <td>操作</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
foreach ($result as $row) {
    $user_id = $row['user_id'];
    $nick = $row['nick'];
    echo "<tr>";
    echo "<td>$user_id</td>";
    echo "<td>$nick</td>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        echo "<td><a class='btn btn-sm btn-danger' href='javascript:void(0);' onclick=\"deleteTeamUser('$team_id','$user_id')\">删除</a></td>";
    }
    echo "</tr>";
}
?>

                                </tbody>
                            </table>

                            <?php
if (!(isset($_GET['keyword']) && $_GET['keyword'] != "")) {
    echo "<div style='display:inline;'>";
    echo "<nav class='center'>";
    echo "<ul class='pagination pagination-sm'>";
    echo "<li class='page-item'><a href='contest_list.php?page=" . (strval(1)) . "'>&lt;&lt;</a></li>";
    echo "<li class='page-item'><a href='contest_list.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "'>&lt;</a></li>";
    for ($i = $spage; $i <= $epage; $i++) {
        echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='contest_list.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
    }
    echo "<li class='page-item'><a href='contest_list.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "'>&gt;</a></li>";
    echo "<li class='page-item'><a href='contest_list.php?page=" . (strval($pages)) . "'>&gt;&gt;</a></li>";
    echo "</ul>";
    echo "</nav>";
    echo "</div>";
}
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'js.php';?>
        <script>
            layui.use(['element', 'layer', 'form'], function () {
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
            function addUser() {
                layer.open({
                    type: 1,
                    area: ['500px', '750px'],
                    title: '添加团队成员',
                    content: '<form method="POST" action="team_edit.php" class="admin-layer-form layui-form">'
                        + '<div class="form-group">'
                        + '<label for="ulist">添加团队成员</label>'
                        + '<textarea name="ulist" rows="35" style="width:100%;" placeholder="每一行对应一个用户ID，若对应账号存在则加入团队，不存在则无视之"></textarea>'
                        + '</div>'
                        + '<input type="hidden" name="team_id" value=<?php echo $team_id ?>>'
                        + '<input type="hidden" name="postkey" value="<?php echo $_SESSION[$OJ_NAME . '_' . 'postkey'] ?>">'
                            + '<?php require "../csrf.php";?>'
                            + '<button class="btn btn-primary" type="submit" lay-submit="">提交</button>'
                            + '</form>'
                })
            }
            function deleteTeamUser(teamID, userID, teamName) {
                layer.confirm('确定要删除将' + userID + '从<?php echo $name ?>移除吗？该用户将会从该团队参赛的团队赛的全部参赛名单中删去！', { icon: 3, title: '提示', offset: '300px' }, function (index) {
                    window.location.href = 'team_user_del.php?tid=' + teamID + '&uid=' + userID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
                    layer.close(index);
                });
            }
        </script>
    </body>

    </html>