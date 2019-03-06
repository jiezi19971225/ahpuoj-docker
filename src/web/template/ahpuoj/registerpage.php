<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>注册</title>
  <?php include "template/$OJ_TEMPLATE/css.php";?>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <?php include "template/$OJ_TEMPLATE/nav.php";?>
  <div class="main-content" style="position:relative;">
    <div class="container">
      <!-- Main component for a primary marketing message or call to action -->
      <div class="login-form-wrapper">

        <form action="register.php" method="post" role="form" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-4 control-label">用户名</label>
            <div class="col-sm-8">
              <input name="user_id" class="form-control" placeholder="用户名*" type="text">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">昵称</label>
            <div class="col-sm-8">
              <input name="nick" class="form-control" placeholder="昵称*" type="text">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">密码</label>
            <div class="col-sm-8">
              <input name="password" class="form-control" placeholder="密码*" type="password">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">重复密码</label>
            <div class="col-sm-8">
              <input name="rptpassword" class="form-control" placeholder="重复密码*" type="password">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">学校</label>
            <div class="col-sm-8">
              <input name="school" class="form-control" placeholder="学校" type="text" value="安徽工程大学">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">邮箱</label>
            <div class="col-sm-8">
              <input name="email" class="form-control" placeholder="邮箱" type="text">
            </div>
          </div>

          <?php if ($OJ_VCODE) {?>
          <div class="form-group">
            <label class="col-sm-4 control-label">验证码</label>
            <div class="col-sm-4">
              <input name="vcode" class="form-control" placeholder="验证码*" type="text">
            </div>
            <div class="col-sm-4">
              <img alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()" height="30px">*
            </div>
          </div>
          <?php }?>

          <div class="form-group">
            <div class="col-sm-6">
              <button name="submit" type="submit" class="btn btn-success btn-block">注册</button>
            </div>
            <div class="col-sm-6">
              <button name="submit" type="reset" class="btn btn-danger btn-block">重置</button>
            </div>
          </div>
          <p style="text-align:center;">
            <a href="loginpage.php">已有账号?点击去登录</a>
          </p>
        </form>
      </div>
    </div>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
  <script>
    $("input").attr("class", "form-control");
  </script>
</body>

</html>