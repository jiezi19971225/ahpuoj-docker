<?php
require_once "admin-header.php";
require_once 'js.php';
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if (isset($_POST['msg'])) {
    require_once "../include/check_post_key.php";
    $fp = fopen("msg.txt", "w");
    $msg = $_POST['msg'];
    $msg = str_replace("<p>", "", $msg);
    $msg = str_replace("</p>", "<br />", $msg);
    $msg = str_replace(",", "&#44;", $msg);
    if (get_magic_quotes_gpc()) {
        $title = stripslashes($title);
    }
    $msg = RemoveXSS($msg);
    fputs($fp, $msg);
    fclose($fp);
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header('Location:setmsg.php');
    exit(0);
} else {
    $msg = file_get_contents("msg.txt");
    include "kindeditor.php";
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>设置公告</title>
<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class='sub-page-title'>设置公告</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form action="setmsg.php" method="POST">
              <div class="form-group">
                <textarea name='msg' style="width:100%;" rows=25 class="kindeditor"><?php echo $msg ?></textarea>
              </div>
              <?php require_once "../include/set_post_key.php";?>
              <button type="submit" class="btn btn-primary">保存</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once 'js.php';?>
  <script>
    layui.use(['element','layer'], function () {
      var element = layui.element,
      layer = layui.layer;

      <?php
$status = flash_status_session();
if ($status == $OPERATOR_SUCCESS) {
    echo "layer.msg('操作成功');\n";
}
?>
    });
  </script>

</body>

</html>