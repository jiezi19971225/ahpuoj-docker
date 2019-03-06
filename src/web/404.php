<!DOCTYPE html>
<?php
require_once "./frontend-header.php";
?>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <title><?php echo $OJ_NAME ?></title>
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
      <div class="notfound">
        <p>你所访问的页面不存在</p>
        <a class="btn btn-primary" onclick="javascript:history.go(-1)">点击返回</a>
      </div>
    </div>
    <?php include "template/$OJ_TEMPLATE/js.php";?>
  </body>
</html>