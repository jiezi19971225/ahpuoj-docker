<?php
////////////////////////////Common head
$cache_time = 1200;
$OJ_CACHE_SHARE = true;
require_once "./frontend-header.php";

$json = @file_get_contents('http://contests.acmicpc.info/contests.json');

$rows = json_decode($json, true);
require "template/" . $OJ_TEMPLATE . "/recent-contest.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
