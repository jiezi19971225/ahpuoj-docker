<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <?php require_once 'top_menubar.php';?>
  <?php require_once 'side_menubar.php';?>
  <div class="layui-body">
    <!-- 内容主体区域 -->
    <div class="notfound">
      <p>你所访问的页面不存在</p>
      <a class="btn btn-primary" onclick="javascript:history.go(-1)">点击返回</a>
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