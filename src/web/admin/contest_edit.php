<?php

require_once "admin-header.php";
require_once "../include/const.inc.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}

include_once "kindeditor.php";

if (isset($_POST['startdate'])) {
    require_once "../include/check_post_key.php";

    $contest_id = intval($_POST['cid']);

    $starttime = $_POST['startdate'] . " " . intval($_POST['shour']) . ":" . intval($_POST['sminute']) . ":00";
    $endtime = $_POST['enddate'] . " " . intval($_POST['ehour']) . ":" . intval($_POST['eminute']) . ":00";

    $title = $_POST['title'];
    $description = $_POST['description'];

    if (get_magic_quotes_gpc()) {
        $title = stripslashes($title);
        $password = stripslashes($password);
        $description = stripslashes($description);
    }

    $lang = $_POST['lang'];
    $langmask = 0;
    foreach ($lang as $t) {
        $langmask += 1 << $t;
    }

    $langmask = ((1 << count($language_ext)) - 1) & (~$langmask);

    if (!(isset($_SESSION[$OJ_NAME . '_' . "m$contest_id"]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "没有权限";
        header('Location:contest_list.php');
        exit(0);
    }

    $description = str_replace("<p>", "", $description);
    $description = str_replace("</p>", "<br />", $description);
    $description = str_replace(",", "&#44;", $description);

    $sql = "UPDATE `contest` SET `title`=?,`description`=?,`start_time`=?,`end_time`=?,`langmask`=? WHERE `contest_id`=?";
    pdo_query($sql, $title, $description, $starttime, $endtime, $langmask, $contest_id);
    $sql = "DELETE FROM `contest_problem` WHERE `contest_id`=?";
    pdo_query($sql, $contest_id);
    $plist = trim($_POST['cproblem']);
    $pieces = explode(',', $plist);
    if (count($pieces) > 0 && strlen($pieces[0]) > 0) {
        $sql_1 = "INSERT INTO `contest_problem`(`contest_id`,`problem_id`,`num`) VALUES (?,?,?)";
        for ($i = 0; $i < count($pieces); $i++) {
            pdo_query($sql_1, $contest_id, intval($pieces[$i]), $i);
        }

        pdo_query("update solution set num=-1 where contest_id=?", $contest_id);

        $plist = "";
        for ($i = 0; $i < count($pieces); $i++) {
            if ($plist) {
                $plist .= ",";
            }

            $plist .= $pieces[$i];
            $sql_2 = "update solution set num=? where contest_id=? and problem_id=?;";
            pdo_query($sql_2, $i, $contest_id, $pieces[$i]);
        }

        $sql = "update `problem` set defunct='N' where `problem_id` in ($plist)";
        pdo_query($sql);
    }

    $sql = "DELETE FROM `privilege` WHERE `rightstr`=?";
    pdo_query($sql, "c$contest_id");
    $pieces = explode("\n", trim($_POST['ulist']));

    if (count($pieces) > 0 && strlen($pieces[0]) > 0) {
        $sql_1 = "INSERT INTO `privilege`(`user_id`,`rightstr`) VALUES (?,?)";
        for ($i = 0; $i < count($pieces); $i++) {
            pdo_query($sql_1, trim($pieces[$i]), "c$contest_id");
        }
    }

    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header('Location:contest_list.php');
    exit(0);
} else {
    $contest_id = intval($_GET['cid']);
    $sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
    $result = pdo_query($sql, $contest_id);

    if (count($result) != 1) {
        echo "No such Contest!";
        exit(0);
    }

    $row = $result[0];
    $starttime = $row['start_time'];
    $endtime = $row['end_time'];
    $private = $row['private'];
    $team_mode = $row['team_mode'];
    $password = $row['password'];
    $langmask = $row['langmask'];
    $description = $row['description'];
    $title = htmlentities($row['title'], ENT_QUOTES, "UTF-8");

    $plist = "";
    $sql = "SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=? ORDER BY `num`";
    $result = pdo_query($sql, $contest_id);

    foreach ($result as $row) {
        if ($plist) {
            $plist .= ",";
        }

        $plist .= $row[0];
    }

    $ulist = "";
    $sql = "SELECT `user_id` FROM `privilege` WHERE `rightstr`=? order by user_id";
    $result = pdo_query($sql, "c$contest_id");

    foreach ($result as $row) {
        if ($ulist) {
            $ulist .= "\n";
        }

        $ulist .= $row[0];
    }
}
?>

<!DOCTYPE html>


<html>
<?php require_once 'admin-header.php'?>
<title>编辑竞赛&作业</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">
      <h3 class="sub-page-title">编辑竞赛&作业</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form method="POST" class="layui-form" role="form" action="contest_edit.php">
              <input type=hidden name='cid' value=<?php echo $contest_id ?>>
              <div class="form-group">
                <label for="title">竞赛&作业标题</label>
                <input class="form-control" style="width:40%;" type="text" name="title" id="title" value="<?php echo $title ?>" placeholder="请输入竞赛&作业标题"
                  lay-verify="required">
              </div>
              <div class="row" style="margin-top:20px;">
                <p style="margin: 0 0 5px 15px;">
                <b>竞赛&作业开始于</b>
                </p>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="startdate" id="startdate" value="<?php echo substr($starttime, 0, 10) ?>">
                      <div class="input-group-addon">日</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="shour" id="shour" value="<?php echo substr($starttime, 11, 2) ?>">
                      <div class="input-group-addon">时</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="sminute" id="sminute" value="<?php echo substr($starttime, 14, 2) ?>">
                      <div class="input-group-addon">分</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row" style="margin-top:20px;">
                <p style="margin: 0 0 5px 15px;">
                  <b>竞赛&作业结束于</b> ( 结束时间若为 24:00 需要设置为次日 0:00 否则会导致添加失败 )
                </p>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="enddate" id="enddate" value="<?php echo substr($endtime, 0, 10) ?>">
                      <div class="input-group-addon">日</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="ehour" id="ehour" value="<?php echo substr($endtime, 11, 2) ?>">
                      <div class="input-group-addon">时</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="eminute" id="eminute" value="<?php echo substr($endtime, 14, 2) ?>">
                      <div class="input-group-addon">分</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group" style="margin-top:20px;">
                <label for="cproblem">竞赛&作业-题目编号 ( 使用 , 半角逗号 分隔题目ID列表 )</label>
                <input class="form-control" placeholder="格式如:1000,1001,1002" type="text" name="cproblem" id="cproblem" value="<?php echo $plist ?>">
              </div>
              <div class="form-group" style="margin-top:20px;">
                <label for="description">竞赛&作业-题目描述</label>
                <textarea class="kindeditor" rows="13" name="description" id="description" cols="80"><?php echo htmlentities($description, ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <p>
                  <b>竞赛&作业语言</b>
                </p>
                <?php
$lang_count = count($language_ext);
$lang = (~((int) $langmask)) & ((1 << $lang_count) - 1);

if (isset($_COOKIE['lastlang'])) {
    $lastlang = $_COOKIE['lastlang'];
} else {
    $lastlang = 0;
}

for ($i = 0; $i < $lang_count; $i++) {
    echo "<input type='checkbox'  value='$i' " . ($lang & (1 << $i) ? 'checked' : '') . " name='lang[]' title=$language_name[$i] >";
}
?>
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
    layui.use(['element', 'form'], function () {
      var element = layui.element,
        form = layui.form;

    });
  </script>
</body>

</html>