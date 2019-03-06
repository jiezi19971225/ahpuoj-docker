<?php
require_once "discuss-header.php";
$pid = (isset($_GET['pid']) && $_GET['pid'] != '') ? intval($_GET['pid']) : 0;

if ($pid) {
    $sql = "SELECT 1 FROM `problem` WHERE problem_id=? AND defunct ='N'";
    $result = pdo_query($sql, $pid);
    if (count($result) < 1) {
        header("Location:../404.php");
        exit(1);
    }
}

$sql = "SELECT count(1) AS ids FROM `topic` WHERE status != 2 ";
if ($pid) {
    $sql .= " AND pid = $pid";
}
$result = pdo_query($sql);
$row = $result[0];

$ids = $row['ids'];
$idsperpage = 15;
$pages = max(intval(ceil($ids / $idsperpage)), 1);

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}

$pagesperframe = 5;
$frame = intval(ceil($page / $pagesperframe));

$spage = ($frame - 1) * $pagesperframe + 1;
$epage = min($spage + $pagesperframe - 1, $pages);
$sid = ($page - 1) * $idsperpage;

$isadmin = isset($_SESSION[$OJ_NAME . '_' . 'administrator']);

$sql = "SELECT `tid`, `title`, `top_level`, `t`.`status`, `pid`, MIN(`r`.`time`) `posttime`,
		MAX(`r`.`time`) `lastupdate`, `t`.`author_id`, COUNT(`rid`) `count`
		FROM `topic` t left join `reply` r on t.tid=r.topic_id
        WHERE `t`.`status`!=2  ";

if ($pid) {
    $sql .= " AND ( `pid` = $pid OR `top_level` >= 2 )";
    $level = "";
} else {
    // 这句SQL的意思是按照top_level排序的时候 忽略top_level = 1的行
    $level = " - ( `top_level` = 1 )";
}
$sql .= " GROUP BY t.tid ORDER BY `top_level`$level DESC, MAX(`r`.`time`) DESC LIMIT $sid, $idsperpage";
$result = pdo_query($sql);
?>
<div class="path-wrapper">
    位置：<a href="discuss.php">广场</a>
<?php
if ($pid) {
    echo " / <a href='discuss.php?pid=$pid'>问题$pid</a>";
    echo "<a class='btn btn-primary noradius btn-sm fr' href='../problem.php?id=$pid'>转到问题</a>";
}
?>
</div>
<div class="topic-wrapper">
    <ul class="topic-list">
<?php
foreach ($result as $row) {
    ?>
        <li class="topic-box">
            <div class="container">
                <div class="row">
                    <div class="left-side col-xs-12 col-sm-2 col-md-1 col-lg-1">
                        <ul>
                            <li>讨论发起人</li>
                            <li><a href="../userinfo.php?user=<?php echo $row['author_id'] ?>"><?php echo $row['author_id'] ?></a></li>
                        </ul>
                    </div>
                    <div class="main col-xs-12 col-sm-7 col-md-8 col-lg-8">
            <?php

    if ($row['top_level'] == 3) {
        echo "<span class='topic-status table-tag bg-green'>置顶</span>";
    } else if ($row['top_level'] == 2) {
        echo "<span class='topic-status table-tag bg-blackish-green'>置中</span>";
    } else if ($row['top_level'] == 1) {
        echo "<span class='topic-status table-tag bg-red'>置底</span>";
    }
    ?>
                        <div class="content">
                            <a href="thread.php?tid=<?php echo $row['tid'] ?>"><?php echo htmlentities($row['title'], ENT_QUOTES, "UTF-8") ?></a>
                        </div>
                    </div>
                    <div class="right-side col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <ul>
                            <li>
                            <?php
if ($row['pid']) {
        ?>
For: <a href="discuss.php?pid=<?php echo $row['pid'] ?>">问题<?php echo $row['pid'] ?></a></li>
<?php
} else {
        ?>
In: <a href="discuss.php">广场</a>
                                <?php
}

    ?>
                            </li>
                            <li>发布时间: <?php echo date("Y-m-d h:i", strtotime($row['posttime'])) ?></li>
                            <li>最后回复: <?php echo date("Y-m-d h:i", strtotime($row['lastupdate'])) ?></li>
                            <li>回复总数: <?php echo $row['count'] - 1 ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
<?php
}
?>
    </ul>
<?php
$param = $pid ? "&pid=$pid" : '';
echo "<div style='display:inline;'>";
echo "<nav class='center'>";
echo "<ul class='pagination pagination-sm'>";
echo "<li class='page-item'><a href='discuss.php?page=" . (strval(1)) . "$param'>&lt;&lt;</a></li>";
echo "<li class='page-item'><a href='discuss.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "$param'>&lt;</a></li>";
for ($i = $spage; $i <= $epage; $i++) {
    echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='discuss.php?page=" . $i . "$param'>" . $i . "</a></li>";
}
echo "<li class='page-item'><a href='discuss.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "$param'>&gt;</a></li>";
echo "<li class='page-item'><a href='discuss.php?page=" . (strval($pages)) . "$param'>&gt;&gt;</a></li>";
echo "</ul>";
echo "</nav>";
echo "</div>";
?>

    <div class="topic-inputer-wrapper">
        <h2 class="topic-inputer-title">发表讨论</h2>
<?php
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    echo "<a class='redirect-login-link' href=../loginpage.php>请登录后发表内容</a>";
} else {
    ?>
        <form class="layui-form" action="post.php?action=new" method="POST">
        <input type="hidden" name="pid" value="<?php echo $pid ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title" placeholder="请输入讨论标题" autocomplete="off" class="layui-input" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">板块</label>
                <div class="layui-input-block">
                    <input type="text" name="pid" placeholder="请选择板块名称或者输入题目ID" autocomplete="off" class="layui-input" value="<?php echo $pid ? $pid : '广场' ?>" lay-verify="required" lay-search>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">内容</label>
                <div class="layui-input-block">
                <textarea placeholder="请输入内容" name="content" class="layui-textarea" lay-verify="required"></textarea>
            </div>
            <div class="layui-form-item" style="margin-top:20px;">
                <div class="layui-input-block">
                    <button class="btn btn-primary noradius" type="submit" lay-submit="">提交</button>
                </div>
            </div>
        </form>
    </div>
<?php
}
?>
</div>
<?php require_once "../template/$OJ_TEMPLATE/discuss.php"?>

