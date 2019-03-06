<?php
ini_set("display_errors", "Off");
header("Content-type:application/excel;charset=UTF-8");
require_once "./frontend-header.php";
class User
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
        $this->solved = [];
        $this->time = [];
        $this->p_wa_num = [];
        $this->p_ac_sec = [];
        $this->team_id = [];
    }
    // 将提交记录记录到正确的contest下
    public function Add($contest_id, $pid, $sec, $res)
    {
        global $OJ_CE_PENALTY;
        if (isset($this->p_ac_sec[$contest_id][$pid]) && $this->p_ac_sec[$contest_id][$pid] > 0) {
            return;
        }

        if ($res != 4) {
            // 编译失败不增加惩罚时间
            if (isset($OJ_CE_PENALTY) && !$OJ_CE_PENALTY && $res == 11) {
                return;
            }
            // ACM WF punish no ce

            if (isset($this->p_wa_num[$contest_id][$pid])) {
                $this->p_wa_num[$contest_id][$pid]++;
            } else {
                $this->p_wa_num[$contest_id][$pid] = 1;
            }
        } else {
            $this->p_ac_sec[$contest_id][$pid] = $sec;
            if (!isset($this->solved[$contest_id])) {
                $this->solved[$contest_id] = 1;
            } else {
                $this->solved[$contest_id]++;
            }
            if (!isset($this->p_wa_num[$contest_id][$pid])) {
                $this->p_wa_num[$contest_id][$pid] = 0;
            }
            if (!isset($this->time[$contest_id])) {
                $this->time[$contest_id] = $sec + $this->p_wa_num[$contest_id][$pid] * 1200;
            } else {
                $this->time[$contest_id] += $sec + $this->p_wa_num[$contest_id][$pid] * 1200;
            }
        }
    }
}

class SortObject
{
    public $cnt;
    public $solved;
    public $time;
    public $rank;
    public function __construct()
    {
        $this->cnt = 0;
        $this->time = 0;
        $this->solved = 0;
        $this->rank = -1;
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
    public $rank;
    public function __construct()
    {
        $this->solved = [];
        $this->time = [];
        $this->p_wa_num = [];
        $this->p_ac_num = [];
        $this->rank = [];
    }
    public function Add($contest_id, User $user)
    {
        if (!isset($this->solved[$contest_id]) && isset($user->solved[$contest_id])) {
            $this->solved[$contest_id] = $user->solved[$contest_id];
        } else if (isset($user->solved[$contest_id])) {
            $this->solved[$contest_id] += $user->solved[$contest_id];
        }

        if (!isset($this->time[$contest_id]) && isset($user->time[$contest_id])) {
            $this->time[$contest_id] = $user->time[$contest_id];
        } else if (isset($user->time[$contest_id])) {
            $this->time[$contest_id] += $user->time[$contest_id];
        }
    }
}

function rank_cmp(SortObject $A, SortObject $B)
{
    // 没有提交记录不计入排名
    if ($A->solved == 0 && $A->time == 0) {
        return 1;
    }
    if ($B->solved == 0 && $B->time == 0) {
        return 0;
    }

    if ($A->solved != $B->solved) {
        return $A->solved < $B->solved;
    } else {
        return $A->time > $B->time;
    }
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
if (!isset($_GET['sid'])) {
    $errors = "<h2>系列赛不存在</h2>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(1);
}

$series_id = intval($_GET['sid']);

$sql = "SELECT * FROM `series` WHERE series_id=? AND defunct = 'N'";
$result = pdo_query($sql, $series_id);

if (count($result) > 0) {
    $row = $result[0];
    $fname = $row['name'];
    $filename = "series{$series_id}_teamrank_{$fname}.xls";
    header("content-disposition:   attachment;   filename=$filename");
}

// 获得系列赛模式
$team_mode = intval($row['team_mode']);

if (!$team_mode) {
    $errors = "<h2>系列赛不是团队模式</h2>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(1);
}

// 取得系列赛包含的竞赛作业数据
$sql = "SELECT `contest`.contest_id,`contest`.title FROM `contest_series` INNER JOIN `contest` ON `contest_series`.contest_id=`contest`.contest_id WHERE `contest_series`.series_id=? AND `contest`.team_mode=?";
$contest_result = pdo_query($sql, $series_id, $team_mode);
$contest_str_list = ""; // 竞赛作业列表字符串
$contest_list = []; // 存储竞赛作业编号的数组
foreach ($contest_result as $key => $row) {
    $contest_list[$key]['contest_id'] = intval($row['contest_id']);
    $contest_list[$key]['title'] = $row['title'];
    if ($contest_str_list) {
        $contest_str_list .= "," . $row['contest_id'];
    } else {
        $contest_str_list = $row['contest_id'];
    }
}

// 个人模式的处理 个人排名汇总的处理
$user_cnt = 0;
$user_name = '';

$U = [];
// 取得系列赛全部的提交记录
$sql = "SELECT users.user_id,users.nick,solution.team_id,solution.result,solution.num,solution.in_date,solution.contest_id,contest.start_time
                FROM (SELECT * FROM solution_contest WHERE contest_id IN
                ($contest_str_list)
                AND num>=0 AND problem_id>0) solution
                INNER JOIN users ON users.user_id=solution.user_id
                INNER JOIN contest ON solution.contest_id=contest.contest_id
                WHERE users.defunct='N'
                ORDER BY users.user_id,in_date";
$solution = pdo_query($sql);

$user_cnt = 0;
$user_name = '';

// 对每条提交记录进行处理
foreach ($solution as $row) {
    $n_user = $row['user_id'];
    $contest_id = $row['contest_id'];
    $start_time = strtotime($row['start_time']);

    if (strcmp($user_name, $n_user)) {
        $user_cnt++;
        $U[$user_cnt] = new User();
        $U[$user_cnt]->user_id = $row['user_id'];
        $U[$user_cnt]->nick = $row['nick'];
        $U[$user_cnt]->team_id[$contest_id] = intval($row['team_id']);
        $user_name = $n_user;
    }
    $U[$user_cnt]->Add($contest_id, $row['num'], strtotime($row['in_date']) - $start_time, 0);
    $U[$user_cnt]->Add($contest_id, $row['num'], strtotime($row['in_date']) - $start_time, intval($row['result']));
    if (intval($row['team_id'])) {
        $U[$user_cnt]->team_id[$contest_id] = intval($row['team_id']);
    }
}

// 对于每场比赛 分别计算用户排名
foreach ($contest_list as $row) {
    $contest_id = $row['contest_id'];
    $sort_array = [];
    foreach ($U as $key => $user) {
        $u_sort_obj = new SortObject();
        $u_sort_obj->solved = isset($user->solved[$contest_id]) ? $user->solved[$contest_id] : 0;
        $u_sort_obj->time = isset($user->time[$contest_id]) ? $user->time[$contest_id] : 0;
        $u_sort_obj->cnt = $key;
        $sort_array[] = $u_sort_obj;
    }
    usort($sort_array, "rank_cmp");
    foreach ($sort_array as $key => $sort_item) {
        $cnt = $sort_item->cnt;
        if ($sort_item->solved == 0 && $sort_item->time == 0) {
            $U[$cnt]->rank[$contest_id] = -1;
        } else {
            $U[$cnt]->rank[$contest_id] = $key + 1;
        }
    }
}

// 获得所有参赛团队
$sql = "SELECT `teams`.team_id ,any_value(name) as name
FROM `contest_team` INNER JOIN `teams` ON `contest_team`.team_id = `teams`.team_id
WHERE contest_id IN ($contest_str_list)
GROUP BY `teams`.team_id
ORDER BY `teams`.team_id ASC";
$team_list = pdo_query($sql);

$T = [];
foreach ($team_list as $team) {
    $team_obj = new Team();
    $team_obj->id = intval($team['team_id']);
    $team_obj->name = $team['name'];
    $T[] = $team_obj;
}

// 对于每场比赛 分别计算团队排名
foreach ($contest_list as $row) {
    $contest_id = $row['contest_id'];
    $sort_array = [];

    foreach ($T as $team) {
        foreach ($U as $user) {
            if (isset($user->team_id[$contest_id]) && $user->team_id[$contest_id] == $team->id) {
                $team->add($contest_id, $user);
            }
        }
    }

    foreach ($T as $key => $team) {
        $t_sort_obj = new SortObject();
        $t_sort_obj->solved = isset($team->solved[$contest_id]) ? $team->solved[$contest_id] : 0;
        $t_sort_obj->time = isset($team->time[$contest_id]) ? $team->time[$contest_id] : 0;
        $t_sort_obj->cnt = $key;
        $sort_array[] = $t_sort_obj;
    }

    usort($sort_array, "rank_cmp");
    foreach ($sort_array as $key => $sort_item) {
        $cnt = $sort_item->cnt;
        if ($sort_item->solved == 0 && $sort_item->time == 0) {
            $T[$cnt]->rank[$contest_id] = -1;
        } else {
            $T[$cnt]->rank[$contest_id] = $key + 1;
        }
    }
}

?>

<table>
    <tr>
        <td rowspan="2">Team</td>
        <?php
foreach ($contest_list as $contest) {
    echo "<td colspan='3'>{$contest['title']}</td>";
}
?>
    </tr>
    <tr>
<?php
foreach ($contest_list as $contest) {
    echo "<td>Rank</td>";
    echo "<td>Solved</td>";
    echo "<td>Penalty</td>";
}
?>
    </tr>
<?php
foreach ($T as $team) {
    echo "<tr>";
    echo "<td>{$team->name}</td>";
    foreach ($contest_list as $contest) {
        $contest_id = $contest['contest_id'];
        $time = sec2str($team->time[$contest_id]);
        if ($team->rank[$contest_id] != -1) {
            echo "<td>{$team->rank[$contest_id]}</td>";
            echo "<td>{$team->solved[$contest_id]}</td>";
            echo "<td>$time</td>";
        } else {
            echo "<td> -- </td>";
            echo "<td> -- </td>";
            echo "<td> -- </td>";
        }
    }
    echo "</tr>";
}
?>
</table>