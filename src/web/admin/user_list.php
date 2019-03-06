<?php
require_once "admin-header.php";
require_once "../include/set_get_key.php";
if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$sql = "SELECT COUNT('user_id') AS ids FROM `users`";
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
    $sql = "SELECT `user_id`,`nick`,`reg_time`,`ip`,`school`,`defunct` FROM `users` WHERE (user_id LIKE ?) OR (nick LIKE ?) OR (school LIKE ?) ORDER BY `user_id` DESC";
    $result = pdo_query($sql, $keyword, $keyword, $keyword);
} else {
    $sql = "SELECT `user_id`,`nick`,`reg_time`,`ip`,`school`,`defunct` FROM `users` ORDER BY `reg_time` DESC LIMIT $sid, $idsperpage";
    $result = pdo_query($sql);
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>用户列表</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">用户列表</h3>
      <div class='container'>
        <div class="panel panel-default">
          <div class="panel-body">
            <form action="user_list.php" class="form-inline admin-search-bar">
              <div class="form-group">
                <input class="form-control" name="keyword" placeholder="请输入搜索内容">
              </div>
              <button type="submit" class="btn btn-primary">搜索</button>
            </form>
            <table width="100%" class="table table-striped">
              <thead>
                <tr class="toprow">
                  <td>ID</td>
                  <td>昵称</td>
                  <td>学校</td>
                  <td>注册时间</td>
                  <td>状态</td>
                </tr>
              </thead>
              <tbody>
              <?php
foreach ($result as $row) {
    $user_id = $row['user_id'];
    $nick = $row['nick'];
    $school = $row['school'];
    $reg_time = $row['reg_time'];
    $defunct = $row['defunct'];
    $getkey = $_SESSION[$OJ_NAME . '_' . 'getkey'];

    echo "<tr>";
    echo "<td><a href='../userinfo.php?user=$user_id'>$user_id</a></td>";
    echo "<td>$nick</td>";
    echo "<td>$school</td>";
    echo "<td>$reg_time</td>";
    echo "<td><a class='btn btn-sm " . ($defunct == "N" ? "btn-success" : "btn-danger") . "' href='user_df_change.php?cid=$user_id&getkey=$getkey'>" . ($row['defunct'] == "N" ? "可用" : "禁用") . "</a></td>";
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
    echo "<li class='page-item'><a href='user_list.php?page=" . (strval(1)) . "'>&lt;&lt;</a></li>";
    echo "<li class='page-item'><a href='user_list.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "'>&lt;</a></li>";
    for ($i = $spage; $i <= $epage; $i++) {
        echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='user_list.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
    }
    echo "<li class='page-item'><a href='user_list.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "'>&gt;</a></li>";
    echo "<li class='page-item'><a href='user_list.php?page=" . (strval($pages)) . "'>&gt;&gt;</a></li>";
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
    layui.use(['element','layer'], function () {
      var element = layui.element,
      layer = layui.layer;
<?php
$status = flash_status_session();
if ($status == $OPERATOR_SUCCESS) {
    echo "layer.msg('操作成功');";
}
?>
    });
  </script>
</body>

</html>