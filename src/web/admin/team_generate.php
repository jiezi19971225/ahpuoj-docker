<?php
require_once "admin-header.php";
require_once "js.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if (isset($_POST['prefix'])) {
    require_once "../include/check_post_key.php";
    $prefix = $_POST['prefix'];
    require_once "../include/my_func.inc.php";
    if (!is_valid_user_name($prefix)) {
        echo "Prefix is not valid.";
        exit(0);
    }
    $teamnumber = intval($_POST['teamnumber']);
    $pieces = explode("\n", trim($_POST['ulist']));
    if ($teamnumber > 0) {
        $team_account_info_html = "";
        $team_account_info_html .= "<table class=\"table\">";
        $team_account_info_html .= "<tr><td colspan=\"3\">复制这些账号分发</td></tr>";
        $team_account_info_html .= "<tr><td>team_name<td>login_id</td><td>password</td></tr>";
        for ($i = 1; $i <= $teamnumber; $i++) {

            $user_id = $prefix . ($i < 10 ? ('0' . $i) : $i);
            $password = strtoupper(substr(MD5($user_id . rand(0, 9999999)), 0, 10));
            if (isset($pieces[$i - 1]) && $pieces[$i - 1] != null) {
                $nick = $pieces[$i - 1];
            } else {
                $nick = "比赛账号";
            }

            if ($teamnumber == 1) {
                $user_id = $prefix;
            }

            $team_account_info_html .= "<tr><td>$nick<td>$user_id</td><td>$password</td></tr>";

            $password = pwGen($password);
            $email = "your_own_email@internet";

            $school = "安徽工程大学";
            $ip = ($_SERVER['REMOTE_ADDR']);
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
                $tmp_ip = explode(',', $REMOTE_ADDR);
                $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
            }
            $sql = "INSERT INTO `users`(" . "`user_id`,`email`,`ip`,`accesstime`,`password`,`reg_time`,`nick`,`school`)" .
                "VALUES(?,?,?,NOW(),?,NOW(),?,?)on DUPLICATE KEY UPDATE `email`=?,`ip`=?,`accesstime`=NOW(),`password`=?,`reg_time`=now(),nick=?,`school`=?";
            pdo_query($sql, $user_id, $email, $ip, $password, $nick, $school, $email, $ip, $password, $nick, $school);
        }
        $team_account_info_html .= "</table>";
        $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
        $_SESSION['team_account_info'] = $team_account_info_html;
        header("Location:team_generate.php");
        exit(0);
    }
}
?>
<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<title>比赛队账号生成器</title>
<body class="layui-layout-body">
	<div class="layui-layout layui-layout-admin">
		<?php require_once 'top_menubar.php';?>
		<?php require_once 'side_menubar.php';?>
		<div class="layui-body">
			<h3 class="sub-page-title">比赛队账号生成器</h3>
			<div class="container">
				<div class="panel panel-default">
					<div class="panel-body">
						<form action='team_generate.php' method="POST" class="layui-form">
							<div class="form-group">
								<label for="team">前缀</label>
								<input class="form-control" style="width:30%;" type="test" name="prefix" value="team" placeholder="请输入队伍前缀">
							</div>
							<div class="form-group">
								<label for="teambumber">队伍数量</label>
								<input class="form-control" style="width:30%;" type="text" name='teamnumber' value="5" lay-verify="required">
							</div>
							<div class="form-group">
								<label for="ulist">用户昵称</label>
								<textarea class="form-control" style="width:30%" ; name="ulist" rows="12" placeholder="预设队伍的昵称，每一行对应一个昵称"></textarea>
							</div>
							<?php require_once "../include/set_post_key.php";?>
							<button class="btn btn-primary" type="submit" lay-submit="">生成</button>
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

<?php
$status = flash_status_session();
if ($status == $OPERATOR_SUCCESS) {
    $team_account_info_html = $_SESSION['team_account_info'];
    echo "layer.open({
        type:1,
        area:['800px','750px'],
        title: '队伍账号信息',
        content: '$team_account_info_html',
    })";
    unset($_SESSION['team_account_info']);
} else if ($status == $OPERATOR_FAILURE) {
    $msg = $_SESSION['error_message'];
    echo "layer.msg('$msg',{time:5000});\n";

}
?>
		});
	</script>
</body>

</html>