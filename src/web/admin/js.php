<?php
require_once "../include/db_info.inc.php"
?>
<script src="../template/<?php echo $OJ_TEMPLATE ?>/jquery.min.js"></script>
<script src="../template/<?php echo $OJ_TEMPLATE ?>/layui/layui.js"></script>
<script src="../template/<?php echo $OJ_TEMPLATE ?>/bootstrap.min.js"></script>
<script>
$("document").ready(function (){
	$("form").append("<div id='csrf' />");
	$("#csrf").load("../csrf.php");
});
</script>
