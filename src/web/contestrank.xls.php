<?php
ini_set("display_errors", "Off");
header("Content-type:application/excel;charset=UTF-8");
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
    public function Add($pid, $sec, $res, $mark_base, $mark_per_problem, $mark_per_punish)
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
    if ($A->solved != $B->solved) {
        return $A->solved < $B->solved;
    } else {
        return $A->time > $B->time;
    }

}
if (!isset($_GET['cid'])) {
    $errors = "<h2>竞赛&作业不存在</h2>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(1);
}
$cid = intval($_GET['cid']);
$sql = "SELECT `start_time`,`title`,`team_mode` FROM `contest` WHERE `contest_id`=?";
$result = pdo_query($sql, $cid);
$rows_cnt = count($result);
$start_time = 0;
if ($rows_cnt > 0) {
    $row = $result[0];
    $start_time = strtotime($row[0]);
    $title = $row[1];
    $team_mode = $row['team_mode'];
    $ftitle = rawurlencode($title . " 排名");
    $filename = "contest{$cid}_{$ftitle}.xls";
    header("content-disposition:   attachment;   filename=$filename");
}
if ($start_time == 0) {
    $errors = "<h2>竞赛&作业不存在</h2>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(1);
}

if ($start_time > time()) {
    $errors = "<h2>竞赛&作业尚未开始</h2>";
    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(1);
}
$sql = "SELECT count(1) FROM `contest_problem` WHERE `contest_id`=?";
$result = pdo_query($sql, $cid);
$row = $result[0];
$pid_cnt = intval($row[0]);
if ($pid_cnt == 1) {
    $mark_base = 100;
    $mark_per_problem = 0;
} else {
    $mark_per_problem = (100 - $mark_base) / ($pid_cnt - 1);
}
$mark_per_punish = $mark_per_problem / 5;
$sql = "SELECT
	users.user_id,users.nick,solution.team_id,solution.result,solution.num,solution.in_date,teams.name as team_name
		FROM
			(select * from solution_contest where solution_contest.contest_id=? and num>=0 and problem_id>0) solution
		LEFT JOIN users
		ON users.user_id=solution.user_id LEFT JOIN `teams` ON `solution`.team_id = `teams`.team_id
	ORDER BY users.user_id,in_date";
$result = pdo_query($sql, $cid);
$user_cnt = 0;
$user_name = '';
$U = array();
foreach ($result as $row) {
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
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, 0, $mark_base, $mark_per_problem, $mark_per_punish);
    } else {
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, intval($row['result']), $mark_base, $mark_per_problem, $mark_per_punish);
    }

}

usort($U, "s_cmp");
?>
<table>
    <tr>
        <td>Rank</td>
        <td>User</td>
        <td>Nick</td>
        <?php if ($team_mode) {
    echo "<td>Team</td>";
}?>
        <td>Solved</td>
        <td>Penalty</td>
        <?php
for ($i = 0; $i < $pid_cnt; $i++) {
    echo "<td>$PID[$i]</td>";
}
?>
    </tr>
<?php
$rank = 0;

for ($i = 0; $i < $user_cnt; $i++) {
    $rank++;
    $time = sec2str($U[$i]->time);
    echo "<tr>";
    echo "<td>$rank</td>";
    echo "<td>{$U[$i]->user_id}</td>";
    echo "<td>{$U[$i]->nick}</td>";
    if ($team_mode) {
        echo "<td>{$U[$i]->team_name}</td>";
    }
    echo "<td>{$U[$i]->solved}</td>";
    echo "<td>$time</td>";
    for ($j = 0; $j < $pid_cnt; $j++) {
        echo "<td>";
        if (isset($U[$i]->p_ac_sec[$j]) && $U[$i]->p_ac_sec[$j] > 0) {
            echo sec2str($U[$i]->p_ac_sec[$j]);
        }
        if (isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j] > 0) {
            echo "(-" . $U[$i]->p_wa_num[$j] . ")";
        }
        echo "</td>";
    }
    echo "</tr>";
}
?>
</table>