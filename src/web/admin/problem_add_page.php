<?php
require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

include_once "kindeditor.php";
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>添加问题</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class='sub-page-title'>添加问题</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form method="POST" action="problem_add.php" class="layui-form">
              <div class="form-group">
                <input type=hidden name=problem_id value="New Problem">
                <label for="title">标题</label>
                <input class="form-control" style="width:30%;" type="text" name="title" id="title" placeholder="请输入问题标题" lay-verify="required">
              </div>
              <div class="form-group">
                <label for="time_limit">时间限制</label>
                <div class="input-group" style="width:30%;">
                  <input class="form-control" type="text" name="time_limit" id="time_limit" value="1" placeholder="请输入时间限制">
                  <div class="input-group-addon">秒</div>
                </div>
              </div>
              <div class="form-group">
                <label for="memory_limit">内存限制</label>
                <div class="input-group" style="width:30%;">
                  <input class="form-control" type="text" name="memory_limit" id="memory_limit" value="128" placeholder="请输入内存限制">
                  <div class="input-group-addon">兆字节</div>
                </div>
              </div>
              <div class="form-group">
                <label for="description">题目描述</label>
                <textarea class="kindeditor" rows="13" name="description" id="description" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="input">输入</label>
                <textarea class="kindeditor" rows="13" name="input" id="input" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="output">输出</label>
                <textarea class="kindeditor" rows="13" name="output" id="output" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="sample_input">样例输入</label>
                <textarea class="form-control" rows="13" name="sample_input" id="sample_input" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="sample_output">样例输出</label>
                <textarea class="form-control" rows="13" name="sample_output" id="sample_output" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="test_input">测试输入</label>
                更多组测试数据，请在题目添加完成后补充
                <br>
                <textarea class="form-control" rows="13" name="test_input" id="test_input" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="test_output">测试输出</label>
                更多组测试数据，请在题目添加完成后补充
                <br>
                <textarea class="form-control" rows="13" name="test_output" id="test_output" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="hint">提示</label>
                <textarea class="kindeditor" rows="13" name="hint" id="hint" cols="80"></textarea>
              </div>
              <div class="form-group">
                <h4>特殊裁判 相关功能尚未开发 请选择否</h4>
                特殊裁判的使用，请参考<a href='https://cn.bing.com/search?q=hustoj+special+judge' target='_blank'>搜索hustoj special judge</a>
                <br>
                <?php echo "否" ?>
                <input type=radio name="spj" value='0' checked>
                <?php echo "是" ?>
                <input type=radio name="spj" value='1'>
                <br>
              </div>
              <div class="form-group">
                <label for="source">来源/分类</label>
                <textarea class="kindeditor" rows="13" name="source" id="source" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="contest_id">
                  竞赛&作业
                </label>
                <select name="contest_id" id="contest_id" class="form-control">
                  <?php
$sql = "SELECT `contest_id`,`title` FROM `contest` WHERE `start_time`>NOW() order by `contest_id`";
$result = pdo_query($sql);
echo "<option value=''>none</option>";
if (count($result) == 0) {
} else {
    foreach ($result as $row) {
        echo "<option value='{$row['contest_id']}'>{$row['contest_id']} {$row['title']}</option>";
    }
}?>
                </select>
              </div>
              <?php require_once "../include/set_post_key.php";?>
              <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
  <?php require_once 'js.php';?>
  <script>
    layui.use(['element','layer','form'], function () {
      var element = layui.element,
      layer = layui.layer;

    });
  </script>
</body>

</html>