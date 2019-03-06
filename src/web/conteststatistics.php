<?php
$OJ_CACHE_SHARE = true;
$cache_time = 3;
require_once "./frontend-header.php";

if (!isset($_GET['cid'])) {
    header('Location:404.php');
    exit(1);
}

// contest start time
$contest_id = intval($_GET['cid']);

$sql = "SELECT * FROM `contest` WHERE `contest_id`=? AND `start_time`<NOW()";
$result = pdo_query($sql, $contest_id);
if (count($result) == 0) {
    $errors = "<h2>竞赛作业尚未开始！</h2>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(0);
}

$row = $result[0];
$title = $row['title'];

$sql = "SELECT count(`num`) FROM `contest_problem` WHERE `contest_id`=?";
$result = pdo_query($sql, $contest_id);
$row = $result[0];
$pid_cnt = intval($row[0]);

$sql = "SELECT `result`,`num`,`language` FROM `solution` WHERE `contest_id`=? and num>=0";
$result = pdo_query($sql, $contest_id);
$R = array();
foreach ($result as $row) {
    $res = intval($row['result']) - 4;
    if ($res < 0) {
        $res = 8;
    }

    $num = intval($row['num']);
    $lag = intval($row['language']);
    if (!isset($R[$num][$res])) {
        $R[$num][$res] = 1;
    } else {
        $R[$num][$res]++;
    }

    if (!isset($R[$num][$lag + 11])) {
        $R[$num][$lag + 11] = 1;
    } else {
        $R[$num][$lag + 11]++;
    }

    if (!isset($R[$pid_cnt][$res])) {
        $R[$pid_cnt][$res] = 1;
    } else {
        $R[$pid_cnt][$res]++;
    }

    if (!isset($R[$pid_cnt][$lag + 11])) {
        $R[$pid_cnt][$lag + 11] = 1;
    } else {
        $R[$pid_cnt][$lag + 11]++;
    }

    if (!isset($R[$num][10])) {
        $R[$num][10] = 1;
    } else {
        $R[$num][10]++;
    }

    if (!isset($R[$pid_cnt][10])) {
        $R[$pid_cnt][10] = 1;
    } else {
        $R[$pid_cnt][10]++;
    }

}

$res = 3600;

$sql = "SELECT (UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(start_time))/100 FROM contest WHERE contest_id=? ";
$result = pdo_query($sql, $contest_id);
$view_userstat = array();
if ($row = $result[0]) {
    $res = $row[0];
}
$sql = "SELECT floor(UNIX_TIMESTAMP((in_date))/$res)*$res*1000 md,count(1) c FROM `solution` where  `contest_id`=?  group by md order by md desc ";
$result = pdo_query($sql, $contest_id);
$chart_data_all = array();
//echo $sql;
foreach ($result as $row) {
    $chart_data_all[$row['md']] = $row['c'];
}

$sql = "SELECT floor(UNIX_TIMESTAMP((in_date))/$res)*$res*1000 md,count(1) c FROM `solution` where  `contest_id`=? and result=4 group by md order by md desc ";
$result = pdo_query($sql, $contest_id); //mysql_escape_string($sql));
$chart_data_ac = array();
//echo $sql;

foreach ($result as $row) {
    $chart_data_ac[$row['md']] = $row['c'];
}

/////////////////////////Template
require "template/" . $OJ_TEMPLATE . "/conteststatistics.php";
/////////////////////////Common foot
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
