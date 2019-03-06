<?php
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once "./frontend-header.php";
class TM
{
    public $solved = 0;
    public $time = 0;
    public $p_wa_num;
    public $p_ac_sec;
    public $user_id;
    public $nick;
    public $team_name;
    public function __construct()
    {
        $this->solved = 0;
        $this->time = 0;
        $this->p_wa_num = [];
        $this->p_ac_sec = [];
    }
    public function Add($pid, $sec, $res)
    {
        global $OJ_CE_PENALTY;
        if (isset($this->p_ac_sec[$pid]) && $this->p_ac_sec[$pid] > 0) {
            return;
        }

        if ($res != 4) {
            if (isset($OJ_CE_PENALTY) && !$OJ_CE_PENALTY && $res == 11) {
                return;
            }
            // ACM WF punish no ce

            if (isset($this->p_wa_num[$pid])) {
                $this->p_wa_num[$pid]++;
            } else {
                $this->p_wa_num[$pid] = 1;
            }
        } else {
            $this->p_ac_sec[$pid] = $sec;
            $this->solved++;
            if (!isset($this->p_wa_num[$pid])) {
                $this->p_wa_num[$pid] = 0;
            }

            $this->time += $sec + $this->p_wa_num[$pid] * 1200;
        }
    }
}

function s_cmp($A, $B)
{
//      echo "Cmp....<br>";
    if ($A->solved != $B->solved) {
        return $A->solved < $B->solved;
    } else {
        return $A->time > $B->time;
    }

}

// contest start time
if (!isset($_GET['cid'])) {
    header('Location:404.php');
    exit(0);
}

$contest_id = intval($_GET['cid']);

if ($OJ_MEMCACHE) {
    $sql = "SELECT `start_time`,`title`,`end_time`,`team_mode` FROM `contest` WHERE `contest_id`=$contest_id";
    require "./include/memcache.php";
    $result = mysql_query_cache($sql);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

} else {
    $sql = "SELECT `start_time`,`title`,`end_time`,`team_mode` FROM `contest` WHERE `contest_id`=?";
    $result = pdo_query($sql, $contest_id);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

}
$start_time = 0;
$end_time = 0;
if ($rows_cnt > 0) {
//       $row=$result[0];

    if ($OJ_MEMCACHE) {
        $row = $result[0];
    } else {
        $row = $result[0];
    }

    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);
    $title = $row['title'];
    $team_mode = $row['team_mode'];
}
if (!$OJ_MEMCACHE) {
    if ($start_time == 0) {
        header('Location:404.php');
        exit(0);
    }
}

if ($start_time > time()) {
    $errors = "<h2>竞赛&作业尚未开始!</h2>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(0);
}
if (!isset($OJ_RANK_LOCK_PERCENT)) {
    $OJ_RANK_LOCK_PERCENT = 0;
}

$lock = $end_time - ($end_time - $start_time) * $OJ_RANK_LOCK_PERCENT;

//echo $lock.'-'.date("Y-m-d H:i:s",$lock);

if ($OJ_MEMCACHE) {
    $sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`='$contest_id'";
//        require("./include/memcache.php");
    $result = mysql_query_cache($sql);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

} else {
    $sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`=?";
    $result = pdo_query($sql, $contest_id);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

}

if ($OJ_MEMCACHE) {
    $row = $result[0];
} else {
    $row = $result[0];
}

// $row=$result[0];
$pid_cnt = intval($row['pbc']);

if ($OJ_MEMCACHE) {
    // solution_contest表中增加了团队的记录项
    $sql = "SELECT
        users.user_id,users.nick,solution.team_id,solution.result,solution.num,solution.in_date,teams.team_name as team_name
                FROM
                        (select * from solution_contest where solution_contest.contest_id='$contest_id' and num>=0 and problem_id>0) solution
                INNER JOIN users
                ON users.user_id=solution.user_id and users.defunct='N' LEFT JOIN `teams` ON `solution`.team_id = `teams`.team_id
        ORDER BY users.user_id,in_date";
    $result = mysql_query_cache($sql);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }
    var_dump($result);
} else {
    $sql = "SELECT
        users.user_id,users.nick,solution.team_id,solution.result,solution.num,solution.in_date,teams.name as team_name
                FROM
                        (select * from solution_contest where solution_contest.contest_id=? and num>=0 and problem_id>0) solution
                INNER JOIN users
                ON users.user_id=solution.user_id and users.defunct='N' LEFT JOIN `teams` ON `solution`.team_id = `teams`.team_id
        ORDER BY users.user_id,in_date";
    $result = pdo_query($sql, $contest_id);
    var_dump($result);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

}

$user_cnt = 0;
$user_name = '';
$U = [];
//$U[$user_cnt]=new TM();
for ($i = 0; $i < $rows_cnt; $i++) {
    $row = $result[$i];
    $n_user = $row['user_id'];
    if (strcmp($user_name, $n_user)) {
        $user_cnt++;
        $U[$user_cnt] = new TM();
        $U[$user_cnt]->user_id = $row['user_id'];
        $U[$user_cnt]->nick = $row['nick'];
        $U[$user_cnt]->team_name = $row['team_name'];
        $user_name = $n_user;
    }
    if (time() < $end_time + 3600 && $lock < strtotime($row['in_date'])) {
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, 0);
    } else {
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, intval($row['result']));
    }
}

usort($U, "s_cmp");

////firstblood
$first_blood = [];
for ($i = 0; $i < $pid_cnt; $i++) {
    $first_blood[$i] = "";
}

if ($OJ_MEMCACHE) {
    $sql = "SELECT num,any_value(user_id) as user_id FROM
        (SELECT num,user_id FROM solution WHERE contest_id=$contest_id AND result=4 ORDER BY solution_id ) contest
        GROUP BY num";
    $fb = mysql_query_cache($sql);
    if ($fb) {
        $rows_cnt = count($fb);
    } else {
        $rows_cnt = 0;
    }
} else {
    $sql = "SELECT num,any_value(user_id) as user_id FROM
        (SELECT num,user_id FROM solution where contest_id=? and result=4 order by solution_id ) contest
        group by num";
    $fb = pdo_query($sql, $contest_id);
    if ($fb) {
        $rows_cnt = count($fb);
    } else {
        $rows_cnt = 0;
    }

}

for ($i = 0; $i < $rows_cnt; $i++) {
    $row = $fb[$i];
    $first_blood[$row['num']] = $row['user_id'];
}

require "template/" . $OJ_TEMPLATE . "/contestrank.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
