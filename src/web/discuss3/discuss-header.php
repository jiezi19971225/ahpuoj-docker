<?php
require_once '../include/db_info.inc.php';
require_once '../include/const.inc.php';
require_once '../include/my_func.inc.php';

$fp = fopen("../admin/config.txt", "r");
$bbs = 0;
while ($buff = fgets($fp)) {
    $item = explode(" ", $buff);
    if ($item[0] == 'bbs') {
        $bbs = intval($item[1]);
    }
}
if (!$bbs && !isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    $errors = "<h2>讨论版已经被管理员关闭！</h2>";
    require "./error.php";
    exit(1);
}
ob_start();
