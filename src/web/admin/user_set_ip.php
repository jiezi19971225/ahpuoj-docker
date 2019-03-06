<?php
require_once "admin-header.php";
require_once 'js.php';
require_once "../include/set_get_key.php";
$sql = "";
if (isset($_POST['user_id'])) {
    require_once "../include/check_post_key.php";
    $user_id = $_POST['user_id'];
    $ip = $_POST['ip'];

    $sql = "SELECT 1 FROM `users` WHERE user_id = ?";
    $row = 0;
    if ($result = pdo_query($sql, $user_id)) {
        $row = count($result);
    }
    // 用户存在
    if ($row) {
        // 判断ip是否合法
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "设置失败，请输入有效的IP地址";
            header('Location:user_set_ip.php');
            exit(0);
        }

        $sql = "insert into loginlog (user_id,password,ip,time) value(?,?,?,now())";
        $result = pdo_query($sql, $user_id, "set ip by " . $_SESSION[$OJ_NAME . "_user_id"], $ip);
        $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    } else {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "设置失败，用户不存在";
    }
    header('Location:user_set_ip.php');
    exit(0);
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>指定登录IP</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class='sub-page-title'>指定登录IP</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form action="user_set_ip.php" method="POST" class="layui-form">
              <div class="form-group">
                <label for="user_id">
                  用户
                </label>
                <input class="form-control" type="text" id="user_id" name="user_id" value='' placeholder="请输入用户ID" style="width:30%;" lay-verify="required">
              </div>
              <div class="form-group">
                <label for="ip">IP</label>
                <input class="form-control" type="text" id="ip" name="ip" value='' placeholder="请输入IP地址" style="width:30%;" lay-verify="required">
              </div>
              <?php require_once "../include/set_post_key.php";?>
              <button type="submit" class="btn btn-primary" lay-submit="">设置</button>
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