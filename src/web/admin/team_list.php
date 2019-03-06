<?php
require "admin-header.php";
require_once "../include/set_get_key.php";

if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$sql = "SELECT COUNT('team_id') AS ids FROM `teams` WHERE id_delete = 'N'";
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
    $sql = "SELECT `team_id`,`name`,`user_count`,`reg_time` FROM `teams` WHERE (`name` LIKE ?) AND `is_delete` = 'N' ORDER BY `team_id` DESC";
    $result = pdo_query($sql, $keyword);
} else {
    $sql = "SELECT `team_id`,`name`,`user_count`,`reg_time` FROM `teams` WHERE `is_delete` = 'N' ORDER BY `team_id`  DESC LIMIT $sid, $idsperpage";
    $result = pdo_query($sql);
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>团队列表</title>
<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">团队列表</h3>
      <div class='container'>
        <div class="panel panel-default">
          <div class="panel-body">
            <button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 10px;" onclick="createTeam()">创建团队</button>
            <form action="team_list.php" class="form-inline admin-search-bar">
              <div class="form-group">
                <input class="form-control" name="keyword" placeholder="请输入团队名称搜索">
              </div>
              <button type="submit" class="btn btn-primary">搜索</button>
            </form>
            <table class="table table-striped">
              <thead>
                <tr class="toprow">
                  <td>ID</td>
                  <td>名称</td>
                  <td>成员总数</td>
                  <td>注册时间</td>
                  <td>操作</td>
                </tr>
              </thead>
              <tbody>
<?php
foreach ($result as $row) {
    $team_id = $row['team_id'];
    $name = $row['name'];
    $user_count = $row['user_count'];
    $reg_time = $row['reg_time'];

    echo "<tr>";
    echo "<td>$team_id</td>";
    echo "<td>$name</td>";
    echo "<td>$user_count</td>";
    echo "<td>$reg_time</td>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        echo "<td><a class='btn btn-sm btn-success' href=team_edit.php?id=$team_id>编辑</a>";
        echo "<a class='btn btn-sm btn-danger' href=\"javascript:void(0);\" onclick=\"deleteTeam($team_id,'$name')\">删除</a></td>";
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
    echo "<li class='page-item'><a href='team_list.php?page=" . (strval(1)) . "'>&lt;&lt;</a></li>";
    echo "<li class='page-item'><a href='team_list.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "'>&lt;</a></li>";
    for ($i = $spage; $i <= $epage; $i++) {
        echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='team_list.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
    }
    echo "<li class='page-item'><a href='team_list.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "'>&gt;</a></li>";
    echo "<li class='page-item'><a href='team_list.php?page=" . (strval($pages)) . "'>&gt;&gt;</a></li>";
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
    layui.use(['element','layer','form'], function () {
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
    function createTeam(){
      layer.open({
        type:1,
        area:['400px','250px'],
        title: '创建团队',
        content:'<form method="POST" action="team_add.php" class="admin-layer-form layui-form">'
                    +'<div class="form-group">'
                      +'<label for="team_name">团队名称</label>'
                      +'<input class="form-control" type="text" id="team_name" lay-verify="required" name="name" value="" placeholder="请输入团队名称">'
                    +'</div>'
                    +'<?php require_once "../include/set_post_key.php";?>'
                    +'<?php require "../csrf.php";?>'
                    +'<button class="btn btn-primary" type="submit" lay-submit="">提交</button>'
                +'</form>'
      })
    }
    function deleteTeam(teamID, teamName) {
      layer.confirm('确定要删除' + teamName + '吗?', { icon: 3, title: '提示' }, function (index) {
        window.location.href = 'team_del.php?id=' + teamID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>'
        layer.close(index);
      });
    }
  </script>
</body>

</html>