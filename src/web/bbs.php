<?php require_once "./include/db_info.inc.php";
$parm = "";

if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $parm = "pid=" . $pid;
} else {
    $pid = 0;
}
if (isset($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    $parm .= "&cid=" . $cid;
} else {
    $cid = 0;
}

echo "<script>location.href='discuss3/discuss.php?" . $parm . "';</script>";
