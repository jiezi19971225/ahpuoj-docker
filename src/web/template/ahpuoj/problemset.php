
<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>问题集</title>
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
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
          <form class="form-inline" action="problem.php">
            <input class="form-control search-query noradius input-sm" type='text' name='id' placeholder="请输入跳转问题ID">
            <button class="btn btn-primary noradius btn-sm" type="submit"><i class="glyphicon glyphicon-search"></i></button>
          </form>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
          <form class="form-search form-inline">
            <input type="text" name=search class="form-control search-query noradius input-sm" placeholder="请输入标题或来源搜索" value="<?php echo $keyword ?>">
            <button class="btn btn-primary noradius btn-sm" type="submit"><i class="glyphicon glyphicon-search"></i></button>
          </form>
        </div>
      </div>
    </div>
    <table id='problemset' class='table table-striped'>
      <thead>
        <tr class='toprow'>
          <th style="width:5%;"></th>
          <th style="width:5%;" class='hidden-xs'>#</th>
          <th>题名</th>
          <th style="width:15%;"class='hidden-xs'>来源/分类</th>
          <th style="width:10%;">通过</th>
          <th style="width:10%;">提交</th>
        </tr>
      </thead>
      <tbody>
        <?php
foreach ($problemset as $row) {
    foreach ($row as $key => $table_cell) {
        if ($key == 1 || $key == 3) {
            echo "<td  class='hidden-xs'>";
        } else {
            echo "<td>";
        }

        echo "\t" . $table_cell;
        echo "</td>";
    }
    echo "</tr>";
}
?>
      </tbody>
    </table>
    <nav id="page" class="center"><ul class="pagination">
<li class="page-item"><a href="problemset.php?<?php echo $keyword ? "search={$keyword}&" : "" ?>&page=1">&lt;&lt;</a></li>
<?php
if (!isset($page)) {
    $page = 1;
}

$page = intval($page);
$section = 8;
$start = $page > $section ? $page - $section : 1;
$end = $page + $section > $total_page ? $total_page : $page + $section;
for ($i = $start; $i <= $end; $i++) {
    ?>
    <li class="page-item <?php echo $page == $i ? 'active ' : '' ?>" >
        <a href='problemset.php?<?php echo $keyword ? "search={$keyword}&" : "" ?>page=<?php echo $i ?>'><?php echo $i ?></a>
    </li>
<?php
}
?>
<li class="page-item"><a href="problemset.php?<?php echo $keyword ? "search={$keyword}&" : "" ?>page=<?php echo $total_page ?>">&gt;&gt;</a></li>
</ul></nav>
</div>
    <?php include "template/$OJ_TEMPLATE/js.php";?>
</body>

</html>