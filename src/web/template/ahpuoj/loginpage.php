<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>登录</title>
  <?php include "template/$OJ_TEMPLATE/css.php";?>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

  <?php include "template/$OJ_TEMPLATE/nav.php";?>
  <div class="main-content"  style="position:relative;">
    <div class="container">

      <div class="login-form-wrapper">
        <h3 class="title">登录</h3>
        <form id="login" action="login.php" method="post" role="form" onSubmit="return jsMd5();">
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-user"></i>
              </div>
              <input name="user_id" class="form-control" placeholder="请输入用户名" type="text" value="<?php echo $username ?>" autocomplete="off" >
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-lock"></i>
              </div>
              <input name="password" class="form-control" placeholder="请输入密码" type="password" value="<?php echo $password ?>" autocomplete="off" >
            </div>
          </div>
          <?php if ($OJ_VCODE) {?>

          <div class="form-group">
            <div class="row" style="margin:0px;">
              <div class="col-sm-8" style="padding: 0px;">
                <input name="vcode" class="form-control" style="width:100%" ; type="text" placeholder="请输入验证码">
              </div>
              <div class="col-sm-4">
                <img alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()" height="30px">
              </div>
            </div>
          </div>
          <?php }?>

          <div class="form-group">
            <button name="submit" type="submit" class="btn btn-primary btn-block">
              登录
            </button>
          </div>
          <p style="text-align:center;"><a href="registerpage.php">还没有账号?点击去注册</a></p>
        </form>

      </div>
      <script src="include/md5-min.js"></script>
      <script>
        function jsMd5() {
          if ($("input[name=password]").val() == "") return false;
          $("input[name=password]").val(hex_md5($("input[name=password]").val()));
          return true;
        }
      </script>
    </div>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
</body>

</html>