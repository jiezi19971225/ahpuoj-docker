<?php
require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

if (isset($_POST['title'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];

    if (get_magic_quotes_gpc()) {
        $title = stripslashes($title);
        $content = stripslashes($content);
    }

    $content = str_replace("<p>", "", $content);
    $content = str_replace("</p>", "<br />", $content);
    $content = str_replace(",", "&#44;", $content);

    $title = RemoveXSS($title);
    $content = RemoveXSS($content);

    $sql = "INSERT INTO news(`user_id`,`title`,`content`,`time`) VALUES(?,?,?,now())";
    if (pdo_query($sql, $user_id, $title, $content)) {
        $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    } else {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    }
    header('Location:news_list.php');
    exit(0);
} else {
    include_once "kindeditor.php";
    if (isset($_GET['cid'])) {
        $news_id = intval($_GET['cid']);
        $sql = "SELECT * FROM news WHERE `news_id`=?";
        $result = pdo_query($sql, $news_id);
        $row = $result[0];
        $title = $row['title'];
        $content = $row['content'];
        $defunct = $row['defunct'];
    }
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>添加新闻</title>
<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class='sub-page-title'>添加新闻</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form method="POST" action="news_add.php" class="admin-form-general layui-form">
              <div class="form-group">
                <label for="title">标题</label>
                <input class="form-control" type="text" id="new_title" name="title" value="<?php echo isset($title) ? $title . '-副本' : '' ?>"
                  placeholder="请输入新闻标题" style="width:30%;" lay-verify="required">
                <?php require_once "../include/set_post_key.php";?>
              </div>
              <div class="form-group">
              <label for="content">内容</label>
                <textarea class="kindeditor" name="content" id="content" rows="30">
                  <?php echo isset($content) ? $content : '' ?>
                </textarea>
              </div>
              <?php require_once "../include/set_post_key.php";?>
              <button type="submit" class="btn btn-primary" lay-submit="">提交</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
  <?php require_once 'js.php';?>
  <script>
    layui.use(['element', 'form'], function () {
      var element = layui.element,
      form = layui.form;
    });
  </script>
</body>

</html>