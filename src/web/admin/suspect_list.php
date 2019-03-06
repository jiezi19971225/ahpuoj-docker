<?php
require "admin-header.php";

if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) ||
    isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])
)) {
    require_once "./redirect_to_login.php";
    exit(1);
}
require_once "../include/set_get_key.php";
$contest_id = intval($_GET['cid']);
$sql = "select * from (select count(distinct user_id) c,ip from solution where contest_id=? group by ip) suspect
	inner join (select distinct ip,user_id from solution where contest_id=? ) u on suspect.ip=u.ip and suspect.c>1 order by c desc ,u.ip
       ";
$result1 = pdo_query($sql, $contest_id, $contest_id);
$start = pdo_query("select start_time from contest where contest_id=?", $contest_id)[0][0];
$end = pdo_query("select end_time from contest where contest_id=?", $contest_id)[0][0];
$sql = "select * from (select count(distinct ip) c,user_id from loginlog where time>=? and time<=? group by user_id) suspect
inner join (select distinct user_id from solution where contest_id=? ) u on suspect.user_id=u.user_id and suspect.c>1
inner join (select distinct ip,user_id from loginlog where time>=? and time<=? ) ips on ips.user_id=u.user_id
order by c desc ,u.user_id ";
$result2 = pdo_query($sql, $start, $end, $contest_id, $start, $end);
?>

<!DOCTYPE html>


<html>
<?php require_once 'admin-header.php'?>
<title>监视列表</title>

<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <?php require_once 'top_menubar.php';?>
        <?php require_once 'side_menubar.php';?>
        <div class="layui-body">
            <div class="panel panel-default">
                <h2 class="sub-page-title">监视列表</h2>
                <div class="panel-body">
                    <p>在这场竞赛&作业中从相同IP登录的账号</p>
                    <table class="table table-striped">
                        <thead>
                            <tr class="toprow">
                                <td>IP</td>
                                <td>用户</td>
                                <td>相同IP用户总计</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
foreach ($result1 as $row) {
    echo "<tr>";
    echo "<td>" . $row['ip'];
    echo "<td><a href='../status.php?cid=$contest_id&user_id=" . $row['user_id'] . "'>" . $row['user_id'] . "</a>";
    echo "<td>" . $row['c'];
    echo "</tr>";
}
?>
                        </tbody>
                    </table>
                    <p>在这场竞赛&作业中从不同IP登录的账号</p>
                    <table class="table table-striped">
                        <thead>
                            <tr class="toprow">
                                <td>IP</td>
                                <td>user</td>
                                <td>相同用户IP总计</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
foreach ($result2 as $row) {
    echo "<tr>";
    echo "<td>" . $row['ip'];
    echo "<td><a href='../status.php?cid=$contest_id&user_id=" . $row['user_id'] . "'>" . $row['user_id'] . "</a>";
    echo "<td>" . $row['c'];

    echo "</tr>";
}?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'js.php';?>
    <script>
        layui.use('element', function () {
            var element = layui.element;
        });
    </script>
</body>

</html>