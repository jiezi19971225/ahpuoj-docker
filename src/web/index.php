<?php

$cache_time = 30;
$OJ_CACHE_SHARE = true;
require_once "./frontend-header.php";
require_once "./include/memcache.php";
$result = false;
if (isset($OJ_ON_SITE_CONTEST_ID)) {
    header("location:contest.php?cid=" . $OJ_ON_SITE_CONTEST_ID);
    exit();
}
$sql = "select * "
    . "FROM `news` "
    . "WHERE `defunct`!='Y'"
    . "ORDER BY `importance` ASC,`time` DESC "
    . "LIMIT 50";
$news = mysql_query_cache($sql);
$sql = "SELECT UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM (select * from solution order by solution_id desc limit 8000) solution  where result<13 group by md order by md desc limit 200";
$result = mysql_query_cache($sql); //mysql_escape_string($sql));
$chart_data_all = array();
//echo $sql;
foreach ($result as $row) {
    array_push($chart_data_all, array($row['md'], $row['c']));
}
$sql = "SELECT UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM  (select * from solution order by solution_id desc limit 8000) solution where result=4 group by md order by md desc limit 200";
$result = mysql_query_cache($sql); //mysql_escape_string($sql));
$chart_data_ac = array();

foreach ($result as $row) {
    array_push($chart_data_ac, array($row['md'], $row['c']));
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    $sql = "select avg(sp) sp from (select  count(1) sp,judgetime from solution where result>3 and judgetime>convert(now()-100,DATETIME)  group by judgetime order by sp) tt;";
    $result = mysql_query_cache($sql);
    $speed = $result[0][0];
} else {
    $speed = $chart_data_all[0][1];
}
require "template/" . $OJ_TEMPLATE . "/index.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
