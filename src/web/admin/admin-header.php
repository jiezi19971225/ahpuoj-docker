
<?php
require_once "../include/db_info.inc.php";
require_once "../include/my_func.inc.php";
?>
<head>
	<meta charset="utf-8">
	 <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel=stylesheet href='./css/reset.css' type='text/css'>
	<link rel=stylesheet href='./css/admin.css' type='text/css'>
	<link rel="stylesheet" href="../template/<?php echo $OJ_TEMPLATE ?>/bootstrap.min.css" type='text/css'>
	<link rel=stylesheet href='../template/<?php echo $OJ_TEMPLATE ?>/layui/css/layui.css' type='text/css'>
</head>
<?php
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) ||
    isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) ||
    isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
?>