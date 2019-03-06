<?php
require "admin-header.php";
require_once "../include/set_get_key.php";

if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$sql = "SELECT COUNT('series_id') AS ids FROM `series`";
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
    $sql = "SELECT * FROM `series` WHERE (`name` LIKE ?) ORDER BY `series_id` DESC";
    $result = pdo_query($sql, $keyword);
} else {
    $sql = "SELECT * FROM `series` ORDER BY `series_id` DESC LIMIT $sid, $idsperpage";
    $result = pdo_query($sql);
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>系列赛列表</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">系列赛列表</h3>
      <div class='container'>
        <div class="panel panel-default">
          <div class="panel-body">
            <button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 10px;" onclick="createSeries()">创建系列赛</button>
            <form action=team_list.php class="form-inline admin-search-bar">
              <div class="form-group">
                <input class="form-control" name="keyword" placeholder="请输入系列赛名称搜索">
              </div>
              <button type="submit" class="btn btn-primary">搜索</button>
            </form>
            <table width="100%" class="table table-striped">
              <thead>
                <tr class="toprow">
                  <td>ID</td>
                  <td>名称</td>
                  <td>包含竞赛&作业总数</td>
                  <td>模式</td>
                  <td>操作</td>
                </tr>
              </thead>
              <tbody>
              <?php
foreach ($result as $row) {
    $series_id = $row['series_id'];
    $name = $row['name'];
    $contest_count = $row['contest_count'];
    $team_mode = $row['team_mode'];

    echo "<tr>";
    echo "<td>$series_id</td>";
    echo "<td><a href='../series.php?sid=$series_id'>$name</a></td>";
    echo "<td>$contest_count</td>";
    echo "<td><a  class='btn btn-sm " . ($team_mode == "0" ? "btn-success" : "btn-info") . "' onclick=\"changeSeriesMode($series_id,'$name')\"" . ">" . ($team_mode == "0" ? "个人" : "团队") . "</a></td>";
    echo "<td>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        echo "<a class='btn btn-sm btn-success'  href=series_edit.php?id=$series_id>编辑</a>";
        echo "<a class='btn btn-sm btn-danger' href=\"javascript:void(0);\" onclick=\"deleteSeries($series_id,'$name')\">删除</a>";
    }
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
    echo "<li class='page-item'><a href='series_list.php?page=" . (strval(1)) . "'>&lt;&lt;</a></li>";
    echo "<li class='page-item'><a href='series_list.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "'>&lt;</a></li>";
    for ($i = $spage; $i <= $epage; $i++) {
        echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='series_list.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
    }
    echo "<li class='page-item'><a href='series_list.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "'>&gt;</a></li>";
    echo "<li class='page-item'><a href='series_list.php?page=" . (strval($pages)) . "'>&gt;&gt;</a></li>";
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
    function createSeries(){
      layer.open({
        type:1,
        area:['400px','300px'],
        title: '创建系列赛',
        content:'<form method="POST" action="series_add.php" class="admin-layer-form layui-form">'
                    +'<div class="form-group">'
                    +'<label for="series">系列赛名称</label>'
                    +'<input class="form-control" type="text" id="series" lay-verify="required" name="name" value="" placeholder="请输入系列赛名称">'
                    +'</div>'
                    +'<div class="form-group">'
                    +'<label for="team_mode">系列赛模式</label>'
                    +'<select class="form-control" name="team_mode" lay-ignore>'
                    +'<option value="0">个人</option>'
                    +'<option value="1">团队</option>'
                    +'</select>'
                    +'</div>'
                    +'<?php require_once "../include/set_post_key.php";?>'
                    +'<?php require "../csrf.php";?>'
                    +'<button class="btn btn-primary" type="submit" lay-submit="">提交</button>'
                +'</form>'
      })
    }
    function changeSeriesMode(seriesID, seriesName) {
      layer.confirm('确定要更改' + seriesName + '的模式吗？系列赛页面中只会显示与系列赛模式相同模式的竞赛&作业！', { icon: 3, title: '提示' }, function (index) {
        window.location.href = 'series_mode_change.php?sid=' + seriesID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>'
        layer.close(index);
      });
    }
    function deleteSeries(seriesID, seriesName) {
      layer.confirm('确定要删除' + seriesName + '吗?', { icon: 3, title: '提示' }, function (index) {
        window.location.href = 'series_del.php?id=' + seriesID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>'
        layer.close(index);
      });
    }
  </script>
</body>

</html>