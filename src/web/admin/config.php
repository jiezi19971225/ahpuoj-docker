<?php
require_once "admin-header.php";

if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once "../include/check_post_key.php";
    $fp = fopen("config.txt", "w");
    $configs = [];
    $bbs = isset($_POST['bbs']);
    $configs['bbs'] = $bbs ? 1 : 0;
    foreach ($configs as $key => $config) {
        $config_row = $key . " " . $config;
        fputs($fp, $config_row);
    }
    fclose($fp);
    echo $bbs;
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header('Location:config.php');
    exit(0);
} else {
    $fp = fopen("config.txt", "r");
    $bbs = 0;
    while ($buff = fgets($fp)) {
        $item = explode(" ", $buff);
        if ($item[0] == 'bbs') {
            $bbs = intval($item[1]);
        }
    }
}
?>
<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>设置</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class='sub-page-title'>设置</h3>
      <form class="layui-form" action="config.php" method="POST">
        <div class="layui-form-item">
          <label class="layui-form-label">讨论版</label>
          <div class="layui-input-block">
            <input type="checkbox" name="bbs" <?php echo $bbs ? "checked=''" : '' ?> lay-skin="switch" lay-text="开启|关闭">
          </div>
          <?php require_once "../include/set_post_key.php";?>
          <div class="layui-form-item">
            <div class="layui-input-block">
            <button type="submit" class="btn btn-primary" lay-submit="">保存</button>
            </div>
          </div>
        </div>
    </div>
    </form>
  </div>
  <?php require_once 'js.php';?>
  <script>
    layui.use(['element', 'layer', 'form'], function () {
      var element = layui.element,
        layer = layui.layer,
        form = layui.form;
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