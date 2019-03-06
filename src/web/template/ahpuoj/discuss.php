<?php
$view_discuss = ob_get_contents();
ob_end_clean();
require_once "../include/my_func.inc.php";
?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <link rel="stylesheet" href="../template/<?php echo $OJ_TEMPLATE ?>/layui/css/layui.css">
    <title>讨论版</title>

    <?php include "../template/$OJ_TEMPLATE/css.php";?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php include "../template/$OJ_TEMPLATE/nav.php";?>
    <div class="main-content">
	    <?php echo $view_discuss ?>
    </div>
    <?php include "../template/$OJ_TEMPLATE/js.php";?>
  </body>
  <script src="../template/<?php echo $OJ_TEMPLATE ?>/layui/layui.js"></script>
  <script>
    layui.use(['element','layer','form'],function(){
      var element = layui.element,
      layer = layui.layer,
      form = layui.form;
      <?php
$status = flash_status_session();
if ($status == $OPERATOR_SUCCESS) {
    echo "layer.msg('操作成功');\n";
} else if ($status == $OPERATOR_FAILURE) {
    $msg = $_SESSION['error_message'];
    echo "layer.msg('$msg',{time:5000});\n";
}
?>
    });
    function deleteTopic(url) {
      layer.confirm('确定要删除这个讨论吗?', { icon: 3, title: '提示' }, function (index) {
        window.location.href = url;
        layer.close(index);
      });
    }
    function deleteReply(url) {
      layer.confirm('确定要删除这个回复吗?', { icon: 3, title: '提示' }, function (index) {
        window.location.href = url;
        layer.close(index);
      });
    }
  </script>
</html>
