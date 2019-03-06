<!DOCTYPE html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">
	<title>评测姬</title>
	<?php include "template/$OJ_TEMPLATE/css.php";?>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	<?php include "template/$OJ_TEMPLATE/nav.php";?>
	<div class="main-content">
		<div class="general-form-wrapper">
			<form id="simform" class="form-inline" action="status.php" method="get">
				<div class="form-group">
					<label for="problem_id">题目编号</label>
					<input class="form-control noradius" type="text" name="problem_id" id="problem_id" value='<?php echo htmlspecialchars($problem_id, ENT_QUOTES) ?>'>
				</div>
				<div class="form-group">
					<label for="user_id">用户</label>
					<input class="form-control noradius" type="text" name="user_id" id="user_id" value='<?php echo htmlspecialchars($user_id, ENT_QUOTES) ?>'>
				</div>
				<?php if (isset($cid)) {
    echo "<input type='hidden' name='cid' value='$cid'>";
}
?>
				<div class="form-group">
					<label for="language">语言</label>
					<select class="form-control noradius select-sm" size="1" name="language" id="language">
						<option value="-1">All</option>
						<?php
if (isset($_GET['language'])) {
    $selectedLang = intval($_GET['language']);
} else {
    $selectedLang = -1;
}
$lang_count = count($language_ext);
$langmask = $OJ_LANGMASK;
$lang = (~((int) $langmask)) & ((1 << ($lang_count)) - 1);
for ($i = 0; $i < $lang_count; $i++) {
    if ($lang & (1 << $i)) {
        echo "<option value=$i " . ($selectedLang == $i ? "selected" : "") . ">
" . $language_name[$i] . "
</option>";
    }

}
?>
					</select>
				</div>
				<div class="form-group">
					<label for="language">结果</label>
					<select class="form-control noradius select-sm" size="1" name="jresult" id="jresult">
						<?php if (isset($_GET['jresult'])) {
    $jresult_get = intval($_GET['jresult']);
} else {
    $jresult_get = -1;
}

if ($jresult_get >= 12 || $jresult_get < 0) {
    $jresult_get = -1;
}

if ($jresult_get == -1) {
    echo "<option value='-1' selected>All</option>";
} else {
    echo "<option value='-1'>All</option>";
}

for ($j = 0; $j < 12; $j++) {
    $i = ($j + 4) % 12;
    if ($i == $jresult_get) {
        echo "<option value='" . strval($jresult_get) . "' selected>" . $jresult[$i] . "</option>";
    } else {
        echo "<option value='" . strval($i) . "'>" . $jresult[$i] . "</option>";
    }

}
?>
					</select>
				</div>

						<?php if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'source_browser'])) {
    if (isset($_GET['showsim'])) {
        $showsim = intval($_GET['showsim']);
    } else {
        $showsim = 0;
    }
    echo "<div class='form-group'>
					<label>SIM</label>
					<select id=\"appendedInputButton\" class=\"form-control noradius select-sm\" name=showsim onchange=\"document.getElementById('simform').submit();\">
						<option value=0 " . ($showsim == 0 ? 'selected' : '') . ">All</option>
						<option value=50 " . ($showsim == 50 ? 'selected' : '') . ">50</option>
						<option value=60 " . ($showsim == 60 ? 'selected' : '') . ">60</option>
						<option value=70 " . ($showsim == 70 ? 'selected' : '') . ">70</option>
						<option value=80 " . ($showsim == 80 ? 'selected' : '') . ">80</option>
						<option value=90 " . ($showsim == 90 ? 'selected' : '') . ">90</option>
						<option value=100 " . ($showsim == 100 ? 'selected' : '') . ">100</option>
					</select>
			</div>";
}
?>
				<button type='submit' class='btn btn-primary noradius'>
				<i class='glyphicon glyphicon-search'></i>
				</button>
			</form>

			</div>
		<div>
			<table id=result-tab class="table table-striped content-box-header" align=center width=80%>
				<thead>
					<tr class='toprow'>
						<th>提交编号</th>
						<th>用户</th>
						<th>问题</th>
						<th>结果</th>
						<th class='hidden-xs'>内存</th>
						<th class='hidden-xs'>耗时</th>
						<th class='hidden-xs'>语言</th>
						<th class='hidden-xs'>代码长度</th>
						<th>提交时间</th>
						<th class='hidden-xs'>判题机</th>
					</tr>
				</thead>
				<tbody>
					<?php
foreach ($view_status as $row) {
    echo "<tr>";
    foreach ($row as $key => $table_cell) {
        if ($key > 3 && $key != 8) {
            echo "<td class='hidden-xs'>";
        } else {
            echo "<td>";
        }

        echo $table_cell;
        echo "</td>";
    }
    echo "</tr>";
}
?>
				</tbody>
			</table>
		</div>

		<div class="status-buttons-wrapper">
				<?php echo "<a class='btn btn-primary' href=status.php?" . $str2 . ">首页</a>";
if (isset($_GET['prevtop'])) {
    echo "<a class='btn btn-primary' href=status.php?" . $str2 . "&top=" . intval($_GET['prevtop']) . ">上一页</a>";
} else {
    echo "<a class='btn btn-primary' href=status.php?" . $str2 . "&top=" . ($top + 20) . ">上一页</a>";
}

echo "<a class='btn btn-primary' href=status.php?" . $str2 . "&top=" . $bottom . "&prevtop=$top>下一页</a>";
?>
	</div>

	</div>

	<?php include "template/$OJ_TEMPLATE/js.php";?>
	<script>var i = 0;
		var judge_result = [<?php
foreach ($judge_result as $result) {
    echo "'$result',";
}
?> ''];

		var judge_color = [<?php
foreach ($judge_color as $result) {
    echo "'$result',";
}
?> ''];
	</script>
	<script src="template/<?php echo $OJ_TEMPLATE ?>/auto_refresh.js?v=0.34"></script>
</body>

</html>