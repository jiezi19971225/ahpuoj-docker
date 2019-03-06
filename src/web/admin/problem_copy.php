<?php require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>复制题目</title>
<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">复制题目</h3>
      <div class="container">

        <div class="panel panel-default">
          <div class="panel-body">
            <form method="POST" class="layui-form" action="problem_add_page_hustoj.php" role="form">
              <div class="form-group">
                <label for="url">从 http://hustoj...... 复制</label>
                <input placeholder="请输入带有http前缀的网址" lay-verify="required" class="form-control" style="width:50%;" name="url" type="text">
              </div>
              <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
            </form>

            <form method="POST" class="layui-form" action="problem_add_page_luogu.php" role="form">
              <div class="form-group">
                <label for="url">从 https://www.luogu.org/problemnew/show/ 复制</label>
                <input placeholder="请输入带有http前缀的网址" lay-verify="required" class="form-control" style="width:50%;" name="url" type="text">
              </div>
              <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
            </form>

            <form method="POST" class="layui-form" action="problem_add_page_loj.php" role="form">
              <div class="form-group">
                <label for="url">从 https://loj.ac/problem/ 复制</label>
                <input placeholder="请输入带有http前缀的网址" lay-verify="required" class="form-control" style="width:50%;" name="url" type="text">
              </div>
              <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
            </form>

            <form method="POST" class="layui-form" action="problem_add_page_pku.php" role="form">
              <div class="form-group">
                <label for="url">从 acm.pku.edu.cn 复制</label>
                <input placeholder="请输入带有http前缀的网址" lay-verify="required" class="form-control" style="width:50%;" name="url" type="text">
              </div>
              <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
            </form>

            <form method="POST" class="layui-form" action="problem_add_page_hdu.php" role="form">
              <div class="form-group">
                <label for="url">从 acm.hdu.edu.cn 复制</label>
                <input placeholder="请输入带有http前缀的网址" lay-verify="required" class="form-control" style="width:50%;" name="url" type="text">
              </div>
              <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
            </form>

            <form method="POST" class="layui-form" action="problem_add_page_zju.php" role="form">
              <div class="form-group">
                <label for="url">从 acm.zju.edu.cn 复制</label>
                <input placeholder="请输入带有http前缀的网址" lay-verify="required" class="form-control" style="width:50%;" name="url" type="text">
              </div>
              <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
            </form>

          </div>
        </div>
      </div>
    </div>

  </div>
  <?php require_once 'js.php';?>
  <script>
    layui.use(['element','form','layer'], function () {
      var element = layui.element,
      form = layui.form,
      layer = layui.layer;
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
  </script>
</body>

</html>