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
    public $team_id;
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
            // 编译失败不增加惩罚时间
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
class Team
{
    public $solved = 0;
    public $time = 0;
    public $p_wa_num;
    public $p_ac_num;
    public $id;
    public $name;
    public function __construct()
    {
        $this->solved = 0;
        $this->time = 0;
        $this->p_wa_num = [];
        $this->p_ac_num = [];
    }
    public function Add(TM $user)
    {
        $this->solved += $user->solved;
        $this->time += $user->time;
        foreach ($user->p_ac_sec as $key => $value) {
            if (!isset($this->p_ac_num[$key])) {
                $this->p_ac_num[$key] = 1;
            } else {
                $this->p_ac_num[$key] += 1;
            }
        }
        foreach ($user->p_wa_num as $key => $value) {
            if (!isset($this->p_wa_num[$key])) {
                $this->p_wa_num[$key] = $value;
            } else {
                $this->p_wa_num[$key] += $value;
            }
        }
    }
}

function user_cmp(TM $A, TM $B)
{
    return $A->team_id > $B->team_id;
}

function team_cmp(Team $A, Team $B)
{
    if ($A->solved != $B->solved) {
        return $A->solved < $B->solved;
    } else {
        return $A->time > $B->time;
    }

}

// 获得竞赛数据
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

// 不是团队模式
if (!$team_mode) {
    header('Location:404.php');
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

$pid_cnt = intval($row['pbc']);

// 获得参赛团队
if ($OJ_MEMCACHE) {
    $sql = "SELECT `teams`.team_id,`teams`.name FROM `contest_team` INNER JOIN `teams` ON `contest_team`.team_id = `teams`.team_id WHERE contest_id='$contest_id' ORDER BY team_id ASC";
    $result = mysql_query_cache($sql);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }
} else {
    $sql = "SELECT `teams`.team_id,`teams`.name FROM `contest_team` INNER JOIN `teams` ON `contest_team`.team_id = `teams`.team_id WHERE contest_id=? ORDER BY team_id ASC";
    $result = pdo_query($sql, $contest_id);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }
}
$teams = $result;
$team_cnt = count($teams);

if ($OJ_MEMCACHE) {
    // solution_contest表中增加了团队的记录项
    $sql = "SELECT
        users.user_id,users.nick,solution.team_id,solution.result,solution.num,solution.in_date
                FROM
                        (SELECT * FROM solution_contest WHERE solution_contest.contest_id='$contest_id' AND num>=0 AND problem_id>0) solution
                INNER JOIN users
                ON users.user_id=solution.user_id AND users.defunct='N'
        ORDER BY users.user_id,in_date";
    $result = mysql_query_cache($sql);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

} else {
    $sql = "SELECT
        users.user_id,users.nick,solution.team_id,solution.result,solution.num,solution.in_date
                FROM
                        (SELECT * FROM solution_contest WHERE solution_contest.contest_id=? AND num>=0 AND problem_id>0) solution
                INNER JOIN users
                ON users.user_id=solution.user_id AND users.defunct='N'
        ORDER BY users.user_id,in_date";
    $result = pdo_query($sql, $contest_id);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }
}
$user_cnt = -1;
$user_name = '';
$U = array();
for ($i = 0; $i < $rows_cnt; $i++) {
    $row = $result[$i];
    $n_user = $row['user_id'];
    if (strcmp($user_name, $n_user)) {
        $user_cnt++;
        $U[$user_cnt] = new TM();
        $U[$user_cnt]->user_id = $row['user_id'];
        $U[$user_cnt]->nick = $row['nick'];
        $U[$user_cnt]->team_id = intval($row['team_id']);
        $user_name = $n_user;
    }
    if (time() < $end_time + 3600 && $lock < strtotime($row['in_date'])) {
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, 0);
    } else {
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, intval($row['result']));
    }
    if (intval($row['team_id'])) {
        $U[$user_cnt]->team_id = intval($row['team_id']);
    }

}
// 这样效率高一点。。。。 然并卵
usort($U, "user_cmp");
$T = array();
$i = 0;
foreach ($teams as $key => $row) {
    $T[$key] = new Team();
    $T[$key]->id = intval($row['team_id']);
    $T[$key]->name = $row['name'];
    while ($i <= $user_cnt) {
        if ($U[$i]->team_id == $T[$key]->id) {
            $T[$key]->Add($U[$i]);
        } else {
            break;
        }
        $i++;
    }
}
usort($T, "team_cmp");
require "template/" . $OJ_TEMPLATE . "/contestteamrank.php";
if (file_exists('./include/cache_end.php')) {
    require_once './include/cache_end.php';
}
