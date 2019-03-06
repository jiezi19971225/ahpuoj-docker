<?php
require "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if (isset($_POST['ulist'])) {
    require_once "../include/check_post_key.php";
    $password = pwGen("123456");
    $ip = $_SERVER['REMOTE_ADDR'];
    $school = "安徽工程大学";
    $pieces = explode("\n", trim($_POST['ulist']));
    if (count($pieces) > 0 && strlen($pieces[0]) > 0) {
        $sql_general = "INSERT INTO `users`(`user_id`,`nick`,`school`,`password`,`ip`,`accesstime`,`reg_time`) VALUES (?,?,?,?,?,NOW(),NOW())";
        foreach ($pieces as $row) {
            $user_id = trim($row);
            $can_insert = true;
            $sql = "SELECT 1 FROM `users` WHERE user_id = ?";
            $result = pdo_query($sql, $user_id);
            if (count($result) > 0) {
                $can_insert = false;
            }
            if ($can_insert) {
                pdo_query($sql_general, $user_id, $user_id, $school, $password, $ip);
            }
        }
    }
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("Location:user_list.php");
    exit(0);
}
?>

<!DOCTYPE html>


<html>
<?php require_once 'admin-header.php'?>
<title>用户账号生成器</title>

<body class="layui-layout-body">
	<div class="layui-layout layui-layout-admin">
		<?php require_once 'top_menubar.php';?>
		<?php require_once 'side_menubar.php';?>
		<div class="layui-body">
			<h3 class="sub-page-title">用户账号生成器</h3>
			<div class="container">
				<div class="panel panel-default">
					<div class="panel-body">
						<form action='user_generate.php' method="POST">
							<div class="form-group">
								<textarea class="form-control" style="width:30%" ; name="ulist" rows="30" placeholder="可以将学生学号从Excel整列复制过来，批量生成用户账号，初始密码默认为123456。"></textarea>
							</div>
							<?php require_once "../include/set_post_key.php";?>
							<button class="btn btn-primary" type="submit">生成</button>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
	<?php require_once 'js.php';?>
	<script>
		layui.use('element', function () {
			var element = layui.element;

		});
	</script>
</body>

</html>