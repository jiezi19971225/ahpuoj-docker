<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>个人主页 -- <?php echo $user ?></title>
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
    <div>
        <p>
          <span>
            <b>用户名：</b>
            <?php echo $user ?>
          </span>
        </p>
        <p>
          <span>
            <b>昵称：</b>
            <?php echo htmlentities($nick, ENT_QUOTES, "UTF-8") ?>
          </span>
        </p>
    </div>
    <table class="table table-striped" id="statics" width=70%>

      <tr>
        <td width="15%">名次</td>
        <td width="25%">排名</td>
        <td width="60%">通过题目</td>
      </tr>
      <tr>
        <td>解决</td>
        <td>
          <a href='status.php?user_id=<?php echo $user ?>&jresult=4'>
            <?php echo $AC ?>
          </a>
        </td>
        <td rowspan="14" id="solved-problem-list">
<?php
$sql = "SELECT `problem_id`,count(1) from solution where `user_id`=? and result=4 group by `problem_id` ORDER BY `problem_id` ASC";
if ($result = pdo_query($sql, $user)) {
    foreach ($result as $row) {
        $problem_id = $row[0];
        $count = $row[1];
        echo "[<a href='problem.php?id=$problem_id'>$problem_id</a>&nbsp;<a href='status.php?user_id=$user&problem_id=$problem_id'>($count)&nbsp;</a>]";
    }
}
?>
        </td>
      </tr>
      <tr>
        <td>提交</td>
        <td>
          <a href='status.php?user_id=<?php echo $user ?>'>
            <?php echo $Submit ?>
          </a>
        </td>
      </tr>
      <?php
foreach ($view_userstat as $row) {
//i++;
    echo "<tr ><td>" . $jresult[$row[0]] . "<td><a href=status.php?user_id=$user&jresult=" . $row[0] . " >" . $row[1] . "</a></tr>";
}
//}
echo "<tr id=pie ><td>统计<td><div id='PieDiv' style='position:relative;height:105px;width:120px;'></div></tr>";
?>
        <script type="text/javascript" src="include/wz_jsgraphics.js"></script>
        <script type="text/javascript" src="include/pie.js"></script>
        <script language="javascript">
var y = new Array();
          var x = new Array();
          var dt = document.getElementById("statics");
          var data = dt.rows;
          var n;
          for (var i = 3; dt.rows[i].id != "pie"; i++) {
            n = dt.rows[i].cells[0];
            n = n.innerText || n.textContent;
            x.push(n);
            n = dt.rows[i].cells[1].firstChild;
            n = n.innerText || n.textContent;
            //alert(n);
            n = parseInt(n);
            y.push(n);
          }
          var mypie = new Pie("PieDiv");
          mypie.drawPie(y, x);
//mypie.clearPie();
        </script>
    </table>
    <div class="submission-wrapper">
      <div id="submission" style="width:95%;height:300px;margin:0 auto;"></div>
    </div>
    <?php
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    ?>
      <table class="table">
        <tr>
          <td>用户名</td>
          <td>状态</td>
          <td>IP</td>
          <td>时间</td>
        </tr>
        <tbody>
          <?php
foreach ($view_userinfo as $row) {
        echo "<tr>";
        for ($i = 0; $i < count($row) / 2; $i++) {
            echo "<td>";
            echo "\t" . $row[$i];
            echo "</td>";
        }
        echo "</tr>";
    }
    ?>
        </tbody>
      </table>
      <?php
}
?>

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