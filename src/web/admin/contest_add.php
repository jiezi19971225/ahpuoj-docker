<?php
require_once "admin-header.php";
require_once "../include/const.inc.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
include_once "kindeditor.php";

$description = "";
if (isset($_POST['startdate'])) {
    require_once "../include/check_post_key.php";

    $starttime = $_POST['startdate'] . " " . intval($_POST['shour']) . ":" . intval($_POST['sminute']) . ":00";
    $endtime = $_POST['enddate'] . " " . intval($_POST['ehour']) . ":" . intval($_POST['eminute']) . ":00";

    $title = $_POST['title'];
    $private = $_POST['private'];
    $description = $_POST['description'];

    if (get_magic_quotes_gpc()) {
        $title = stripslashes($title);
        $private = stripslashes($private);
        $description = stripslashes($description);
    }
    $lang = $_POST['lang'];
    $langmask = 0;
    foreach ($lang as $t) {
        $langmask += 1 << $t;
    }

    $langmask = ((1 << count($language_ext)) - 1) & (~$langmask);

    $sql = "INSERT INTO `contest`(`title`,`start_time`,`end_time`,`private`,`langmask`,`description`)
          VALUES(?,?,?,?,?,?)";

    $description = str_replace("<p>", "", $description);
    $description = str_replace("</p>", "<br />", $description);
    $description = str_replace(",", "&#44; ", $description);
    $contest_id = pdo_query($sql, $title, $starttime, $endtime, $private, $langmask, $description);

    $plist = trim($_POST['cproblem']);
    $pieces = explode(",", $plist);

    if (count($pieces) > 0 && intval($pieces[0]) > 0) {
        $sql_1 = "INSERT INTO `contest_problem`(`contest_id`,`problem_id`,`num`) VALUES (?,?,?)";
        $plist = "";
        for ($i = 0; $i < count($pieces); $i++) {
            if ($plist) {
                $plist .= ",";
            }

            $plist .= $pieces[$i];
            pdo_query($sql_1, $contest_id, $pieces[$i], $i);
        }
        $sql = "UPDATE `problem` SET defunct='N' WHERE `problem_id` IN ($plist)";
        pdo_query($sql);
    }

    $sql = "INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)";
    pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], "m$contest_id");
    $_SESSION[$OJ_NAME . '_' . "m$contest_id"] = true;
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header('location:contest_list.php');
    exit(0);
} else {
    if (isset($_GET['cid'])) {
        $contest_id = intval($_GET['cid']);
        $sql = "SELECT * FROM contest WHERE `contest_id`=?";
        $result = pdo_query($sql, $contest_id);
        $row = $result[0];
        $title = $row['title'] . "-副本";

        $private = $row['private'];
        $langmask = $row['langmask'];
        $description = $row['description'];

        $plist = "";
        $sql = "SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=? ORDER BY `num`";
        $result = pdo_query($sql, $contest_id);
        foreach ($result as $row) {
            if ($plist) {
                $plist = $plist . ',';
            }

            $plist = $plist . $row[0];
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
    } else if (isset($_POST['problem2contest'])) {
        $plist = "";
        sort($_POST['pid']);
        foreach ($_POST['pid'] as $value) {
            if ($plist) {
                $plist .= ',' . intval($value);
            } else {
                $plist = $value;
            }

        }
    } else if (isset($_GET['spid'])) {
        require_once "../include/check_get_key.php";
        $spid = intval($_GET['spid']);
        $plist = "";
        $sql = "SELECT `problem_id` FROM `problem` WHERE `problem_id`>=? ";
        $result = pdo_query($sql, $spid);
        foreach ($result as $row) {
            if ($plist) {
                $plist .= ',';
            }
            $plist .= $row[0];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>添加竞赛&作业</title>

<body class="layui-layout-body">
  <div class="layui-layout layui-layout-admin">
    <?php require_once 'top_menubar.php';?>
    <?php require_once 'side_menubar.php';?>
    <div class="layui-body">

      <h3 class="sub-page-title">添加竞赛&作业</h3>
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-body">
            <form method="POST" class="layui-form" role="form">
              <div class="form-group">
                <label for="title">竞赛&作业标题</label>
                <input class="form-control" style="width:40%;" type="text" name="title" id="title" value="<?php echo isset($title) ? $title : '' ?>" placeholder="请输入竞赛&作业标题" lay-verify="required">
              </div>
              <div class="row" style="margin-top:20px;">
                <p style="margin: 0 0 5px 15px;">
                  <b>竞赛&作业开始于</b>
                </p>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="startdate" id="startdate" value="<?php echo date('Y') . '-' . date('m') . '-' . date('d') ?>">
                      <div class="input-group-addon">日</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="shour" id="shour" value="<?php echo date('H') ?>">
                      <div class="input-group-addon">时</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="sminute" id="sminute" value="00">
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
                      <input lay-verify="required" class="form-control" type="text" name="enddate" id="enddate" value="<?php echo date('Y') . '-' . date('m') . '-' . date('d') ?>">
                      <div class="input-group-addon">日</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="ehour" id="ehour" value="<?php echo date('H') ?>">
                      <div class="input-group-addon">时</div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                  <div class="from-gruop">
                    <div class="input-group" style="width:60%;">
                      <input lay-verify="required" class="form-control" type="text" name="eminute" id="eminute" value="00">
                      <div class="input-group-addon">分</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group" style="margin-top:20px;">
                <label for="cproblem">竞赛&作业题目编号 ( 使用 , 半角逗号 分隔题目ID列表 )</label>
                <input class="form-control" placeholder="格式如:1000,1001,1002" type="text" name="cproblem" id="cproblem" value="<?php echo isset($plist) ? $plist : '' ?>">
              </div>
              <div class="form-group" style="margin-top:20px;">
                <label for="description">竞赛&作业-题目描述</label>
                <textarea class="kindeditor" rows="13" name="description" id="description" cols="80"><?php echo isset($description) ? $description : "" ?></textarea>
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


              <div class="form-group">
                <p>
                  <b>竞赛&作业模式</b>
                </p>
                个人
                <input type="radio" name="team_mode" id="team_mode" value="0" checked>
                团队
                <input type="radio" name="team_mode" id="team_mode" value="1">
              </div>

              <div class="form-group">
                <p>
                  <b>竞赛&作业公开度</b>
                </p>
                公开
                <input type="radio" name="private" id="private" value="0" checked>
                私有
                <input type="radio" name="private" id="private" value="1">
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
    layui.use(['element', 'layer', 'form'], function () {
      var element = layui.element,
        layer = layui.layer,
        form = layui.form;

    });
  </script>
</body>

</html>