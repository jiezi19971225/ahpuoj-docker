<!DOCTYPE html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">
	<title>提交页面</title>
	<?php include "template/$OJ_TEMPLATE/css.php";?>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	<?php include "template/$OJ_TEMPLATE/nav.php";?>
	<div class="main-content" id="submit-page-content">
		<!-- Main component for a primary marketing message or call to action -->
		<script src="include/checksource.js"></script>
		<form id="frmSolution" action="submit.php" method="post" onsubmit='do_submit()' class="form-horizontal">
<?php
if (isset($id)) {
    ?>
			<h3 class="submit-title">问题
				<?php echo " $id" ?>
			</h3>
			<input id=problem_id type='hidden' value='<?php echo $id ?>' name="id">
<?php
} else {
    ?>
			<h3 class="submit-title">问题
				<?php echo " $id " ?>竞赛
				<?php echo $cid ?>
			</h3>
			<input id="cid" type='hidden' value='<?php echo $cid ?>' name="cid">
			<input id="pid" type='hidden' value='<?php echo $pid ?>' name="pid">
			<?php
}?>
			<div class="form-group" style="width: 300px;margin: 0 auto 20px;">
				<label for="language" class="control-label col-xs-3 col-sm-3 col-md-3 col-lg-3">语言</label>
				<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
				<select class="form-control" id="language" name="language" onChange="reloadtemplate($(this).val());">
					<?php
$lang_count = count($language_ext);
if (isset($_GET['langmask'])) {
    $langmask = $_GET['langmask'];
} else {
    $langmask = $OJ_LANGMASK;
}

$lang = (~((int) $langmask)) & ((1 << ($lang_count)) - 1);
if (isset($_COOKIE['lastlang'])) {
    $lastlang = $_COOKIE['lastlang'];
} else {
    $lastlang = 0;
}

for ($i = 0; $i < $lang_count; $i++) {
    if ($lang & (1 << $i)) {
        echo "<option value=$i " . ($lastlang == $i ? "selected" : "") . ">
" . $language_name[$i] . "
</option>";
    }

}
?>
				</select>
				</div>
			</div>
			<div class="form-group" style="padding:0 50px;">
				<pre style="width:100%;height:600px;" cols=180 rows=20 id="source"><?php echo htmlentities($view_src, ENT_QUOTES, "UTF-8") ?></pre>
				<input type="hidden" id="hide_source" name="source" value="" />
			</div>
<?php
if (isset($OJ_TEST_RUN) && $OJ_TEST_RUN) {
    ?>
			<div class="form-group" style="margin-top: 50px;">
				输入:
				<textarea style="width:30%;resize:none;" cols=40 rows=5 id="input_text" name="input_text"><?php echo $view_sample_input ?></textarea>
				输出:
				<textarea style="width:30%;resize:none;" cols=10 rows=5 id="out" name="out">结果应为:<?php echo $view_sample_output ?></textarea>
			</div>
<?php
}
?>


<?php
if ($OJ_VCODE) {
    ?>
	<div class="form-group" style="margin-top: 50px;">
		<label for="vcode" >验证码</label>
<input name="vcode" id="vcode" class="form-control input-sm noradius" type="text" style="display:inline-block;width:100px;"><img id="vcode" alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()">
</div>
<?php
}
?>
			<div class="form-group">
				<input id="Submit" class="btn btn-info" type=button value="提交" onclick="do_submit();">

<?php
if (isset($OJ_ENCODE_SUBMIT) && $OJ_ENCODE_SUBMIT) {
    ?>
					<input class="btn btn-success" title="WAF gives you reset ? try this." type="button" value="Encoded 提交" onclick="encoded_submit();">
					<input type="hidden" id="encoded_submit_mark" name="reverse2" value="reverse" />
					<?php
}
?>

<?php
if (isset($OJ_TEST_RUN) && $OJ_TEST_RUN) {
    ?>
						<input id="TestRun" class="btn btn-info" type=button value="测试运行" onclick=do_test_run();>
						<span class="btn btn-primary" id="result">状态</span>
<?php
}
?>
			</div>

<?php
if (isset($OJ_BLOCKLY) && $OJ_BLOCKLY) {
    ?>
			<input id="blockly_loader" type=button class="btn" onclick="openBlockly()" value="可视化" style="color:white;background-color:rgb(169,91,128)">
			<input id="transrun" type=button class="btn" onclick="loadFromBlockly() " value="翻译运行" style="display:none;color:white;background-color:rgb(90,164,139)">
			<div id="blockly" class="center">Blockly</div>
<?php
}
?>
		</form>
	<?php include "template/$OJ_TEMPLATE/js.php";?>
	<script>
		var sid = 0;
		var i = 0;
		var using_blockly = false;
		var judge_result = [<?php
foreach ($judge_result as $result) {
    echo "'$result',";
}
?> ''];
		function print_result(solution_id) {
			sid = solution_id;
			$("#out").load("status-ajax.php?tr=1&solution_id=" + solution_id);
		}
		function fresh_result(solution_id) {
			var tb = window.document.getElementById('result');
			if (solution_id == undefined) {
				tb.innerHTML = "验证码错误";
				if ($("#vcode") != null) $("#vcode").click();
				return;
			}
			sid = solution_id;
			$.ajax({
				type:"GET",
				url:"status-ajax.php?solution_id=" + solution_id,
				success:function(res){
					console.log(res);
					var r = res;
					var ra = r.split(",");
					var loader = "<img width=18 src=image/loader.gif>";
					var tag = "span";
					if (ra[0] < 4) tag = "span disabled=true";
					else tag = "a";
					{
						if (ra[0] == 11)
							tb.innerHTML = "<" + tag + " href='ceinfo.php?sid=" + solution_id + "' class='badge badge-info' target=_blank>" + judge_result[ra[0]] + "</" + tag + ">";
						else
							tb.innerHTML = "<" + tag + " href='reinfo.php?sid=" + solution_id + "' class='badge badge-info' target=_blank>" + judge_result[ra[0]] + "</" + tag + ">";
					}
					if (ra[0] < 4) tb.innerHTML += loader;
					tb.innerHTML += "Memory:" + ra[1] + "kb&nbsp;&nbsp;";
					tb.innerHTML += "Time:" + ra[2] + "ms";
					if (ra[0] < 4)
						window.setTimeout("fresh_result(" + solution_id + ")", 2000);
					else {
						window.setTimeout("print_result(" + solution_id + ")", 2000);
						// count = 1;
					}
				}
			})
		}
		function getSID() {
			var ofrm1 = document.getElementById("testRun").document;
			var ret = "0";
			if (ofrm1 == undefined) {
				ofrm1 = document.getElementById("testRun").contentWindow.document;
				var ff = ofrm1;
				ret = ff.innerHTML;
			}
			else {
				var ie = document.frames["frame1"].document;
				ret = ie.innerText;
			}
			return ret + "";
		}
		var count = 0;

		function encoded_submit() {

			var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
			var problem_id = document.getElementById(mark);

			if (typeof (editor) != "undefined")
				$("#hide_source").val(editor.getValue());
			if (mark == 'problem_id')
				problem_id.value = '<?php if (isset($id)) {
    echo $id;
}
?> ';
        else
		problem_id.value = '<?php if (isset($cid)) {
    echo $cid;
}
?> ';

		document.getElementById("frmSolution").target = "_self";
		document.getElementById("encoded_submit_mark").name = "encoded_submit";
		var source = $("#source").val();
		if (typeof (editor) != "undefined") {
			source = editor.getValue();
			$("#hide_source").val(encode64(utf16to8(source)));
		} else {
			$("#source").val(encode64(utf16to8(source)));
		}
		//      source.value=source.value.split("").reverse().join("");
		//      alert(source.value);
		document.getElementById("frmSolution").submit();
}

		function do_submit() {
			if (using_blockly)
				translate();
			if (typeof (editor) != "undefined") {
				$("#hide_source").val(editor.getValue());
			}
			var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
			var problem_id = document.getElementById(mark);
			if (mark == 'problem_id')
				problem_id.value = '<?php if (isset($id)) {
    echo $id;
}
?> ';
	else
		problem_id.value = '<?php if (isset($cid)) {
    echo $cid;
}
?> ';
		document.getElementById("frmSolution").target = "_self";
		document.getElementById("frmSolution").submit();
}

		var handler_interval;
		function do_test_run() {
			if (handler_interval) window.clearInterval(handler_interval);
			var loader = "<img width=18 src=image/loader.gif>";
			var tb = window.document.getElementById('result');
			var source = $("#source").val();
			if (typeof (editor) != "undefined") {
				source = editor.getValue();
				$("#hide_source").val(source);
			}
			if (source.length < 10) return alert("代码过短！");

			if (tb != null) tb.innerHTML = "";

			var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
			var problem_id = document.getElementById(mark);
			problem_id.value = -problem_id.value;
			document.getElementById("frmSolution").target = "testRun";
			$.ajax({
				type:'POST',
				data:$("#frmSolution").serialize(),
				url:"submit.php?ajax",
				success:function(data){
					fresh_result(data);
				},
				error:function(){
					fresh_result(undefined);
				}
			});
			$("#Submit").prop('disabled', true);
			$("#TestRun").prop('disabled', true);
			problem_id.value = -problem_id.value;
			count = 10;
			handler_interval = window.setTimeout("resume();", 1000);
		}
		function resume() {
			count--;
			var s = $("#Submit")[0];
			var t = $("#TestRun")[0];
			if (count < 0) {
				s.disabled = false;
				if (t != null) t.disabled = false;
				s.value = "提交";
				if (t != null) t.value = "测试运行";
				if (handler_interval) window.clearInterval(handler_interval);
				if ($("#vcode") != null) $("#vcode").click();
			} else {
				s.value = "提交(" + count + ")";
				if (t != null) t.value = "测试运行(" + count + ")";
				window.setTimeout("resume();", 1000);
			}
		}
		function switchLang(lang) {
			var langnames = new Array("c_cpp", "c_cpp", "pascal", "java", "ruby", "sh", "python", "php", "perl", "csharp", "objectivec", "vbscript", "scheme", "c_cpp", "c_cpp", "lua", "javascript", "golang");
			editor.getSession().setMode("ace/mode/" + langnames[lang]);

		}
		function reloadtemplate(lang) {
			console.log("lang=" + lang);
			document.cookie = "lastlang=" + lang.value;
			var url = window.location.href;
			var i = url.indexOf("sid=");
			if (i != -1) url = url.substring(0, i - 1);
			switchLang(lang);
		}
		function openBlockly() {
			$("#frame_source").hide();
			$("#TestRun").hide();
			$("#language")[0].scrollIntoView();
			$("#language").val(6).hide();
			$("#language_span").hide();
			$("#EditAreaArroundInfos_source").hide();
			$('#blockly').html('<iframe name=\'frmBlockly\' width=90% height=580 src=\'blockly/demos/code/index.html\'></iframe>');
			$("#blockly_loader").hide();
			$("#transrun").show();
			$("#Submit").prop('disabled', true);
			using_blockly = true;

		}
		function translate() {
			var blockly = $(window.frames['frmBlockly'].document);
			var tb = blockly.find('td[id=tab_python]');
			var python = blockly.find('pre[id=content_python]');
			tb.click();
			blockly.find('td[id=tab_blocks]').click();
			if (typeof (editor) != "undefined") editor.setValue(python.text());
			else $("#source").val(python.text());
			$("#language").val(6);

		}
		function loadFromBlockly() {
			translate();
			do_test_run();
			$("#frame_source").hide();
			//  $("#Submit").prop('disabled', false);
		}
	</script>
	<script language="Javascript" type="text/javascript" src="include/base64.js"></script>
	<?php if ($OJ_ACE_EDITOR) {?>
	<script src="ace/ace.js"></script>
	<script src="ace/ext-language_tools.js"></script>
	<script>
		ace.require("ace/ext/language_tools");
		var editor = ace.edit("source");
		editor.setTheme("ace/theme/chrome");
		switchLang(<?php echo $lastlang ?>);
		editor.setOptions({
			enableBasicAutocompletion: true,
			enableSnippets: true,
			enableLiveAutocompletion: true
		});
		reloadtemplate($("#language").val());

	</script>
	<?php }?>

</body>

</html>