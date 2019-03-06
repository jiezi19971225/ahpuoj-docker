<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>竞赛&作业 -- <?php echo $title ?></title>
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
    <div class="problem-wrapper">
      <h3 class="general-page-title ">
        <?php echo "$contest_id $title" ?>
      </h3>
      <div class="problem-tags-wrapper">
        <span>开始时间：</span>
        <span class="problem-info-tag bg-orange">
          <?php echo $date_start_time ?>
        </span>
        <span>结束时间：</span>
        <span class="problem-info-tag bg-purple">
          <?php echo $date_end_time ?>
        </span>
        <span>当前时间：</span>
        <span class="problem-info-tag bg-azure" id="nowdate">
          <?php echo date("Y-m-d H:i:s") ?>
        </span>
        <span>状态：</span>
        <?php
if ($now > $end_time) {
    echo "<span class='problem-info-tag bg-red'>已结束</span>";
} else if ($now < $start_time) {
    echo "<span class='problem-info-tag bg-blackish-green'>未开始</span>";
} else {
    echo "<span class='problem-info-tag bg-green'>进行中</span>";
}
?>
          <span>公开度：</span>
          <?php
if ($private == '0') {
    echo "<span class='problem-info-tag bg-green'>公开</span>";
} else {
    echo "<span class='problem-info-tag bg-red'>私有</span>";
}
?>
      </div>
      <div class="problem-buttons-wrapper">
        <a class="btn btn-success btn-sm noradius" href='status.php?cid=<?php echo $contest_id ?>'>状态</a>
        <?php
if ($team_mode) {
    ?>
            <a class="btn btn-sm btn-warning noradius bg-orange" href='contestteamrank.php?cid=<?php echo $contest_id ?>'>团队排名</a>
            <a class="btn btn-info btn-sm noradius" href='contestrank.php?cid=<?php echo $contest_id ?>'>个人排名</a>
<?php
} else {
    ?>
            <a class="btn btn-info btn-sm noradius" href='contestrank.php?cid=<?php echo $contest_id ?>'>排名</a>
            <?php
}
?>
        <a class="btn btn-primary btn-sm noradius" href='conteststatistics.php?cid=<?php echo $contest_id ?>'>统计</a>
      </div>
      <div><?php echo $description ?></div>
<?php
if ($show_problems) {
    ?>
      <table id='problemset' class='table table-striped'>
        <thead>
          <tr class='toprow'>
            <th style="width:5%;"></th>
            <th style="width:10%;">#</th>
            <th>题名</th>
            <th style="width:15%;">来源/分类</th>
            <th style="width:10%;">通过人数</th>
            <th style="width:10%;">提交人数</th>
          </tr>
        </thead>
        <tbody>
          <?php
$cnt = 0;
    foreach ($problemset as $row) {
        echo "<tr>";
        foreach ($row as $table_cell) {
            echo "<td>";
            echo "\t" . $table_cell;
            echo "</td>";
        }
        echo "</tr>";
        $cnt = 1 - $cnt;
    }
    ?>
        </tbody>
      </table>
<?php
} else {
    echo "<div style='font-size:18px;font-weight:bold;text-align:center;'>$info</div>";
}?>
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