<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>竞赛&作业团队排名 -- <?php echo $title ?></title>
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
    <div style="text-align:center;">
      <h3>竞赛&作业团队排名 -- <?php echo $title ?></h3>
      <a class='btn btn-sm btn-info noradius' href="contestteamrank.xls.php?cid=<?php echo $contest_id ?>">导出Excel文件</a>
    </div>
    <?php
$rank = 1;
if ($OJ_MEMCACHE) {
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        echo ' | <a href="contestrank3.php?cid=' . $contest_id . '">滚榜</a>';
    }
    if ($OJ_MEMCACHE) {
        echo '<a href="contestrank2.php?cid=' . $contest_id . '">Replay</a>';
    }
}
?>
      <div style="overflow: auto">
        <table id="rank" class="table table-striped" width="95%">
          <thead>
            <tr class="toprow">
              <th style="width:50px; min-width: 50px;">排名</th>
              <th style="width:120px; min-width: 120px;">团队名称</th>
              <th style="width:60px; min-width: 100px;">总计通过</th>
              <th style="width:100px; min-width: 100px;">罚时</th>
              <?php
for ($i = 0; $i < $pid_cnt; $i++) {
    echo "<th style='min-width: 100px;'><a href=problem.php?cid=$contest_id&pid=$i>$PID[$i]</a></th>";
}
?>
            </tr>
          </thead>
          <tbody>
            <?php
for ($i = 0; $i < $team_cnt; $i++) {
    echo "<tr>";
    echo "<td>";
    echo $rank++;
    echo "</td>";
    // $uuid = $U[$i]->user_id;
    $name = $T[$i]->name;

    $tsolved = $T[$i]->solved;
    echo "<td>$name</td>";
    echo "<td>$tsolved</a>";
    echo "<td>" . sec2str($T[$i]->time);
    for ($j = 0; $j < $pid_cnt; $j++) {
        $bg_color = "eeeeee";
        if (isset($T[$i]->p_ac_num[$j]) && $T[$i]->p_ac_num[$j] > 0) {
            $aa = 0x33 + $U[$i]->p_wa_num[$j] * 32;
            $aa = $aa > 0xaa ? 0xaa : $aa;
            $aa = dechex($aa);
            $bg_color = "$aa" . "ff" . "$aa";
        } else if (isset($T[$i]->p_wa_num[$j]) && $T[$i]->p_wa_num[$j] > 0) {
            $aa = 0xaa - $U[$i]->p_wa_num[$j] * 10;
            $aa = $aa > 16 ? $aa : 16;
            $aa = dechex($aa);
            $bg_color = "ff$aa$aa";
        }
        echo "<td class='well' style='background-color:#$bg_color'>";
        if (isset($T[$i])) {
            if (isset($T[$i]->p_ac_num[$j]) && $T[$i]->p_ac_num[$j] > 0) {
                echo $T[$i]->p_ac_num[$j];
            }

            if (isset($T[$i]->p_wa_num[$j]) && $T[$i]->p_wa_num[$j] > 0) {
                echo "(-" . $T[$i]->p_wa_num[$j] . ")";
            }

        }
    }
    echo "</tr>\n";
}
?>
          </tbody>
        </table>
      </div>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
  <script type="text/javascript">
    $(document).ready(function () {
      metal();
    }
    );
  </script>
  <script>
    function getTotal(rows) {
      var total = 0;
      for (var i = 0; i < rows.length && total == 0; i++) {
        try {
          total = parseInt(rows[rows.length - i].cells[0].innerHTML);
          if (isNaN(total)) total = 0;
        } catch (e) {
        }
      }
      return total;
    }
    function metal() {
      var tb = window.document.getElementById('rank');
      var rows = tb.rows;
      try {
        var total = getTotal(rows);
        //alert(total);
        for (var i = 1; i < rows.length; i++) {
          var cell = rows[i].cells[0];
          var acc = rows[i].cells[2];
          var ac = parseInt(acc.innerText);
          if (isNaN(ac)) ac = parseInt(acc.textContent);
          if (cell.innerHTML != "*" && ac > 0) {
            var r = parseInt(cell.innerHTML);
            if (r == 1) {
              cell.innerHTML = "Winner";
              cell.className = "badge noradius btn-warning";
            }
            if (r > 1 && r <= total * .05 + 1)
              cell.className = "badge noradius btn-warning";
            if (r > total * .05 + 1 && r <= total * .20 + 1)
              cell.className = "badge noradius";
            if (r > total * .20 + 1 && r <= total * .45 + 1)
              cell.className = "badge noradius btn-danger";
            if (r > total * .45 + 1 && ac > 0)
              cell.className = "badge noradius badge-info";
          }
        }
      } catch (e) {
        //alert(e);
      }
    }

  </script>
  <style>
    .well {
      background-image: none;
      padding: 1px;
    }
    td {
      white-space: nowrap;
    }
  </style>
</body>
</html>