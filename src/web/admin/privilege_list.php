<?php
require "admin-header.php";
require_once "../include/set_get_key.php";

if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$sql = "SELECT COUNT(*) AS ids FROM privilege WHERE rightstr IN ('administrator','source_browser','contest_creator','http_judge','problem_editor','password_setter','printer','balloon') ORDER BY user_id, rightstr";
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
    $sql = "SELECT * FROM privilege WHERE (user_id LIKE ?) OR (rightstr LIKE ?) ORDER BY user_id, rightstr";
    $result = pdo_query($sql, $keyword, $keyword);
} else {
    $sql = "SELECT * FROM privilege WHERE rightstr IN ('administrator','source_browser','contest_creator','http_judge','problem_editor','password_setter','printer','balloon') ORDER BY user_id, rightstr LIMIT $sid, $idsperpage";
    $result = pdo_query($sql);
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>权限列表</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">权限列表</h3>
      <div class='container'>
        <div class="panel panel-default">
          <div class="panel-body">
            <form action="privilege_list.php" class="form-inline admin-search-bar">
              <div class="form-group">
                <input class="form-control" name="keyword" placeholder="请输入用户名或权限名搜索">
              </div>
              <button type="submit" class="btn btn-primary">提交</button>
            </form>
            <table class="table table-striped">
              <thead>
                <tr class="toprow">
                  <td>用户ID</td>
                  <td>权限</td>
                  <td>操作</td>
                </tr>
              </thead>
              <tbody>
              <?php
foreach ($result as $row) {
    $user_id = $row['user_id'];
    $rightstr = $row['rightstr'];

    echo "<tr>";
    echo "<td>$user_id</td>";
    echo "<td>$rightstr</td>";
    echo "<td><a class='btn btn-sm btn-danger' " . ($rightstr == 'administrator' ? 'disabled' : '') . " href='javascript:void(0);' onclick=\"deletePrivilege('$user_id','$rightstr')\">移除</a></td>";
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
    echo "<li class='page-item'><a href='privilege_list.php?page=" . (strval(1)) . "'>&lt;&lt;</a></li>";
    echo "<li class='page-item'><a href='privilege_list.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "'>&lt;</a></li>";
    for ($i = $spage; $i <= $epage; $i++) {
        echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='privilege_list.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
    }
    echo "<li class='page-item'><a href='privilege_list.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "'>&gt;</a></li>";
    echo "<li class='page-item'><a href='privilege_list.php?page=" . (strval($pages)) . "'>&gt;&gt;</a></li>";
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
    echo "layer.msg('操作成功');\n";
} else if ($status == $OPERATOR_FAILURE) {
    $msg = $_SESSION['error_message'];
    echo "layer.msg('$msg',{time:5000});\n";
}
?>
    });
    function deletePrivilege(userId, rightStr) {
      layer.confirm('确定要删除' + userId + '的' + rightStr + '权限吗?', { icon: 3, title: '提示' }, function (index) {
        window.location.href = 'privilege_del.php?uid=' + userId + '&rightstr=' + rightStr + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
        layer.close(index);
      });
    }
  </script>
</body>

</html>