<?php
////////////////////////////Common head
$cache_time = 10;
$OJ_CACHE_SHARE = false;
require_once "./frontend-header.php";

if (isset($OJ_REGISTER) && !$OJ_REGISTER) {
    exit(0);
}
/////////////////////////Template
require "template/" . $OJ_TEMPLATE . "/registerpage.php";
/////////////////////////Common foot
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
