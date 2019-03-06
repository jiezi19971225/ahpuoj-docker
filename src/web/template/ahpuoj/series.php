<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>系列赛 --
    <?php echo $name ?>
  </title>
  <?php include "template/$OJ_TEMPLATE/css.php";?>
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <?php include "template/$OJ_TEMPLATE/nav.php";?>
  <div class="main-content" id="contestset-page-content" style="padding-bottom:200px;">
    <h3 class="general-page-title"><?php echo "$series_id $name" ?></h3>
    <div class="general-form-wrapper">
      <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <span class="table-tag bg-azure">系统时间</span>
          <span class="table-tag bg-blackish-green" id="nowdate"></span>
        </div>
      </div>
    </div>
    <table class="table table-striped">
      <thead>
        <tr class="toprow">
          <td style="width:5%;">ID</td>
          <td>竞赛&作业名称</td>
          <td style="width:30%;">状态</td>
          <td style="width:10%;">公开度</td>
          <td style="width:10%;">模式</td>
        </tr>
      </thead>
      <tbody>
        <?php
foreach ($contestset as $row) {
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

    <?php
// 如果是团队模式 显示团队模式计分板
if ($team_mode) {
    ?>
        <div style="text-align:center;">
          <h3 class="general-page-title" style="margin-top:100px;">团队成绩汇总</h3>
          <a class='btn btn-sm btn-info noradius' href="seriesteamrank.xls.php?sid=<?php echo $series_id ?>">导出Excel文件</a>
        </div>
      <div style="overflow:auto;">
        <table class="table table-striped" style="width:95%;">
          <thead>
            <tr class="toprow">
              <td rowspan="2" style="width:120px; min-width: 120px;">队名</td>
              <?php
foreach ($contest_list as $contest) {
        echo "<td colspan='3'>{$contest['title']}</td>";
    }
    ?>
            </tr>
            <tr class="toprow">
              <?php
foreach ($contest_list as $contest) {
        echo "<td>排名</td>";
        echo "<td>通过</td>";
        echo "<td>罚时</td>";
    }
    ?>
            </tr>
          </thead>
          <tbody>
            <?php
foreach ($teamset as $row) {
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
      </div>
      <?php
}
?>
      <div style="text-align:center;">
        <h3 class="general-page-title" style="margin-top:100px;">个人成绩汇总</h3>
        <a class='btn btn-sm btn-info noradius' href="seriesuserrank.xls.php?sid=<?php echo $series_id ?>">导出Excel文件</a>
      </div>
      <div style="overflow:auto;">
        <table class="table table-striped" style="width:95%;">
          <thead>
            <tr class="toprow">
              <td rowspan="2" style="width:120px; min-width: 120px;">用户</td>
              <td rowspan="2" style="width:120px; min-width: 120px;">昵称</td>
              <?php
foreach ($contest_list as $contest) {
    echo "<td colspan='3'>{$contest['title']}</td>";
}
?>
            </tr>
            <tr class="toprow">
              <?php
foreach ($contest_list as $contest) {
    echo "<td>排名</td>";
    echo "<td>通过</td>";
    echo "<td>罚时</td>";
}
?>
            </tr>
          </thead>
          <tbody>
            <?php
foreach ($userset as $row) {
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
      </div>
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