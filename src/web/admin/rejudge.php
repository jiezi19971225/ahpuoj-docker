<?php require "admin-header.php";

if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if (isset($_POST['rjpid']) || isset($_POST['rjsid']) || isset($_POST['rjcid'])) {
    require_once "../include/check_post_key.php";
    if (isset($_POST['rjpid'])) {
        $rjpid = intval($_POST['rjpid']);
        $sql = "UPDATE `solution` SET `result`=1 WHERE `problem_id`=? and problem_id>0";
        pdo_query($sql, $rjpid);
        $sql = "delete from `sim` WHERE `s_id` in (select solution_id from solution where `problem_id`=?)";
        pdo_query($sql, $rjpid);
        $url = "../status.php?problem_id=" . $rjpid;
    } else if (isset($_POST['rjsid'])) {
        $rjsid = intval($_POST['rjsid']);
        $sql = "delete from `sim` WHERE `s_id`=?";
        pdo_query($sql, $rjsid);
        $sql = "UPDATE `solution` SET `result`=1 WHERE `solution_id`=? and problem_id>0";
        pdo_query($sql, $rjsid);
        $url = "../status.php?top=" . ($rjsid + 1);
    } else if (isset($_POST['rjcid'])) {
        $rjcid = intval($_POST['rjcid']);
        $sql = "UPDATE `solution` SET `result`=1 WHERE `contest_id`=? and problem_id>0";
        pdo_query($sql, $rjcid);
        $url = "../status.php?cid=" . ($rjcid);
    }
    header("Location:$url");
    echo str_repeat(" ", 4096);
    flush();
    if ($OJ_REDIS) {
        $redis = new Redis();
        $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
        if (isset($OJ_REDISAUTH)) {
            $redis->auth($OJ_REDISAUTH);
        }

        $sql = "select solution_id from solution where result=1 and problem_id>0";
        $result = pdo_query($sql);
        foreach ($result as $row) {
            echo $row['solution_id'] . "\n";
            $redis->lpush($OJ_REDISQNAME, $row['solution_id']);
        }
        $redis->close();
    }

}
?>

<!DOCTYPE html>


<html>
<?php require_once 'admin-header.php'?>
<title>重判题目</title>
<body class="layui-layout-body">
	<div class="layui-layout layui-layout-admin">
		<?php require_once 'top_menubar.php';?>
		<?php require_once 'side_menubar.php';?>
		<div class="layui-body">
			<h3 class="sub-page-title">重判题目</h3>
			<div class="container">
				<div class="panel panel-default">
					<div class="panel-body">

						<form action="rejudge.php" method="POST" role="form" class="layui-form">
							<div class="form-group">
								<label for="rjpid">问题ID</label>
								<input class="form-control" style="width:30%;" type="input" name="rjpid" id="rjpid" placeholder="请输入问题ID" lay-verify="required">
							</div>
							<?php require_once "../include/set_post_key.php";?>
							<button class="btn btn-primary" type="submit" lay-submit="">提交</button>
						</form>

						<form action="rejudge.php" method="POST" role="form" class="layui-form" style="margin-top:30px;">
							<div class="form-group">
								<label for="rjsid">提交ID</label>
								<input class="form-control" style="width:30%;" type="input" name="rjsid" id="rjsid" placeholder="请输入提交ID" lay-verify="required">
							</div>
							<input type=hidden name="postkey" value="<?php echo $_SESSION[$OJ_NAME . '_' . 'postkey'] ?>">
							<button class="btn btn-primary" type="submit" lay-submit="">提交</button>
						</form>

						<form action="rejudge.php" method="POST" role="form" class="layui-form" style="margin-top:30px;">
							<div class="form-group">
								<label for="rjcid">竞赛ID</label>
								<input class="form-control" style="width:30%;" type="input" name="rjcid" id="rjcid" placeholder="请输入竞赛ID" lay-verify="required">
							</div>
							<input type=hidden name="postkey" value="<?php echo $_SESSION[$OJ_NAME . '_' . 'postkey'] ?>">
							<button class="btn btn-primary" type="submit" lay-submit="">提交</button>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
	<?php require_once 'js.php';?>
	<script>
		layui.use(['element','form','layer'], function () {
			var element = layui.element,
			form = layui.form,
			layer = layui.layer;

		});
	</script>
</body>

</html>