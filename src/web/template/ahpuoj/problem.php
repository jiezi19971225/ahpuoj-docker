<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">
  <title>问题<?php echo $PID[$problem_id] . ": " . $title ?> </title>
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
      <?php
if ($pr_flag) {
    ?>
        <h3 class="general-page-title ">
          <?php echo $problem_id . " " . $title ?>
        </h3>
        <?php
} else {
    ?>
          <h3 class="general-page-title ">
            <?php echo $PID[$problem_id] . ": " . $title ?>
          </h3>
          <?php
}
?>
          <div class="problem-tags-wrapper">
            <span>时间限制：</span>
            <span class="problem-info-tag bg-orange">
              <?php echo $time_limit ?>S</span>
            <span>内存限制：</span>
            <span class="problem-info-tag bg-purple">
              <?php echo $memory_limit ?>MB</span>
            <span>提交：</span>
            <span class="problem-info-tag bg-azure">
              <?php echo $submit ?>
            </span>
            <span>解决：</span>
            <span class="problem-info-tag bg-green">
              <?php echo $accepted ?>
            </span>
          </div>
          <div class="problem-buttons-wrapper">
            <?php
if ($row['spj']) {
    echo "<span class=red>特殊判断</span>";
}
if ($pr_flag) {
    echo "<a class='btn btn-success btn-sm noradius' href='submitpage.php?id=$problem_id'>提交</a>";
} else {
    echo "<a class='btn btn-success btn-sm noradius' href='submitpage.php?cid=$contest_id&pid=$problem_id&langmask=$langmask'>提交</a>";
}
$params = "problem_id=$problem_id";
if ($contest_id) {
    $params = "problem_id=$problem_id&cid=$contest_id";
}
echo "<a class='btn btn-info btn-sm noradius' href='status.php?$params'>提交记录</a>";
if (!$contest_id) {
    echo "<a class='btn btn-warning btn-sm noradius' href='bbs.php?pid=" . $row['problem_id'] . "$contest_id'>讨论版</a>";
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    require_once "include/set_get_key.php";
    echo "<a class='btn btn-primary btn-sm noradius' href='admin/problem_edit.php?id=$problem_id&getkey=" . $_SESSION[$OJ_NAME . '_' . 'getkey'] . "'>编辑</a>";
    echo "<a class='btn btn-info btn-sm noradius' href='javascript:phpfm(" . $row['problem_id'] . ")'>测试数据</a>";
}
?>
          </div>
          <?php
echo "<h4>题目描述</h4>";
echo "<div class=content><div><div>" . $row['description'] . "</div>";

if ($row['input']) {
    echo "<h4>输入</h4><div class=content>" . $row['input'] . "</div>";
}

if ($row['output']) {
    echo "<h4>输出</h4><div class=content>" . $row['output'] . "</div>";
}

$sinput = str_replace("<", "&lt;", $row['sample_input']);
$sinput = str_replace(">", "&gt;", $sinput);
$soutput = str_replace("<", "&lt;", $row['sample_output']);
$soutput = str_replace(">", "&gt;", $soutput);

if (strlen($sinput)) {
    echo "<h4>样例输入</h4><pre class=content><span class=sampledata>" . ($sinput) . "</span></pre>";
}

if (strlen($soutput)) {
    echo "<h4>样例输出</h4><pre class=content><span class=sampledata>" . ($soutput) . "</span></pre>";
}

if ($row['hint']) {
    echo "<h4>提示</h4><div class='hint content'>" . $row['hint'] . "</div>";
}

?>

            <div class="problem-buttons-wrapper" style="margin-top: 20px;">
              <?php
if ($pr_flag) {
    echo "<a class='btn btn-success btn-sm noradius' href='submitpage.php?id=$problem_id'>提交</a>";
} else {
    echo "<a class='btn btn-success btn-sm noradius' href='submitpage.php?cid=$contest_id&pid=$problem_id&langmask=$langmask'>提交</a>";
}
?>
            </div>
    </div>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
  <script>
    function phpfm(pid) {
      //alert(pid);
      $.post("admin/phpfm.php", { 'frame': 3, 'pid': pid, 'pass': '' }, function (data, status) {
        if (status == "success") {
          document.location.href = "admin/phpfm.php?frame=3&pid=" + pid;
        }
      });
    }
  </script>

</body>

</html>