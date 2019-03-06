<?php
require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

if (isset($_POST['name'])) {
    require_once "../include/check_post_key.php";
    $series_id = intval($_POST['series_id']);
    $name = $_POST['name'];
    $sql = "SELECT `name` FROM `series` WHERE `series_id`=?";
    $result = pdo_query($sql, $series_id);
    $row = $result[0];
    $old_name = $row['name'];
    if (get_magic_quotes_gpc()) {
        $name = stripslashes($name);
    }
    $name = RemoveXSS($name);
    if ($name != $old_name) {
        $sql = "UPDATE `series` SET `name`=? WHERE `series_id`=?";
        //echo $sql;
        if (!pdo_query($sql, $name, $series_id)) {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "系列赛名称必须唯一";
        }
    }
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("location:series_edit.php?id=$series_id");
    exit(0);

} else if (isset($_POST['clist'])) {
    require_once "../include/check_post_key.php";
    $series_id = intval($_POST['series_id']);

    $pieces = explode("\n", trim($_POST['clist']));
    if (count($pieces) > 0 && strlen($pieces[0]) > 0) {
        echo count($pieces);
        $sql_general = "INSERT INTO `contest_series`(`contest_id`,`series_id`) VALUES (?,?)";
        $sql_update_count = "UPDATE `series` SET contest_count = contest_count+1 where series_id=?";
        for ($i = 0; $i < count($pieces); $i++) {
            $contest_id = trim($pieces[$i]);
            $can_insert = true;

            // 判断竞赛是否存在
            $sql = "SELECT * FROM `contest` WHERE contest_id = ?";
            $result = pdo_query($sql, $contest_id);
            if (count($result) < 1) {
                $can_insert = false;
            }

            // 判断竞赛是否已经在系列赛中
            $sql = "SELECT * FROM `contest_series` WHERE series_id = ? and contest_id = ?";
            $result = pdo_query($sql, $series_id, $contest_id);
            if (count($result) > 0) {
                $can_insert = false;
            }

            if ($can_insert) {
                pdo_query($sql_general, $contest_id, $series_id);
                pdo_query($sql_update_count, $series_id);
            }
        }
    }
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("location:series_edit.php?id=$series_id");
    exit(0);

} else {
    $series_id = intval($_GET['id']);
    $sql = "SELECT * FROM `series` WHERE series_id=?";
    $result = pdo_query($sql, $series_id);

    if (count($result) != 1) {
        header("location:404.php");
        exit(0);
    }
    $row = $result[0];
    $name = htmlentities($row['name'], ENT_QUOTES, "UTF-8");

    $sql = "SELECT COUNT('contest_id') AS ids FROM `contest_series` INNER JOIN `contest` ON `contest`.contest_id = `contest_series`.contest_id WHERE `contest_series`.series_id = ?";
    $result = pdo_query($sql, $series_id);
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
        $sql = "SELECT * FROM `contest` INNER JOIN `contest_series` ON `contest`.contest_id = `contest_series`.contest_id WHERE `contest_series`.series_id=? AND (`contest`.contest_id like ? OR `contest`.nick like ?)";
        $result = pdo_query($sql, $series_id, $keyword, $keyword);
    } else {

        $sql = "SELECT * FROM `contest` INNER JOIN `contest_series` ON `contest`.contest_id = `contest_series`.contest_id WHERE `contest_series`.series_id=?";
        $result = pdo_query($sql, $series_id);
    }
}

?>
<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>

<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <?php require_once 'top_menubar.php';?>
        <?php require_once 'side_menubar.php';?>
        <div class="layui-body">
            <h3 class="sub-page-title">编辑系列赛</h3>
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form method="POST" action="series_edit.php" class="form-inline layui-form">
                            <div class="form-group">
                                <label for="name">系列赛名称</label>
                                <input class="form-control" type="text" name="name" id="name" value='<?php echo $name ?>' lay-verify="required" placeholder="请输入系列赛名称">
                            </div>
                            <?php require_once "../include/set_post_key.php";?>
                            <input type="hidden" name="series_id" value="<?php echo $series_id ?>">
                            <button class="btn btn-primary" type="submit">修改</button>
                        </form>
                        <p style="font-weight:bold;margin:20px 0 20px 0;">系列赛包含竞赛&作业总数: <?php echo $ids ?></p>
                        <button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 10px;" onclick="addContest()">添加竞赛&作业</button>
                        <form class="form-inline admin-search-bar" action="series_edit.php?id=<?php echo $series_id ?>">
                            <div class="form-group">
                                <input type="hidden" name="id" value="<?php echo $series_id ?>">
                                <input class="form-control" name="keyword" placeholder="请输入竞赛&作业标题搜索">
                            </div>
                            <button class="btn btn-primary" type="submit">搜索</button>
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr class="toprow">
                                    <td>ID</td>
                                    <td>标题</td>
                                    <td>模式</td>
                                    <td>操作</td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
foreach ($result as $row) {
    $contest_id = $row['contest_id'];
    $title = $row['title'];
    $team_mode = $row['team_mode'];
    echo "<tr>";
    echo "<td>$contest_id</td>";
    echo "<td>$title</td>";
    echo "<td>" . ($team_mode ? '团队' : '个人') . "</td>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        echo "<td><a class='btn btn-sm btn-danger' href='javascript:void(0);' onclick=\"deleteSeriesContest('$series_id','$contest_id','$title')\">删除</a></td>";
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
        function addContest(){
            layer.open({
                type:1,
                area:['500px','750px'],
                title: '添加竞赛&作业',
                content:'<form method="POST" action="series_edit.php" class="admin-layer-form layui-form">'
                            +'<div class="form-group">'
                            +'<label for="clist">添加竞赛&作业</label>'
                            +'<textarea name="clist" rows="35" style="width:100%;" placeholder="每一行对应一个竞赛ID，若对应竞赛存在则加入系列赛，不存在则无视之"><?php if (isset($clist)) {echo $clist;}?></textarea>'
                            +'</div>'
                            +'<input type="hidden" name="series_id" value=<?php echo $series_id ?>>'
                            +'<input type="hidden" name="postkey" value="<?php echo $_SESSION[$OJ_NAME . '_' . 'postkey'] ?>">'
                            +'<?php require "../csrf.php";?>'
                            +'<button class="btn btn-primary" type="submit" lay-submit="">提交</button>'
                        +'</form>',
            })
        }
        function deleteSeriesContest(seriesID, contestID, contestTitle) {
            layer.confirm('确定要删除将' + contestTitle + '从' + '<?php echo $name ?>' + '移除吗?', { icon: 3, title: '提示', offset: '100px' }, function (index) {
                window.location.href = 'series_contest_del.php?sid=' + seriesID + '&cid=' + contestID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
                layer.close(index);
            });
        }
    </script>
</body>

</html>