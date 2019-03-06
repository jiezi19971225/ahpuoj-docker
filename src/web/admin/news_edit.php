<?php
require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

include_once "kindeditor.php";

if (isset($_POST['id'])) {
    require_once "../include/check_post_key.php";

    $news_id = intval($_POST['id']);
    $title = $_POST['title'];
    $content = $_POST['content'];

    $content = str_replace("<p>", "", $content);
    $content = str_replace("</p>", "<br />", $content);
    $content = str_replace(",", "&#44;", $content);

    $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];

    if (get_magic_quotes_gpc()) {
        $title = stripslashes($title);
        $content = stripslashes($content);
    }

    $title = RemoveXSS($title);
    $content = RemoveXSS($content);

    $sql = "UPDATE `news` SET `title`=?,`time`=now(),`content`=?,`user_id`=? WHERE `news_id`=?";
    pdo_query($sql, $title, $content, $user_id, $news_id);

    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("location:news_list.php");
    exit(0);
} else {
    $news_id = intval($_GET['id']);
    $sql = "SELECT * FROM `news` WHERE `news_id`=?";
    $result = pdo_query($sql, $news_id);
    if (count($result) != 1) {
        header("location:404.php");
        exit(0);
    }

    $row = $result[0];
    $title = htmlentities($row['title'], ENT_QUOTES, "UTF-8");
    $content = $row['content'];
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>编辑新闻</title>
<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class='sub-page-title'>编辑新闻</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form method="POST" action="news_edit.php" class="admin-form-general layui-form">
              <div class="form-group">
                <label for="title">标题</label>
                <input class="form-control" type="text" id="title" name="title" value="<?php echo $title ?>"
                  placeholder="请输入新闻标题" style="width:30%;" lay-verify="required">
                <?php require_once "../include/set_post_key.php";?>
              </div>
              <div class="form-group">
              <label for="content">内容</label>
                <textarea class="kindeditor" name="content" id="content" rows="30">
                <?php echo htmlentities($content, ENT_QUOTES, "UTF-8") ?>
              </textarea>
              </div>
              <input type="hidden" name="id" value="<?php echo $news_id ?>">
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
    layui.use(['element','form'], function () {
      var element = layui.element,
      form = layui.form;
    });
  </script>
</body>

</html>