<?php require_once "admin-header.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
?>
<title>导出问题</title>
<h3 class="sub-page-title">导出问题</h3>

<div class="container">
	<div class="panel panel-default">
		<div class="panel-body">
		<form action='problem_export_xml.php' method=post>
			<div class="form-group">
				<label for="start">起始问题ID</label>
				<input class="form-control" type="text" style="width:30%;" name="start" id="start" value="1000">
			</div>
			<div class="form-group">
				<label for="end">截止问题ID</label>
				<input class="form-control" type="text" style="width:30%;" name="end" id="end" value="1000">
			</div>
			<div class="form-group">
				<label for="in">题目列表</label>
				<input class="form-control" type="text" style="width:30%;" name="in" id="in" value="">
			</div>
			<div class="form-group">
				<input type='hidden' name='do' value='do'>
				<?php require_once "../include/set_post_key.php";?>
				<input class="btn btn-primary" type="submit" name=submit value='导出'>
				<input class="btn btn-primary" type="submit" value='下载'>
			</div>
		</form>
		<p>
			起始截止方式在题目列表为空时有效</br>
			如果使用题目列表，起始截止方式会被忽略</br>
			题目列表使用 "," 半角逗号划分问题ID，格式如[1000,1001,1002]
		</p>
		</div>
	</div>
</div>
