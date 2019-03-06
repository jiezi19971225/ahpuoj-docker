<?php
require_once "admin-header.php";
require_once "../include/set_get_key.php";

if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$sql = "SELECT COUNT('news_id') AS ids FROM `news`";
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
    $sql = "SELECT `news_id`,`user_id`,`title`,`time`,`defunct` FROM `news` WHERE (title LIKE ?) OR (content LIKE ?) ORDER BY `news_id` DESC";
    $result = pdo_query($sql, $keyword, $keyword);
} else {
    $sql = "SELECT `news_id`,`user_id`,`title`,`time`,`defunct` FROM `news` ORDER BY `news_id` DESC LIMIT $sid, $idsperpage";
    $result = pdo_query($sql);
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>新闻列表</title>
<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">新闻列表</h3>
      <div class='container'>
        <div class="panel panel-default">
          <div class="panel-body">
            <form action=news_list.php class="form-inline admin-search-bar">
              <div class="form-group">
                <input class="form-control" name="keyword" placeholder="请输入新闻标题或内容搜索">
              </div>
              <button type="submit" class="btn btn-primary">搜索</button>
            </form>
            <table class="table table-striped">
              <thead>
                <tr class="toprow">
                  <td>ID</td>
                  <td>标题</td>
                  <td>更新时间</td>
                  <td>状态</td>
                  <td>操作</td>
                </tr>
              </thead>
              <tbody>
              <?php
foreach ($result as $row) {
    $news_id = $row['news_id'];
    $title = $row['title'];
    $time = $row['time'];
    $defunct = $row['defunct'];
    $getkey = $_SESSION[$OJ_NAME . '_' . 'getkey'];
    echo "<tr>";
    echo "<td>$news_id</td>";
    echo "<td>$title </td>";
    echo "<td>$time</td>";
    echo "<td><a class='btn btn-sm " . ($row['defunct'] == "N" ? "btn-success" : "btn-danger") . "' href='news_df_change.php?id=$news_id&getkey=$getkey'>" . ($defunct == "N" ? "显示" : "隐藏") . "</a></td>";
    echo "<td>";
    echo "<a class='btn btn-success btn-sm' href=news_edit.php?id=$news_id>编辑</a>";
    echo "<a class='btn btn-primary btn-sm' href=news_add.php?cid=$news_id>复制</a>";
    echo "<a class='btn btn-danger btn-sm' href='javascript:void(0);' onclick=\"deleteNews($news_id,'$title')\">删除</a>";
    echo "</td>";
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
    echo "<li class='page-item'><a href='news_list.php?page=" . (strval(1)) . "'>&lt;&lt;</a></li>";
    echo "<li class='page-item'><a href='news_list.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "'>&lt;</a></li>";
    for ($i = $spage; $i <= $epage; $i++) {
        echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='news_list.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
    }
    echo "<li class='page-item'><a href='news_list.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "'>&gt;</a></li>";
    echo "<li class='page-item'><a href='news_list.php?page=" . (strval($pages)) . "'>&gt;&gt;</a></li>";
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
    layui.use(['element','form'], function () {
      var element = layui.element,
      layer = layui.layer;
      <?php
$status = flash_status_session();
if ($status == $OPERATOR_SUCCESS) {
    echo "layer.msg('操作成功');\n";
} else if ($status == $OPERATOR_FAILURE) {
    $msg = $_SESSION['error_message'];
    echo "layer.msg('$msg',5000);\n";
}
?>
    });
    function deleteNews(newsID,newsTitle){
      layer.confirm('确定要删除'+newsTitle+'吗?', {icon: 3, title:'提示'}, function(index){
        window.location.href='news_del.php?id='+newsID+'&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>';
        layer.close(index);
      });
    }
  </script>
</body>

</html>