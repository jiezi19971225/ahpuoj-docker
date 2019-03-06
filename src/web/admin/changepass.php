<?php require_once "admin-header.php";?>
<?php if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'password_setter']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if (isset($_POST['user_id'])) {
    require_once "../include/check_post_key.php";
    $user_id = $_POST['user_id'];
    $passwd = $_POST['passwd'];
    if (get_magic_quotes_gpc()) {
        $user_id = stripslashes($user_id);
        $passwd = stripslashes($passwd);
    }
    $passwd = pwGen($passwd);
    $sql = "update `users` set `password`=? where `user_id`=?  and user_id not in( select user_id from privilege where rightstr='administrator') ";
    if (pdo_query($sql, $passwd, $user_id)) {
        $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    } else {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "重设密码失败，该用户不存在或者为管理员";
    }
    header('Location:changepass.php');
    exit(0);
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>重设密码</title>
<body class="layui-layout-body">
	<div class="layui-layout layui-layout-admin">
		<?php require_once 'top_menubar.php';?>
		<?php require_once 'side_menubar.php';?>
		<div class="layui-body">
			<h3 class='sub-page-title'>重设密码</h3>
			<div class="container">
				<div class="panel panel-default">
					<div class="panel-body">
						<form action="changepass.php" method="POST" class="layui-form">
							<div class="form-group">
								<label for="user_id">用户名</label>
								<input class="form-control" type="text" name="user_id" id="user_id" style="width:30%;" placeholder="请输入用户ID" lay-verify="required">
							</div>
							<div class="form-group">
								<label for="passwd">密码</label>
								<input class="form-control" type=text size=10 name="passwd" id="passwd" style="width:30%;" placeholder="请输入用户密码" lay-verify="required">
							</div>
							<?php require_once "../include/set_post_key.php";?>
							<button class="btn btn-primary" type="submit" lay-submit="">更改</button>
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