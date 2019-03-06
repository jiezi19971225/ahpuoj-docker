<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>
    <?php echo $OJ_NAME ?>
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
  <marquee id="broadcast" scrollamount="1" direction="up" onMouseOver='this.stop()' onMouseOut='this.start()' ;>
    <?php
$marquee_msg = file_get_contents("./admin/msg.txt");
echo $marquee_msg;
?>
  </marquee>
  <div class="main-content">
    <div class="submission-wrapper">
      <h4 style="text-align:center;">最近提交</h4>
      <div id="submission" style="max-width:95%;width:95%;height:400px;margin:0 auto;">
      </div>
    </div>
    <div class="news-wrapper">
      <ul class="news-list">
        <?php
foreach ($news as $row) {
    ?>
          <li class="news-box">
            <h4>
              <?php echo $row['title'] ?>
            </h4>
            <p class="content">
              <?php echo $row['content'] ?>
            </p>
            <div class="info">
              <span>
                <?php echo date("Y-m-d h:i", strtotime($row['time'])) ?>
              </span>
            </div>
          </li>
          <?php
}
?>
      </ul>
    </div>
    <div style="text-align: center;">本系统基于<a href='https://github.com/zhblue/hustoj' >HUSTOJ</a>改造</div>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
  <script language="javascript" type="text/javascript" src="include/jquery.flot.js"></script>
  <script type="text/javascript">
    $(function () {
      var d1 = <?php echo json_encode($chart_data_all) ?>;
      var d2 = <?php echo json_encode($chart_data_ac) ?>;
      $.plot($("#submission"), [
        { label: "提交", data: d1, lines: { show: true } },
        { label: "通过", data: d2, bars: { show: true } }], {
          grid: {
            backgroundColor: { colors: ["#fff", "#eee"] }
          },
          xaxis: {
            mode: "time"
          }
        });
    });
  </script>
</body>

</html>