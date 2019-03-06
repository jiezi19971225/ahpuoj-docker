<?php
require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if (isset($_POST['user_id'])) {
    require_once "../include/check_post_key.php";
    $user_id = $_POST['user_id'];
    $sql = "SELECT 1 FROM `users` WHERE user_id = ?";

    $result = pdo_query($sql, $user_id);
    // 用户存在
    if (count($result)) {
        $rightstr = $_POST['rightstr'];
        if (isset($_POST['contest'])) {
            $rightstr = "c$rightstr";
        }

        if (isset($_POST['psv'])) {
            $rightstr = "s$rightstr";
        }

        $sql = "insert into `privilege` values(?,?,'N')";
        $rows = pdo_query($sql, $user_id, $rightstr);
        $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    } else {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "授权失败，用户不存在";
    }
    header('Location:privilege_list.php');
    exit(0);
}

?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>权限授予</title>
<body class="layui-layout-body">
	<div class="layui-layout layui-layout-admin">
		<?php require_once 'top_menubar.php';?>
		<?php require_once 'side_menubar.php';?>
		<div class="layui-body">
			<h3 class="sub-page-title">权限授予</h3>
			<div class="container">
				<div class="panel panel-default">
					<div class="panel-body">
						<p style="font-size:16px;"><b>给予用户权限</b></p>
						<form method="POST" role="form" class="layui-form">
							<div class="form-group">
								<label for="user_id">用户ID</label>
								<input class="form-control" style="width:30%;" type="text" name="user_id" id="user_id" placeholder="请填入用户ID" lay-verify="required">
							</div>
							<div class="form-group">
								<label for="user_id">权限</label>
								<select class="form-control" style="width:30%;" name="rightstr" id="rightstr">
									<?php
$rightarray = array("administrator", "problem_editor", "contest_creator", "source_browser", "password_setter");
while (list($key, $val) = each($rightarray)) {
    if (isset($rightstr) && ($rightstr == $val)) {
        echo '<option value="' . $val . '" selected>' . $val . '</option>';
    } else {
        echo '<option value="' . $val . '">' . $val . '</option>';
    }
}
?>
								</select>
							</div>
							<?php require "../include/set_post_key.php";?>
							<button class="btn btn-primary" type="submit" lay-submit="">添加</button>
							<p>给指定用户增加权限，包括管理员、题目添加者、比赛组织者、代码查看者、密码设置者等权限。</p>
						</form>

						<p style="font-size:16px; margin-top:20px;"><b>给予用户查看指定问题代码的权限</b></p>
						<form method="POST" role="form" class="layui-form">
							<div class="form-group">
								<label for="user_id">用户ID</label>
								<input class="form-control" style="width:30%;" type="text" name="user_id" id="user_id" lay-verify="required" placeholder="请填入用户ID">
							</div>
							<div class="form-group">
								<label for="user_id">问题ID</label>
								<input class="form-control" type="text" style="width:30%;" name="rightstr" id="rightstr" lay-verify="required" placeholder="请填入问题ID">
							</div>
							<input type='hidden' name='psv' value='do'>
							<input type="hidden" name="postkey" value="<?php echo $_SESSION[$OJ_NAME . '_' . 'postkey'] ?>">
							<button class="btn btn-primary" type="submit" lay-submit="">添加</button>
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
			form = layui.form,
			layer = layui.layer;
			<?php
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