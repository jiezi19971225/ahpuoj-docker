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

if (isset($_POST['ulist'])) {
    var_dump($contest_id);
    require_once "../include/check_post_key.php";
    $contest_id = intval($_POST['contest_id']);
    $rightstr = "c$contest_id";

    $sql = "SELECT * FROM `contest` WHERE contest_id=?";
    $result = pdo_query($sql, $contest_id);
    var_dump($result);
    if (count($result) != 1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "竞赛不存在";
        header("location:contest_add_user.php?cid=$contest_id");
        exit(0);
    }
    $row = $result[0];

    $team_mode = $row['team_mode'];
    if ($team_mode != '0') {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "无法访问该页面";
        header('Location:contest_list.php');
    }

    $pieces = explode("\n", trim($_POST['ulist']));
    if (count($pieces) > 0 && strlen($pieces[0]) > 0) {
        echo count($pieces);
        $sql_general = "INSERT INTO `privilege`(`user_id`,`rightstr`) VALUES (?,?)";
        for ($i = 0; $i < count($pieces); $i++) {
            $user_id = trim($pieces[$i]);
            $can_insert = true;

            // 判断用户是否存在
            $sql = "SELECT * FROM `users` WHERE user_id = ?";
            $result = pdo_query($sql, $user_id);
            if (count($result) < 1) {
                $can_insert = false;
            }

            // 判断用户是否已经拥有权限
            $sql = "SELECT * FROM `privilege` WHERE user_id = ? and rightstr = ?";
            $result = pdo_query($sql, $user_id, $rightstr);
            if (count($result) > 0) {
                $can_insert = false;
            }

            if ($can_insert) {
                pdo_query($sql_general, $user_id, $rightstr);
            }
        }
    }
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("location:contest_add_user.php?cid=$contest_id");
    exit(0);

} else {
    $rightstr = "c$contest_id";

    $sql = "SELECT * FROM `contest` WHERE contest_id=?";
    $result = pdo_query($sql, $contest_id);

    if (count($result) != 1) {
        header("location:404.php");
        exit(0);
    }
    $row = $result[0];

    $team_mode = $row['team_mode'];
    if ($team_mode != '0') {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "无法访问该页面";
        header('Location:contest_list.php');
    }

    $title = htmlentities($row['title'], ENT_QUOTES, "UTF-8");

    $sql = "SELECT COUNT('user_id') AS ids FROM `privilege` INNER JOIN `users` ON `users`.user_id = `privilege`.user_id WHERE `privilege`.rightstr = ?";

    $result = pdo_query($sql, $rightstr);
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
        $sql = "SELECT * FROM `users` INNER JOIN `privilege` ON `users`.user_id = `privilege`.user_id WHERE `privilege`.rightstr=? AND (`users`.user_id like ? OR `users`.nick like ?)";
        $result = pdo_query($sql, $rightstr, $keyword, $keyword);
    } else {
        $sql = "SELECT * FROM `users` INNER JOIN `privilege` ON `users`.user_id = `privilege`.user_id WHERE `privilege`.rightstr=?";
        $result = pdo_query($sql, $rightstr);
    }
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title class="sub-page-title">竞赛&作业人员设置</title>
<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <?php require_once 'top_menubar.php';?>
        <?php require_once 'side_menubar.php';?>
        <div class="layui-body">
            <h3 class="sub-page-title">竞赛&作业人员设置</h3>
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p><b>竞赛&作业名称: <?php echo $title ?></b></p>
                        <p style="font-weight:bold;margin:20px 0 20px 0;">竞赛&作业人员总数: <?php echo $ids ?></p>
                        <button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 10px;" onclick="addUser()">添加人员</button>
                        <form class="form-inline admin-search-bar" action="contest_add_user.php?cid=<?php echo $contest_id ?>">
                            <div class="form-group">
                                <input type='hidden' name='cid' value=<?php echo $contest_id ?>>
                                <input class="form-control" name="keyword" placeholder="请输入用户名或昵称搜索">
                            </div>
                            <input class="btn btn-primary" type="submit" value="搜索">
                        </form>
                        <table class="table table-striped">
                            <tbody>
                                <thead>
                                    <tr>
                                        <td>ID</td>
                                        <td>昵称</td>
                                        <td>操作</td>
                                    </tr>
                                </thead>
                                <?php
foreach ($result as $row) {
    $user_id = $row['user_id'];
    $nick = $row['nick'];

    echo "<tr>";
    echo "<td>$user_id</td>";
    echo "<td>$nick</td>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        echo "<td><a class='btn btn-sm btn-danger' href='javascript:void(0);' onclick=\"deleteContestUser('$contest_id','$user_id')\">删除</a></td>";
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
        function addUser(){
            layer.open({
                type:1,
                area:['500px','750px'],
                title: '添加竞赛&作业人员',
                content:'<form method="POST" action="contest_add_user.php" class="admin-layer-form layui-form">'
                            +'<div class="form-group">'
                            +'<label for="ulist">添加竞赛&作业人员</label>'
                            +'<textarea name="ulist" rows="35" style="width:100%;" placeholder="每一行对应一个用户ID，若对应账号存在则加入竞赛&作业，不存在则无视之"></textarea>'
                            +'</div>'
                            +'<input type="hidden" name="contest_id" value=<?php echo $contest_id ?>>'
                            +'<?php require_once "../include/set_post_key.php";?>'
                            +'<?php require "../csrf.php";?>'
                            +'<button class="btn btn-primary" type="submit" lay-submit="">提交</button>'
                        +'</form>'
            })
        }
        function deleteContestUser(contestID, userID) {
            layer.confirm('确定要删除将' + userID + '从<?php echo $title ?>移除吗?', { icon: 3, title: '提示', offset: '300px' }, function (index) {
                window.location.href = 'contest_user_del.php?cid=' + contestID + '&uid=' + userID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
                layer.close(index);
            });
        }
    </script>
</body>

</html>