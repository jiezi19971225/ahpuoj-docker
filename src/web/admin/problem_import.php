<?php function writable($path)
{
    $ret = false;
    $fp = fopen($path . "/testifwritable.tst", "w");
    $ret = !($fp === false);
    fclose($fp);
    unlink($path . "/testifwritable.tst");
    return $ret;
}
require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
$maxfile = min(ini_get("upload_max_filesize"), ini_get("post_max_size"));

?>

<!DOCTYPE html>

<html>
<?php require_once 'admin-header.php'?>
<title>导入问题</title>

<body class="layui-layout-body">
	<div class="layui-layout layui-layout-admin">
		<?php require_once 'top_menubar.php';?>
		<?php require_once 'side_menubar.php';?>
		<div class="layui-body">
			<h3 class="sub-page-title">导入问题</h3>

			<div class="container">

				<div class="panel panel-default">
					<div class="panel-body">
						<p>
							导入免费问题集数据，请保证文件大小小于
							<?php echo $maxfile ?>，或者在 php.ini 中设置 upload_max_filesize 和 post_max_size。 如果导入10M以上的大文件失败，尝试增加 php.ini 中 memory_limit 的大小。
						</p>
						<?php
$show_form = true;
if (!writable($OJ_DATA)) {
    echo " 你需要将 $OJ_DATA 添加到 php.ini 设置中的 open_basedir 选项 ,<br>
				或者你需要执行:<br>
				   <b>chmod 775 -R $OJ_DATA && chgrp -R www-data $OJ_DATA</b><br>
				你现在无法使用该功能.<br>";
    $show_form = false;
}
if (!file_exists("../upload")) {
    mkdir("../upload");
}

if (!writable("../upload")) {

    echo "../upload 无法写入, <b>请对其执行 chmod 770</b><br>";
    $show_form = false;
}
if ($show_form) {
    ?>
							<br>
							<form action='problem_import_xml.php' method="POST" enctype="multipart/form-data">
								<div class="form-group">
									<label for="fps">导入问题</label>
									<input class="form-control" type=file name="fps" id="fps">
								</div>
								<?php require_once "../include/set_post_key.php";?>
								<button class="btn btn-primary" type="submit">导入</button>
							</form>
							<?php

}

?>
							<p>免费题目
								<a href="https://github.com/zhblue/freeproblemset/tree/master/fps-examples" target="_blank">下载</a>
							</p>
							<p>更多题目请到
								<a href="http://tk.hustoj.com/" target="_blank">TK 题库</a> 查看，部分免费，部分收费。</p>
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