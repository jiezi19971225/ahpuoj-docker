<?php
$cache_time = 30;
$OJ_CACHE_SHARE = false;

require_once "./frontend-header.php";

$now = strftime("%Y-%m-%d %H:%M", time());

if (isset($_GET['cid'])) {
    $contest_id = "&cid=" . intval($_GET['cid']);
} else {
    $contest_id = "";
}

require_once "./include/db_info.inc.php";

$pr_flag = false;
$co_flag = false;

if (isset($_GET['id'])) {
    // practice
    $problem_id = intval($_GET['id']);
    //require("oj-header.php");
    if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && $problem_id != 1000 && !isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])) {
        $sql = "SELECT * FROM `problem` WHERE `problem_id`=? AND `defunct`='N' AND `problem_id` NOT IN(
            SELECT `problem_id` FROM `contest_problem` WHERE `contest_id` IN(
              SELECT `contest_id` FROM `contest` WHERE `end_time`>'$now' /* or `private`='1' 私有应该只是限制参赛人员，但是比赛之后题目公开*/))";
    } else {
        $sql = "SELECT * FROM `problem` WHERE `problem_id`=?";
    }

    $pr_flag = true;
    $result = pdo_query($sql, $problem_id);
    if (!count($result)) {
        header("location:404.php");
        exit(0);
    }
} else if (isset($_GET['cid']) && isset($_GET['pid'])) {

    $contest_id = intval($_GET['cid']);
    $problem_id = intval($_GET['pid']);

    $sql = "SELECT problem_id FROM `contest_problem` WHERE contest_id=? AND num=?";
    $result = pdo_query($sql, $contest_id, $problem_id);
    $row = $result[0];
    $real_pid = $row['problem_id'];

    // 不是管理员 只有比赛开始后才显示题目
    if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        $sql = "SELECT langmask,private,defunct,end_time FROM `contest` WHERE `defunct`='N' AND `contest_id`=? AND `start_time`<='$now'";
    } else {
        $sql = "SELECT langmask,private,defunct,end_time FROM `contest` WHERE `defunct`='N' AND `contest_id`=?";
    }

    $result = pdo_query($sql, $contest_id);
    $rows_cnt = count($result);
    $row = ($result[0]);
    $now = time();
    $end_time = strtotime($row['end_time']);
    $contest_ok = true;

    if ($row[1] && !isset($_SESSION[$OJ_NAME . '_' . 'c' . $contest_id])) {
        $contest_ok = false;
    }

    if ($row[2] == 'Y') {
        $contest_ok = false;
    }

    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        $contest_ok = true;
    }

    $ok_cnt = $rows_cnt == 1;
    $langmask = $row[0];

    if ($ok_cnt != 1) {
        // 比赛尚未开始 无法查看题目
        header('Location:404.php');
        exit(1);
    } else {
        // 比赛已经开始 可以查看题目
        $sql = "SELECT * FROM `problem` WHERE `defunct`='N' AND `problem_id`=(
            SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=? AND `num`=?)";
        $result = pdo_query($sql, $contest_id, $problem_id);
    }

    // 如果比赛已经结束 则跳转至公开题目
    if ($now > $end_time) {
        header("Location:problem.php?id=$real_pid");
        exit(0);
    }

    // public
    if (!$contest_ok) {
        $errors = "<h2>你没有查看该题目的权限！</h2>";
        require "template/" . $OJ_TEMPLATE . "/error.php";
        exit(0);
    }
    $co_flag = true;

} else {
    header("location:404.php");
    exit(0);
}

if (count($result) != 1) {
    $errors = "";
    if (isset($_GET['id'])) {
        $problem_id = intval($_GET['id']);
        $sql = "SELECT contest.`contest_id`, contest.`title`,contest_problem.num FROM `contest_problem`, `contest`
          WHERE contest.contest_id=contest_problem.contest_id and `problem_id`=? and defunct='N' ORDER BY `num`";
        //echo $sql;
        $result = pdo_query($sql, $problem_id);

        if ($i = count($result)) {
            $errors .= "<h3>这个题目在一下比赛中</h3>";
            foreach ($result as $row) {
                $errors .= "<a href=problem.php?cid=$row[0]&pid=$row[2]>Contest $row[0]:" . htmlentities($row[1], ENT_QUOTES, "utf-8") . "</a><br>";
            }
        } else {
            $errors .= "<h2>你没有查看该题目的权限！</h2>";
        }
    } else {
        $errors .= "<h2>你没有查看该题目的权限！</h2>";
    }
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(0);
} else {
    $row = $result[0];
    $title = $row['title'];
    $time_limit = $row['time_limit'];
    $memory_limit = $row['memory_limit'];
    $submit = $row['submit'];
    $accepted = $row['accepted'];
    $description = $row['description'];
    $input = $row['input'];
    $output = $row['output'];
    $sample_input = str_replace("<", "&lt;", $row['sample_input']);
    $sample_input = str_replace(">", "&gt;", $sample_input);
    $sample_output = str_replace("<", "&lt;", $row['sample_output']);
    $sample_output = str_replace(">", "&gt;", $sample_output);
    $hint = $row['hint'];

    // 如果是在竞赛&作业中
    if ($contest_id) {
        $sql = "SELECT count(1) as submit FROM solution_contest WHERE contest_id=? AND num=?";
        $result = pdo_query($sql, $contest_id, $problem_id);
        $temp_row = $result[0];
        $submit = $temp_row['submit'];
        $sql = "SELECT count(1) as accepted,user_id FROM solution_contest WHERE contest_id=? AND num=? AND result=4 GROUP BY user_id";
        $result = pdo_query($sql, $contest_id, $problem_id);
        $temp_row = $result[0];
        $accepted = $temp_row ? $temp_row['accepted'] : 0;
    }

}

require "template/" . $OJ_TEMPLATE . "/problem.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
