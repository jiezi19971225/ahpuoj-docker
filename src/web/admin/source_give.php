<?php require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
    require_once "../include/check_post_key.php";
    $from = $_POST['from'];
    $to = $_POST['to'];
    $start = intval($_POST['start']);
    $end = intval($_POST['end']);
    $sql = "update `solution` set `user_id`=? where `user_id`=? and problem_id>=? and problem_id<=? and result=4";
    pdo_query($sql, $to, $from, $start, $end);
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header('Location:source_give.php');
    exit(0);
}
?>
<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>转移源码</title>

<body class="layui-layout-body">
	<div class="layui-layout layui-layout-admin">
		<?php require_once 'top_menubar.php';?>
		<?php require_once 'side_menubar.php';?>
		<div class="layui-body">
			<h3 class="sub-page-title">转移源码</h3>
			<div class="container">
				<div class="panel panel-default">
					<div class="panel-body">
						<form action="source_give.php" method="POST" class="layui-form">
							<div class="form-group">
								<label for="from">从</label>
								<input class="form-control" style="width:30%;" type="text" name="from" id="from" lay-verify="required" placeholder="请填入用户ID">
							</div>
							<div class="form-group">
								<label for="from">到</label>
								<input class="form-control" style="width:30%;" type="text" name="to" id="to" lay-verify="required" placeholder="请填入用户ID">
							</div>
							<div class="form-group">
								<label for="from">起始问题ID</label>
								<input class="form-control" style="width:30%;" type="text" name="start" id="start" lay-verify="required" placeholder="请填入起始ID">
							</div>
							<div class="form-group">
								<label for="from">终止问题ID</label>
								<input class="form-control" style="width:30%;" type="text" name="end" id="end" lay-verify="required" placeholder="请填入终止问题ID">
							</div>
							<?php require_once "../include/set_post_key.php";?>
							<button class="btn btn-primary" lay-submit="" type="submit">转移源码</button>
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