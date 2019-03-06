<?php require "admin-header.php";

if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
function writable($path)
{
    $ret = false;
    $fp = fopen($path . "/testifwritable.tst", "w");
    $ret = !($fp === false);
    fclose($fp);
    unlink($path . "/testifwritable.tst");
    return $ret;
}
if (isset($_POST['from'])) {
    require_once "../include/check_post_key.php";
    $from = intval($_POST['from']);
    $to = intval($_POST['to']);
    $row = 0;
    if ($result = pdo_query("select 1 from problem where problem_id=?", $to)) {
        $row = count($result);

    }

    if ($row == 0 && rename("$OJ_DATA/$from", "$OJ_DATA/$to")) {
        // 重排后的题目ID不存在 而且原先题目ID存在
        $sql = "UPDATE `problem` SET `problem_id`=? WHERE `problem_id`=?";
        // 原先的题目ID不存在
        if (!pdo_query($sql, $to, $from)) {
            rename("$OJ_DATA/$to", "$OJ_DATA/$from");
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "操作失败";
            header('Location:problem_changeid.php');
            exit(1);
        }
        $sql = "UPDATE `solution` SET `problem_id`=? WHERE `problem_id`=?";
        pdo_query($sql, $to, $from);
        $sql = "UPDATE `contest_problem` SET `problem_id`=? WHERE `problem_id`=?";
        pdo_query($sql, $to, $from);
        $sql = "UPDATE `topic` SET `pid`=? WHERE `pid`=?";
        pdo_query($sql, $to, $from);

        $sql = "select max(problem_id) from problem";
        if ($result = pdo_query($sql)) {
            $f = $result[0];
            $nextid = $f[0] + 1;
            $sql = "ALTER TABLE problem AUTO_INCREMENT = ?";
            pdo_query($sql, $nextid);
        }

        $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    } else {
        // 重排后的题目ID存在 不能重排
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "操作失败，问题不存在或者问题ID冲突";
    }
    header('Location:problem_changeid.php');
    exit(0);
}
?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title class="sub-page-title">重排题目</title>

<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <?php require_once 'top_menubar.php';?>
        <?php require_once 'side_menubar.php';?>
        <div class="layui-body">
            <h3 class="sub-page-title">重排题目</h3>
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="problem_changeid.php" method="POST" role="form" class="layui-form">
                            <div class="form-group">
                                <label for="from">原题目ID</label>
                                <input type="text" class="form-control" style="width:30%;" name="from" id="from" placeholder="请填入问题ID" lay-verify="required">
                            </div>
                            <div class="form-group">
                                <label for="to">移动后题目ID</label>
                                <input type="text" class="form-control" style="width:30%;" name="to" id="to" placeholder="请填入问题ID" lay-verify="required">
                            </div>
                            <?php require_once "../include/set_post_key.php";?>
                            <button class="btn btn-primary" type="submit" lay-submit="">提交</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'js.php';?>
        <script>
            layui.use(['element', 'layer', 'form'], function () {
                var element = layui.element,
                    layer = layui.layer;

                <?php

if (!writable($OJ_DATA)) {
    echo " layer.msg( '$OJ_DATA 添加到 php.ini 设置中的 open_basedir 选项 ,或者你需要执行 \n chmod 775 -R $OJ_DATA && chgrp -R www-data $OJ_DATA \n 你现在无法使用该功能')";
}

$status = flash_status_session();

if ($status == $OPERATOR_SUCCESS) {
    echo "layer.msg('操作成功');\n";
} else if ($status == $OPERATOR_FAILURE) {
    $msg = $_SESSION['error_message'];
    echo "layer.msg('$msg',{time:5000});\n";

}
?>

            });
        </script>
</body>

</html>