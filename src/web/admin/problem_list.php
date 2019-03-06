<?php
require "admin-header.php";
require_once "../include/set_get_key.php";

$sql = "SELECT COUNT('problem_id') AS ids FROM `problem`";
$result = pdo_query($sql);
$row = $result[0];

$ids = intval($row['ids']);

$idsperpage = 25;
$pages = max(intval(ceil($ids / $idsperpage)), 1);

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}

$pagesperframe = 5;
$frame = intval(ceil($page / $pagesperframe));

$spage = ($frame - 1) * $pagesperframe + 1;
$epage = min($spage + $pagesperframe - 1, $pages);

$sid = ($page - 1) * $idsperpage;

$sql = "";
if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
    $keyword = $_GET['keyword'];
    $keyword = "%$keyword%";
    $sql = "SELECT `problem_id`,`title`,`accepted`,`in_date`,`defunct` FROM `problem` WHERE (problem_id LIKE ?) OR (title LIKE ?) OR (description LIKE ?) OR (source LIKE ?)";
    $result = pdo_query($sql, $keyword, $keyword, $keyword, $keyword);
} else {
    $sql = "SELECT `problem_id`,`title`,`accepted`,`in_date`,`defunct` FROM `problem` ORDER BY `problem_id` DESC LIMIT $sid, $idsperpage";
    $result = pdo_query($sql);
}
?>

<!DOCTYPE html>


<html>
<?php require_once 'admin-header.php'?>
<title>问题列表</title>
<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <?php require_once 'top_menubar.php';?>
        <?php require_once 'side_menubar.php';?>
        <div class="layui-body">
            <h3 class="sub-page-title">
                问题列表
            </h3>
            <div class='container'>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action=problem_list.php class="form-inline admin-search-bar">
                            <div class="form-group">
                                <input name="keyword" class="form-control" placeholder="请输入题目ID或名称搜索">
                            </div>
                            <button class="btn btn-primary" type="submit">搜索</button>
                        </form>
                        <table class="table table-striped">
                            <form method="post" action="contest_add.php">
                                <tr class="toprow">
                                    <td width=60px>ID
                                        <input type=checkbox style='vertical-align:2px;' onchange='$("input[type=checkbox]").prop("checked", this.checked)'>
                                    </td>
                                    <td>标题</td>
                                    <td>通过数</td>
                                    <td>更新时间</td>
                                    <?php
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor'])) {
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        echo "<td>状态</td>";
    }
    echo "<td>操作</td>";
}
?>
                                </tr>
                                <?php
foreach ($result as $row) {
    $problem_id = $row['problem_id'];
    $title = $row['title'];
    $accepted = $row['accepted'];
    $in_date = $row['in_date'];
    $defunct = $row['defunct'];
    $getkey = $_SESSION[$OJ_NAME . '_' . 'getkey'];

    echo "<tr>";
    echo "<td>$problem_id <input type='checkbox' style='vertical-align:2px;' name='pid[]' value='$problem_id'></td>";
    echo "<td><a href='../problem.php?id=$problem_id'>$title</a></td>";
    echo "<td>$accepted</td>";
    echo "<td>$in_date</td>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor'])) {
        if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
            echo "<td><a  class='btn btn-sm " . ($defunct == "N" ? "btn-success" : "btn-danger") . "' href=problem_df_change.php?id=$problem_id&getkey=$getkey>" . ($defunct == "N" ? "可用" : "保留") . "</a>";
        }
        echo "<td>";
        echo "<a class='btn btn-sm btn-success' href=problem_edit.php?id=$problem_id&getkey=$getkey>编辑</a>";
        if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
            if (function_exists("system")) {
                echo "<a class='btn btn-sm btn-danger' href='javascript:void(0);' onclick=\"deleteProblem($problem_id,'$title')\">删除</a>";
            }
        }
        echo "<a class='btn btn-sm btn-primary' href='javascript:phpfm($problem_id);'>测试数据</a>";
        echo "</td>";
    }
    echo "</tr>";
}
?>
                                    <tr>
<?php
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])) {
    ?>
                                        <td colspan=2 style="height:40px;">选择去</td>
                                        <td colspan=6>
                                            <?php require_once "../include/set_post_key.php";?>
                                            <input class="btn btn-primary" type="submit" name="problem2contest" value="新建竞赛作业">
                                            <?php
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        ?>
                                            <input class="btn btn-primary" type="submit" name="enable" value="可用" onclick="$('form').attr('action','problem_df_change.php')">
                                            <input class="btn btn-primary" type="submit" name="disable" value="保留" onclick="$('form').attr('action','problem_df_change.php')">
                                            <?php
}
    ?>
                                        </td>
<?php
}
?>
                                    </tr>
                            </form>
                        </table>

                    </div>
                </div>

                <?php
if (!(isset($_GET['keyword']) && $_GET['keyword'] != "")) {
    echo "<div style='display:inline;'>";
    echo "<nav class='center'>";
    echo "<ul class='pagination pagination-sm'>";
    echo "<li class='page-item'><a href='problem_list.php?page=" . (strval(1)) . "'>&lt;&lt;</a></li>";
    echo "<li class='page-item'><a href='problem_list.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "'>&lt;</a></li>";
    for ($i = $spage; $i <= $epage; $i++) {
        echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='problem_list.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
    }
    echo "<li class='page-item'><a href='problem_list.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "'>&gt;</a></li>";
    echo "<li class='page-item'><a href='problem_list.php?page=" . (strval($pages)) . "'>&gt;&gt;</a></li>";
    echo "</ul>";
    echo "</nav>";
    echo "</div>";
}
?>

            </div>

        </div>
    </div>
    <?php require_once 'js.php';?>
    <script>
        layui.use(['element','layer'], function () {
            var element = layui.element,
            layer = layui.layer;
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
        function phpfm(pid) {
            //alert(pid);
            $.post("phpfm.php", { 'frame': 3, 'pid': pid, 'pass': '' }, function (data, status) {
                if (status == "success") {
                    document.location.href = "phpfm.php?frame=3&pid=" + pid;
                }
            });
        }
        function deleteProblem(problemID, problemName) {
            layer.confirm('确定要删除' + problemID + problemName + '吗？这会删除该题目全部的评测记录和代码，请慎用该功能！', { icon: 3, title: '提示', offset: '300px' }, function (index) {
                window.location.href = 'problem_del.php?id=' + problemID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>'
                layer.close(index);
            });
        }

    </script>
</body>

</html>