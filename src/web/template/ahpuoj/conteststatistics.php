<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>竞赛&作业统计 -- <?php echo $title ?></title>
  <?php include "template/$OJ_TEMPLATE/css.php";?>
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <?php include "template/$OJ_TEMPLATE/nav.php";?>
  <div class="main-content">
    <!-- Main component for a primary marketing message or call to action -->
    <h3 class="general-page-title">竞赛&作业统计 -- <?php echo $title ?></h3>
    <table id="cs" class="table table-striped">
      <thead>
        <tr class=toprow>
          <th>#</th>
          <th>通过</th>
          <th>格式错误</th>
          <th>答案错误</th>
          <th>时间超制</th>
          <th>内存超限</th>
          <th>输出超限</th>
          <th>运行时错误</th>
          <th>编译错误</th>
          <th></th>
          <th></th>
          <th>总计</th>
          <?php
foreach ($language_name as $key => $lang) {
    if (isset($R[$pid_cnt][$key + 11])) {
        echo "<th>$language_name[$key]</th>";
    } else {
        echo "<th></th>";
    }
}
?>
        </tr>
      </thead>
      <tbody>
        <?php
for ($i = 0; $i < $pid_cnt; $i++) {
    if (!isset($PID[$i])) {
        $PID[$i] = "";
    }
    echo "<tr>";
    echo "<td>";
    echo "<a href='problem.php?cid=$contest_id&pid=$i'>$PID[$i]</a>";
    for ($j = 0; $j < count($language_name) + 10; $j++) {
        if (!isset($R[$i][$j])) {
            $R[$i][$j] = "";
        }

        echo "<td>" . $R[$i][$j];
    }
    echo "</tr>";
}
echo "<tr><td>总计";
for ($j = 0; $j < count($language_name) + 11; $j++) {
    if (!isset($R[$i][$j])) {
        $R[$i][$j] = "";
    }

    echo "<td>" . $R[$i][$j];
}
echo "</tr>";
?>
      </tbody>
      <table>
      <div class="submission-wrapper">
        <div id="submission" style="width:100%;height:400px"></div>
      </div>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
  <script language="javascript" type="text/javascript" src="include/jquery.flot.js"></script>
  <script type="text/javascript">
    $(function () {
      var d1 = [];
      var d2 = [];
<?php
foreach ($chart_data_all as $k => $d) {
    ?>
          d1.push([<?php echo $k ?>, <?php echo $d ?>]);
<?php }?>
<?php
foreach ($chart_data_ac as $k => $d) {
    ?>
          d2.push([<?php echo $k ?>, <?php echo $d ?>]);
<?php }?>
var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];
      $.plot($("#submission"), [
        { label: "提交", data: d1, lines: { show: true } },
        { label: "通过", data: d2, bars: { show: true } }], {
          xaxis: {
            mode: "time"
          },
          grid: {
            backgroundColor: { colors: ["#fff", "#333"] }
          }
        });
    });
  </script>
</body>

</html>