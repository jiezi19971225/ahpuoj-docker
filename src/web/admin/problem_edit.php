
<?php
require_once "admin-header.php";
require_once "js.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
include_once "kindeditor.php";
if (isset($_GET['id'])) {
    //require_once("../include/check_get_key.php");
    $problem_id = $_GET['id'];
    $sql = "SELECT * FROM `problem` WHERE `problem_id`=?";
    $result = pdo_query($sql, intval($problem_id));
    $row = $result[0];
} else {
    require_once "../include/check_post_key.php";
    $problem_id = intval($_POST['problem_id']);
    if (!(isset($_SESSION[$OJ_NAME . '_' . "p$problem_id"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
        exit();
    }

    $title = $_POST['title'];
    $title = str_replace(",", "&#44;", $title);
    $time_limit = $_POST['time_limit'];
    $memory_limit = $_POST['memory_limit'];
    $description = $_POST['description'];
    $description = str_replace("<p>", "", $description);
    $description = str_replace("</p>", "<br />", $description);
    $description = str_replace(",", "&#44;", $description);

    $input = $_POST['input'];
    $input = str_replace("<p>", "", $input);
    $input = str_replace("</p>", "<br />", $input);
    $input = str_replace(",", "&#44;", $input);

    $output = $_POST['output'];
    $output = str_replace("<p>", "", $output);
    $output = str_replace("</p>", "<br />", $output);
    $output = str_replace(",", "&#44;", $output);

    $sample_input = $_POST['sample_input'];
    $sample_output = $_POST['sample_output'];
    $hint = $_POST['hint'];
    $hint = str_replace("<p>", "", $hint);
    $hint = str_replace("</p>", "<br />", $hint);
    $hint = str_replace(",", "&#44;", $hint);

    $source = $_POST['source'];
    $spj = $_POST['spj'];

    if (get_magic_quotes_gpc()) {
        $title = stripslashes($title);
        $time_limit = stripslashes($time_limit);
        $memory_limit = stripslashes($memory_limit);
        $description = stripslashes($description);
        $input = stripslashes($input);
        $output = stripslashes($output);
        $sample_input = stripslashes($sample_input);
        $sample_output = stripslashes($sample_output);
        //$test_input = stripslashes($test_input);
        //$test_output = stripslashes($test_output);
        $hint = stripslashes($hint);
        $source = stripslashes($source);
        $spj = stripslashes($spj);
        $source = stripslashes($source);
    }

    $title = ($title);
    $description = RemoveXSS($description);
    $input = RemoveXSS($input);
    $output = RemoveXSS($output);
    $hint = RemoveXSS($hint);
    $basedir = $OJ_DATA . "/$problem_id";

    if ($sample_input && file_exists($basedir . "/sample.in")) {
        //mkdir($basedir);
        $fp = fopen($basedir . "/sample.in", "w");
        fputs($fp, preg_replace("(\r\n)", "\n", $sample_input));
        fclose($fp);

        $fp = fopen($basedir . "/sample.out", "w");
        fputs($fp, preg_replace("(\r\n)", "\n", $sample_output));
        fclose($fp);
    }

    $spj = intval($spj);

    $sql = "UPDATE `problem` set `title`=?,`time_limit`=?,`memory_limit`=?,
                   `description`=?,`input`=?,`output`=?,`sample_input`=?,`sample_output`=?,`hint`=?,`source`=?,`spj`=?,`in_date`=NOW()
            WHERE `problem_id`=?";

    @pdo_query($sql, $title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $problem_id);
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("Location:problem_list.php");
    exit(0);
}
?>
<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title class="sub-page-title">编辑问题</title>
<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">编辑问题</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form method="POST" action="problem_edit.php" class="layui-form">
              <div class="form-group">
                <input type=hidden name=problem_id value="<?php echo $problem_id ?>">
                <label for="title">标题</label>
                <input class="form-control" style="width:30%;" type="text" name="title" id="title" value="<?php echo htmlentities($row['title'], ENT_QUOTES, "
                  UTF-8 ") ?>" lay-verify="required">
              </div>
              <div class="form-group">
                <label for="time_limit">时间限制</label>
                <div class="input-group" style="width:30%;">
                  <input class="form-control" type="text" name="time_limit" id="time_limit" value="<?php echo htmlentities($row['time_limit'], ENT_QUOTES, 'UTF-8') ?>">
                  <div class="input-group-addon">秒</div>
                </div>
              </div>
              <div class="form-group">
                <label for="memory_limit">内存限制</label>
                <div class="input-group" style="width:30%;">
                <input class="form-control" type="text" name="memory_limit" id="memory_limit" value="<?php echo htmlentities($row['memory_limit'], ENT_QUOTES, 'UTF-8') ?>">
                  <div class="input-group-addon">兆字节</div>
                </div>
              </div>
              <div class="form-group">
                <label for="description">题目描述</label>
                <textarea class="kindeditor" rows="13" name="description" id="description" cols="80"><?php echo htmlentities($row['description'], ENT_QUOTES, "UTF-8") ?></textarea>
              </div>
              <div class="form-group">
                <label for="input">输入</label>
                <textarea class="kindeditor" rows="13" name="input" id="input" cols="80"><?php echo htmlentities($row['input'], ENT_QUOTES, "UTF-8") ?></textarea>
              </div>
              <div class="form-group">
                <label for="output">输出</label>
                <textarea class="kindeditor" rows="13" name="output" id="output" cols="80"><?php echo htmlentities($row['output'], ENT_QUOTES, "UTF-8") ?></textarea>
              </div>
              <div class="form-group">
                <label for="sample_input">样例输入</label>
                <textarea class="form-control" rows="13" name="sample_input" id="sample_input" cols="80"><?php echo htmlentities($row['sample_input'], ENT_QUOTES, "UTF-8") ?></textarea>
              </div>
              <div class="form-group">
                <label for="sample_output">样例输出</label>
                <textarea class="form-control" rows="13" name="sample_output" id="sample_output" cols="80"><?php echo htmlentities($row['sample_output'], ENT_QUOTES, "UTF-8") ?></textarea>
              </div>
              <div class="form-group">
                <label for="hint">提示</label>
                <textarea class="kindeditor" rows="13" name="hint" id="hint" cols="80"><?php echo htmlentities($row['hint'], ENT_QUOTES, "UTF-8") ?></textarea>
              </div>
              <div class="form-group">
                <h4>特殊裁判 相关功能尚未开发 请选择否</h4>
                特殊裁判的使用，请参考<a href='https://cn.bing.com/search?q=hustoj+special+judge' target='_blank'>搜索hustoj special judge</a>
                <br>
                <?php echo "No " ?>
                <input type=radio name="spj" value='0' <?php echo $row['spj'] == "0" ? "checked" : "" ?>>
                <?php echo "/ Yes " ?>
                <input type=radio name="spj" value='1' <?php echo $row['spj'] == "1" ? "checked" : "" ?>>
              </div>
              <div class="form-group">
                <label for="source">来源/分类</label>
                <textarea class="kindeditor" rows="13" name="source" id="source" cols="80"><?php echo htmlentities($row['source'], ENT_QUOTES, "UTF-8") ?></textarea>
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
    layui.use(['element','form'], function () {
      var element = layui.element,
      form = layui.form;
    });
  </script>
</body>

</html>