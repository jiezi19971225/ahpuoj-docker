<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>竞赛&作业</title>
  <?php include "template/$OJ_TEMPLATE/css.php";?>
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <?php include "template/$OJ_TEMPLATE/nav.php";?>
  <div class="main-content" id="contestset-page-content">
    <div class="general-form-wrapper">
      <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
          <form class="form-inline">
            <input class="form-control search-query noradius input-sm" name="keyword" type="text" placeholder="请输入竞赛&作业名称搜索">
            <button class="btn btn-primary noradius btn-sm" type="submit">
              <i class="glyphicon glyphicon-search"></i>
            </button>
          </form>
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <span class="table-tag bg-azure">系统时间</span>
          <span class="table-tag bg-blackish-green" id="nowdate"></span>
        </div>
      </div>
    </div>
    <table class='table table-striped'>
      <thead>
        <tr class="toprow">
          <td style="width:5%;">ID</td>
          <td>名称</td>
          <td style="width:30%;">状态</td>
          <td style="width:10%;">公开度</td>
          <td style="width:10%;">模式</td>
        </tr>
      </thead>
      <tbody>
        <?php
foreach ($contest as $row) {
    echo "<tr>";
    foreach ($row as $table_cell) {
        echo "<td>";
        echo "\t" . $table_cell;
        echo "</td>";
    }
    echo "</tr>";
}
?>
      </tbody>
    </table>
    <ul class="pagination">
      <li class="page-item">
        <a href="contest.php?page=1">&lt;&lt;</a>
      </li>
      <?php
if (!isset($page)) {
    $page = 1;
}

$page = intval($page);
$section = 8;
$start = $page > $section ? $page - $section : 1;
$end = $page + $section > $total_page ? $total_page : $page + $section;
for ($i = $start; $i <= $end; $i++) {
    echo "<li class='" . ($page == $i ? "active " : "") . "page-item'>
            <a title='go to page' href='contest.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a></li>";
}
?>
        <li class="page-item">
          <a href="contest.php?page=<?php echo $total_page ?>">&gt;&gt;</a>
        </li>
    </ul>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
  <script>
    var diff = new Date("<?php echo date("Y / m / d H: i: s") ?>").getTime() - new Date().getTime();
    //alert(diff);
    function clock() {
      var x, h, m, s, n, xingqi, y, mon, d;
      var x = new Date(new Date().getTime() + diff);
      y = x.getYear() + 1900;
      if (y > 3000) y -= 1900;
      mon = x.getMonth() + 1;
      d = x.getDate();
      xingqi = x.getDay();
      h = x.getHours();
      m = x.getMinutes();
      s = x.getSeconds();
      n = y + "-" + mon + "-" + d + " " + (h >= 10 ? h : "0" + h) + ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ? s : "0" + s);
      //alert(n);
      document.getElementById('nowdate').innerHTML = n;
      setTimeout("clock()", 1000);
    }
    clock();
  </script>
</body>

</html>