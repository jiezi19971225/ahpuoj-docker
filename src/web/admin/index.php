<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <?php require_once 'top_menubar.php';?>
  <?php require_once 'side_menubar.php';?>
  <div class="layui-body">
    <div style="padding: 15px;"><p style="text-align: center;font-size: 40px;margin-top: 100px;">欢迎使用AHPUOJ后台管理系统</p></div>
  </div>
</div>
<?php require_once 'js.php';?>
<script>
layui.use('element', function(){
  var element = layui.element;

});
</script>
</body>
</html>