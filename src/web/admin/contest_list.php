<?php
require "admin-header.php";
require_once "../include/set_get_key.php";

if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

$sql = "SELECT COUNT('contest_id') AS ids FROM `contest`";
$result = pdo_query($sql);
$row = $result[0];

$ids = intval($row['ids']);

$idsperpage = 10;
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
    $sql = "SELECT `contest_id`,`title`,`start_time`,`end_time`,`private`,`defunct` FROM `contest` WHERE (title LIKE ?) OR (description LIKE ?) ORDER BY `contest_id` DESC";
    $result = pdo_query($sql, $keyword, $keyword);
} else {
    $sql = "SELECT `contest_id`,`title`,`start_time`,`end_time`,`private`,`team_mode`,`defunct` FROM `contest` ORDER BY `contest_id` DESC LIMIT $sid, $idsperpage";
    $result = pdo_query($sql);
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>竞赛&作业列表</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">竞赛&作业列表</h3>
      <div class='container'>
        <div class="panel panel-default">
          <div class="panel-body">
            <form action=contest_list.php class="form-inline admin-search-bar">
              <div class="form-group">
                <input class="form-control" name="keyword" placeholder="请输入竞赛&作业标题搜索">
              </div>
              <button class="btn btn-primary" type="submit">搜索</button>
            </form>
            <table class="table table-striped">
              <thead>
                <tr class="toprow">
                  <td style="width:5%;">ID</td>
                  <td>标题</td>
                  <td style="width:10%;">开始时间</td>
                  <td style="width:10%;">结束时间</td>
                  <td style="width:5%;">公开度</td>
                  <td style="width:5%;">模式</td>
                  <td style="width:5%;">状态</td>
                  <td style="width:25%;">操作</td>
                </tr>
              </thead>
              <tbody>
              <?php
foreach ($result as $row) {
    $contest_id = $row['contest_id'];
    $title = $row['title'];
    $start_time = $row['start_time'];
    $end_time = $row['end_time'];
    $private = $row['private'];
    $team_mode = $row['team_mode'];
    $defunct = $row['defunct'];
    $getkey = $_SESSION[$OJ_NAME . '_' . 'getkey'];

    echo "<tr>";
    echo "<td>$contest_id</td>";
    echo "<td><a href='../contest.php?$contest_id='>$title</a></td>";
    echo "<td>$start_time</td>";
    echo "<td>$end_time</td>";

    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . "m$contest_id"])) {
        echo "<td><a  class='btn btn-sm " . ($private == "0" ? "btn-success" : "btn-danger") . "' href='contest_pr_change.php?cid=$contest_id&getkey=$getkey'>" . ($private == "0" ? "公开" : "私有") . "</a></td>";
        echo "<td><a  class='btn btn-sm " . ($team_mode == "0" ? "btn-success" : "btn-info") . "' onclick=\"changeContestMode($contest_id,'$title')\"" . ">" . ($team_mode == "0" ? "个人" : "团队") . "</a></td>";
        echo "<td><a  class='btn btn-sm " . ($defunct == "N" ? "btn-success" : "btn-danger") . "' href='contest_df_change.php?cid=$contest_id&getkey=$getkey'>" . ($defunct == "N" ? "可用" : "保留") . "</a></td>";
    } else {
        echo "<td></td><td></td><td></td>";
    }

    echo "<td>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . "m$contest_id"])) {
        echo "<a class='btn btn-sm btn-success' href='contest_edit.php?cid=$contest_id'>编辑</a>";
        echo "<a class='btn btn-sm btn-info' href='contest_add_" . ($row['team_mode'] == "0" ? "user" : "team") . ".php?cid=$contest_id'>人员</a>";
        echo "<a class='btn btn-sm btn-warning' href='contest_add.php?cid=$contest_id'>复制</a>";
        if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
            echo "<a class='btn btn-sm btn-danger' href='javascript:void(0);' onclick=\"deleteContest($contest_id,'$title')\">删除</a>";
            echo "<a class='btn btn-sm btn-primary' class='btn btn-sm btn-primary' href=\"problem_export_xml.php?cid=$contest_id&getkey=$getkey\">导出</a>";
        }
        echo "<a class='btn btn-sm btn-info' href='../export_contest_code.php?cid=$contest_id&getkey=$getkey'>日志</a>";
    } else {
        echo "<a class='btn btn-sm btn-warning' href='contest_add.php?cid=$contest_id'>复制</a>";
    }

    echo "<a class='btn btn-sm btn-default' href='suspect_list.php?cid=$contest_id'>监视</a>";
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
    function changeContestMode(contestID, contestTitle) {
      layer.confirm('确定要更改' + contestTitle + '的模式吗？更改模式后私有比赛的参赛人员需要重新进行设置！', { icon: 3, title: '提示' }, function (index) {
        window.location.href = 'contest_mode_change.php?cid=' + contestID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>'
        layer.close(index);
      });
    }

    function deleteContest(contestID, contestTitle) {
      layer.confirm('确定要删除' + contestTitle + '吗?', { icon: 3, title: '提示' }, function (index) {
        window.location.href = 'contest_del.php?id=' + contestID + '&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>'
        layer.close(index);
      });
    }
  </script>
</body>

</html>