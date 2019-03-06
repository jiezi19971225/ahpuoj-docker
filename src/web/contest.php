<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
if (isset($_POST['keyword'])) {
    $cache_time = 1;
} else {
    $cache_time = 30;
}
$OJ_CACHE_SHARE = false; //!(isset($_GET['cid'])||isset($_GET['my']));
require_once "./include/memcache.php";
require_once "./frontend-header.php";

if (isset($_GET['cid'])) {
    $contest_id = intval($_GET['cid']);

    $sql = "SELECT * FROM `contest` WHERE contest_id=? AND defunct = 'N'";
    $result = pdo_query($sql, $contest_id);

    if (count($result) == 0) {
        header("location:404.php");
        exit(0);
    } else {
        $show_problems = true;
        $row = $result[0];
        $private = $row['private'];
        $team_mode = $row['team_mode'];

        $now = time();
        $date_start_time = $row['start_time'];
        $date_end_time = $row['end_time'];
        $start_time = strtotime($row['start_time']);
        $end_time = strtotime($row['end_time']);
        $description = $row['description'];
        $title = $row['title'];

        $info = "";
        // 如果比赛尚未开始 非管理员不显示题目
        if ($now < $start_time) {
            $show_problems = false;
            $info .= "<p>竞赛&作业尚未开始</p>";
        }

        // 如果无参赛无权限 而且私有比赛
        if (!isset($_SESSION[$OJ_NAME . '_' . 'c' . $contest_id]) && $private) {
            $show_problems = false;
            $info .= "<p>你没有参加该竞赛&作业的权限</p>";
        }

        // 如果比赛已经结束 则公开题目
        if ($now > $end_time) {
            $show_problems = true;
        }
        // 管理员
        if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
            $show_problems = true;
        }
    }

    $sql = "SELECT * FROM (SELECT `problem`.`title` AS `title`,`problem`.`problem_id` AS `pid`,source AS source, contest_problem.num as pnum
    FROM `contest_problem`,`problem` WHERE `contest_problem`.`problem_id`=`problem`.`problem_id` AND `contest_problem`.`contest_id`=? ORDER BY `contest_problem`.`num`)
    problem LEFT JOIN (SELECT problem_id pid1,count(distinct(user_id)) accepted
    FROM solution WHERE result=4 AND contest_id=? GROUP BY pid1) p1 ON problem.pid=p1.pid1
    LEFT JOIN (SELECT problem_id pid2,count(1) submit FROM solution
    WHERE contest_id=? GROUP BY pid2) p2 ON problem.pid=p2.pid2 ORDER BY pnum";

    $result = pdo_query($sql, $contest_id, $contest_id, $contest_id);
    $problemset = [];

    foreach ($result as $key => $row) {
        $problemset[$key][0] = "";
        if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
            $problemset[$key][0] = check_ac($contest_id, $key);
        }

        $problemset[$key][1] = "<div>" . $row['pid'] . " 问题 &nbsp;" . $PID[$key] . "</div>";
        $problemset[$key][2] = "<div><a href='problem.php?cid=$contest_id&pid=$key'>" . $row['title'] . "</a></div>";
        $problemset[$key][3] = "<div>" . mb_substr($row['source'], 0, 8, 'utf8') . "</div >";
        $problemset[$key][4] = "<div><span class='table-tag bg-green'>" . ($row['accepted'] ? $row['accepted'] : 0) . "</span></div>";
        $problemset[$key][5] = "<div><span class='table-tag bg-azure'>" . ($row['submit'] ? $row['submit'] : 0) . "</span></div>";
    }
} else {
    $page = 1;
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
    }

    $page_cnt = 10;
    $pstart = $page_cnt * $page - $page_cnt;
    $pend = $page_cnt;
    $rows = pdo_query("select count(1) from contest where defunct='N'");

    if ($rows) {
        $total = $rows[0][0];
    }

    $total_page = intval($total / $page_cnt) + 1;
    $keyword = "";

    if (isset($_POST['keyword'])) {$keyword = "%" . $_POST['keyword'] . "%";}

    $mycontests = "";
    $len = mb_strlen($OJ_NAME . '_');

    foreach ($_SESSION as $key => $value) {
        if (($key[$len] == 'm' || $key[$len] == 'c') && intval(mb_substr($key, $len + 1)) > 0) {
            $mycontests .= "," . intval(mb_substr($key, $len + 1));
        }
    }

    if (strlen($mycontests) > 0) {
        $mycontests = substr($mycontests, 1);
    }

    $wheremy = "";
    if (isset($_GET['my'])) {
        $wheremy = " and contest_id in ($mycontests)";
    }

    $sql = "SELECT * FROM `contest` WHERE `defunct`='N' ORDER BY `contest_id` DESC LIMIT 1000";

    if ($keyword) {
        $sql = "SELECT *  FROM contest LEFT JOIN (SELECT * FROM privilege WHERE rightstr LIKE 'm%') p ON concat('m',contest_id)=rightstr WHERE contest.defunct='N' AND contest.title LIKE ? $wheremy  ORDER BY contest_id DESC";

        $sql .= " limit " . strval($pstart) . "," . strval($pend);

        $result = pdo_query($sql, $keyword);
    } else {
        $sql = "SELECT *  FROM contest LEFT JOIN (SELECT * FROM privilege WHERE rightstr LIKE 'm%') p ON concat('m',contest_id)=rightstr WHERE contest.defunct='N' $wheremy  ORDER BY contest_id DESC";
        $sql .= " limit " . strval($pstart) . "," . strval($pend);
        $result = mysql_query_cache($sql);
    }

    $contest = [];
    foreach ($result as $key => $row) {

        $start_time = strtotime($row['start_time']);
        $end_time = strtotime($row['end_time']);
        $now = time();
        $length = $end_time - $start_time;
        $left = $end_time - $now;

        $contest[$key][0] = $row['contest_id'];
        $contest[$key][1] = "<a href='contest.php?cid=" . $row['contest_id'] . "'>" . $row['title'] . "</a>";
        //past

        if ($now > $end_time) {
            $contest[$key][2] = "<span class='table-tag bg-red'>已结束@" . $row['end_time'] . "</span>";
            //pending

        } else if ($now < $start_time) {
            $contest[$key][2] = "<span class='table-tag bg-orange'>开始于@" . $row['start_time'] . "</span>&nbsp;";
            $contest[$key][2] .= "<span class='table-tag bg-purple'>总时间" . formatTimeLength($length) . "</span>";
            //running
        } else {
            $contest[$key][2] = "<span class='table-tag bg-azure'>运行中</span>&nbsp;";
            $contest[$key][2] .= "<span class='table-tag bg-blackish-green'>剩余时间" . formatTimeLength($left) . " </span>";
        }

        $private = intval($row['private']);
        if ($private == 0) {
            $contest[$key][3] = "<span class='table-tag bg-green'>公开</span>";
        } else {
            $contest[$key][3] = "<span class='table-tag bg-red'>私有</span>";
        }

        $team_mode = intval($row['team_mode']);
        if ($team_mode == 0) {
            $contest[$key][4] = "<span class='table-tag bg-azure'>个人赛</span>";
        } else {
            $contest[$key][4] = "<span class='table-tag bg-blackish-green'>团体赛</span>";
        }
    }
}

if (isset($_GET['cid'])) {
    require "template/" . $OJ_TEMPLATE . "/contest.php";
} else {
    require "template/" . $OJ_TEMPLATE . "/contestset.php";
}

if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
