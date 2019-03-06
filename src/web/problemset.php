<?php
$OJ_CACHE_SHARE = false;
$cache_time = 60;
require_once './frontend-header.php';
require_once './include/memcache.php';
$page_cnt = 50;
$filter_sql = "";
$keyword = $_GET['search'];
if ($keyword) {
    $search = "%" . $keyword . "%";
    $filter_sql = "AND title like ? or source like ? ";
}

$sql = "SELECT count(1) AS problem_cnt FROM `problem` WHERE 1 " . $filter_sql;
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    $sql .= " AND defunct = 'N'";
}

$result = mysql_query_cache($sql);
$row = $result[0];
$problem_cnt = $row['problem_cnt'];
$cnt = $problem_cnt / $page_cnt;

//remember page
$page = "1";
if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
    if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
        $sql = "update users set volume=? where user_id=?";
        pdo_query($sql, $page, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    }
} else {
    if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
        $sql = "select volume from users where user_id=?";
        $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
        $row = $result[0];
        $page = intval($row[0]);
    }
    if (!is_numeric($page) || $page < 0) {
        $page = '1';
    }
}
$sid = ($page - 1) * $page_cnt;
$sub_arr = array();

// submit
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $sql = "SELECT `problem_id` FROM `solution` WHERE `user_id`=?" .
        " group by `problem_id`";
    $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    foreach ($result as $row) {
        $sub_arr[$row[0]] = true;
    }

}

$acc_arr = array();
// ac
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $sql = "SELECT `problem_id` FROM `solution` WHERE `user_id`=?" .
        " AND `result`=4" .
        " group by `problem_id`";
    $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    foreach ($result as $row) {
        $acc_arr[$row[0]] = true;
    }

}

if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    $sql = "SELECT `problem_id`,`title`,`source`,`submit`,`accepted` FROM `problem` WHERE 1 $filter_sql";
} else {
    $now = strftime("%Y-%m-%d %H:%M", time());
    $sql = "SELECT `problem_id`,`title`,`source`,`submit`,`accepted` FROM `problem`
        WHERE `defunct`='N' $filter_sql AND `problem_id`NOT IN(
		SELECT  `problem_id`
		FROM contest c
		INNER JOIN  `contest_problem` cp ON c.contest_id = cp.contest_id
		AND (
			c.`end_time` >  '$now'
			 /* OR c.private =1 */
		)
			AND c.`defunct` =  'N'
	)  ";
}
$sql .= " ORDER BY `problem_id` LIMIT $sid,$page_cnt";

if ($keyword) {
    $result = pdo_query($sql, $search, $search);
} else {
    $result = mysql_query_cache($sql);
}

$total_page = intval($cnt + 1);

$cnt = 0;
$problemset = array();
foreach ($result as $key => $row) {
    $problemset[$key] = array();
    if (isset($sub_arr[$row['problem_id']])) {
        if (isset($acc_arr[$row['problem_id']])) {
            $problemset[$key][0] = "<div class='btn btn-success btn-xs' style='border-radius:0px;'><i class='glyphicon glyphicon-ok'></i></div>";
        } else {
            $problemset[$key][0] = "<div class='btn btn-danger btn-xs' style='border-radius:0px;'><i class='glyphicon glyphicon-remove'></i></div>";
        }

    } else {
        $problemset[$key][0] = "<div> </div>";
    }
    $problemset[$key][1] = "<div>" . $row['problem_id'] . "</div>";
    $problemset[$key][2] = "<div><a href='problem.php?id=" . $row['problem_id'] . "'>" . $row['title'] . "</a></div>";
    $problemset[$key][3] = "<div>" . mb_substr($row['source'], 0, 8, 'utf8') . "</div >";
    $problemset[$key][4] = "<div><a class='table-tag bg-green' href='status.php?problem_id=" . $row['problem_id'] . "&jresult=4'>" . $row['accepted'] . "</a></div>";
    $problemset[$key][5] = "<div><a class='table-tag bg-azure' href='status.php?problem_id=" . $row['problem_id'] . "'>" . $row['submit'] . "</a></div>";
}

require "template/" . $OJ_TEMPLATE . "/problemset.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
