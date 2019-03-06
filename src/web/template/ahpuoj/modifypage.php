<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>注册信息</title>
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
      <form action="modify.php" method="post" role="form" class="form-horizontal">
        <h3 style="text-align:center;">注册信息</h3>
        <div class="form-group">
          <label class="col-sm-4 control-label">用户名</label>
          <div class="col-sm-4"><label class="col-sm-2 control-label"><?php echo $_SESSION[$OJ_NAME . '_' . 'user_id'] ?></label></div>
          <?php require_once './include/set_post_key.php';?>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label">昵称</label>
          <div class="col-sm-4"><input name="nick" class="form-control" value="<?php echo htmlentities($row['nick'], ENT_QUOTES, "UTF-8") ?>" type="text"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label">密码</label>
          <div class="col-sm-4"><input name="opassword" class="form-control" placeholder="请输入当前密码" type="password"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label">新密码</label>
          <div class="col-sm-4"><input name="npassword" class="form-control"  placeholder="请输入新密码"  type="password"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label">新密码（重复）</label>
          <div class="col-sm-4"><input name="rptpassword" class="form-control"  placeholder="请重复输入新密码"  type="password"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label">学校</label>
          <div class="col-sm-4"><input name="school" class="form-control" value="<?php echo htmlentities($row['school'], ENT_QUOTES, "UTF-8") ?>" type="text"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label">邮箱</label>
          <div class="col-sm-4"><input name="email" class="form-control" value="<?php echo htmlentities($row['email'], ENT_QUOTES, "UTF-8") ?>" type="text"></div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-4 col-sm-2">
            <button name="submit" type="submit" class="btn btn-success btn-block noradius">提交</button>
          </div>
          <div class="col-sm-2">
            <button name="submit" type="reset" class="btn btn-primary btn-block noradius">重置</button>
          </div>
        </div>
      </form>
      <a class="btn btn-success noradius" href="export_ac_code.php">下载全部AC源码</a>
  </div>
  <?php include "template/$OJ_TEMPLATE/js.php";?>
</body>
</html>
