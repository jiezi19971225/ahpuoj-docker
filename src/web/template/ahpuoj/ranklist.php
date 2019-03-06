<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>个人排名</title>
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
    <div class="general-form-wrapper">
      <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
          <form class="form-inline" action="ranklist.php">
            <div class="form-group">
              <input placeholder="请输入用户名搜索" class="form-control noradius input-sm" name="prefix" id="prefix" value="<?php echo htmlentities(isset($_GET['prefix']) ? $_GET['prefix'] : '', ENT_QUOTES, "
                utf-8 ") ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm noradius" class="form-control">
              <i class='glyphicon glyphicon-search'></i>
            </button>
          </form>
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          <a class="btn btn-primary btn-sm noradius" href=ranklist.php?scope=d>日</a>
          <a class="btn btn-primary btn-sm noradius" href=ranklist.php?scope=w>周</a>
          <a class="btn btn-primary btn-sm noradius" href=ranklist.php?scope=m>月</a>
          <a class="btn btn-primary btn-sm noradius" href=ranklist.php?scope=y>年</a>
        </div>
      </div>
    </div>

    <table class="table table-striped">
      <thead>
        <tr class="toprow">
          <td style="width:5%;">名次</td>
          <td style="width:10%;">用户</td>
          <td>昵称</td>
          <td style="width:10%;">通过</td>
          <td style="width:10%;">提交</td>
          <td style="width:10%;">比率</td>
        </tr>
      </thead>
      <tbody>
        <?php
$cnt = 0;
foreach ($view_rank as $row) {
    if ($cnt) {
        echo "<tr class='oddrow'>";
    } else {
        echo "<tr class='evenrow'>";
    }

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
for ($i = 0; $i < $view_total; $i += $page_size) {
    echo "<a class='btn btn-primary btn-sm noradius' href='./ranklist.php?start=" . strval($i) . ($scope ? "&scope=$scope" : "") . "'>";
    echo strval($i + 1);
    echo "-";
    echo strval($i + $page_size);
    echo "</a>&nbsp;";
    if ($i % 250 == 200) {
        echo "<br>";
    }

}
?>

  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
</body>

</html>