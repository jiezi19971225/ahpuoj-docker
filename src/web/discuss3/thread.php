<?php
require_once "discuss-header.php";
$tid = intval($_GET['tid']);
$sql = "SELECT 1 FROM `topic` WHERE tid = ?";
$result = pdo_query($sql, $tid);
if (count($result) < 1) {
    header('Location:../404.php');
    exit(0);
}

// 获得话题信息
$sql = "SELECT `title`, `pid`, `status`, `top_level` FROM `topic` WHERE `tid` = ? AND `status` <= 1";
$result = pdo_query($sql, $tid);
$topic = $result[0];
$pid = $topic['pid'];
$isadmin = isset($_SESSION[$OJ_NAME . '_' . 'administrator']);

$sql = "SELECT count(1) AS ids FROM `reply` WHERE topic_id = ?";
$result = pdo_query($sql, $tid);
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

$sql = "SELECT `rid`, `author_id`, `time`, `content`, `status` FROM `reply` WHERE `topic_id` = ? AND `status` <=1 ORDER BY `rid` LIMIT $sid, $idsperpage";
$result = pdo_query($sql, $tid);

?>

<div class="path-wrapper">
    位置：<a href="discuss.php">广场</a>
<?php
if ($pid != null && $pid != 0) {
    $query = "?pid=$pid";
    echo " / <a href=\"discuss.php" . $query . "\">问题 " . $pid . "</a>";
    echo "<a class='btn btn-primary noradius btn-sm fr' href='../problem.php?id=$pid'>转到问题</a>";
}
?>
</div>
<h2><?php echo nl2br(htmlentities($topic['title'], ENT_QUOTES, "UTF-8"));
if ($topic['top_level'] == 3) {
    echo "<span style='font-size:14px;margin-left:10px;' class='table-tag bg-green'>置顶</span>";
} else if ($topic['top_level'] == 2) {
    echo "<span style='font-size:14px;margin-left:10px;' class='table-tag bg-blackish-green'>置中</span>";
} else if ($topic['top_level'] == 1) {
    echo "<span style='font-size:14px;margin-left:10px;' class='table-tag bg-red'>置底</span>";
}
?>
</h2>
<?php
if ($isadmin) {
    $adminurl = "threadadmin.php?target=thread&tid={$tid}&action=";
    ?>
    <div class="topic-admin-area">
        <div class="btn-group">
            <?php
if ($topic['top_level'] == 0) {
        echo "<a class='btn btn-success btn-sm noradius' href=\"{$adminurl}sticky&level=3\">置顶</a><a class='btn btn-info btn-sm noradius' href=\"{$adminurl}sticky&level=2\">置中</a><a class='btn btn-danger btn-sm noradius' href=\"{$adminurl}sticky&level=1\">置底</a>";
    } else {
        echo "<a class='btn btn-success btn-sm noradius' href=\"{$adminurl}sticky&level=0\">恢复</a>";
    }
    echo "<a class='btn btn-danger btn-sm noradius' href='javascript:void(0);' onclick=\"deleteTopic('{$adminurl}delete')\">删除</a>";
    ?>
        </div>
    </div>
<?php
}
?>

<div class="reply-wrapper">
<ul class="reply-list">
<?php
foreach ($result as $key => $row) {
    $isadmin = isset($_SESSION[$OJ_NAME . '_' . 'administrator']);
    $url = "threadadmin.php?target=reply&rid=" . $row['rid'] . "&tid={$tid}&action=";
    if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
        $isuser = strtolower($row['author_id']) == strtolower($_SESSION[$OJ_NAME . '_' . 'user_id']);
    } else {
        $isuser = false;
    }
    ?>
        <li class="reply-box">
            <div class="container">
                <div class="row">
                    <div class="left-side col-xs-12 col-sm-2 col-md-1 col-lg-1" style="overflow:hidden;">
                        <ul>
                            <li>发言人</li>
                            <li><a href="../userinfo.php?user=<?php echo $row['author_id'] ?>"><?php echo $row['author_id'] ?></a></li>
                        </ul>
                    </div>
                    <div class="main col-xs-12 col-sm-10 col-md-11 col-lg-11">
                        <div class="content" id="reply<?php echo $row['rid'] ?>">
                        <?php echo nl2br(htmlentities($row['content'], ENT_QUOTES, "UTF-8")) ?>
                        </div>
                        <div class="other">
                            <span class="floor-number">#<?php echo ($page - 1) * $idsperpage + $key + 1 ?></span>
        <?php
if ($isuser || $isadmin) {
        echo "<a class='btn btn-danger btn-sm noradius btn-del' href='javascript:void(0);' onclick=\"deleteReply('{$url}delete')\">删除</a>";
    }?>
                            <button class="btn btn-primary btn-sm noradius btn-reply" onclick="reply(<?php echo $row['rid'] ?>)">回复</button>
                        </div>
                    </div>
                </div>
            </div>
        </li>
<?php
}
?>
    </ul>
    <?php
$param = "&tid=$tid";
echo "<div style='display:inline;'>";
echo "<nav class='center'>";
echo "<ul class='pagination pagination-sm'>";
echo "<li class='page-item'><a href='thread.php?page=" . (strval(1)) . "$param'>&lt;&lt;</a></li>";
echo "<li class='page-item'><a href='thread.php?page=" . ($page == 1 ? strval(1) : strval($page - 1)) . "$param'>&lt;</a></li>";
for ($i = $spage; $i <= $epage; $i++) {
    echo "<li class='" . ($page == $i ? "active " : "") . "page-item'><a title='go to page' href='thread.php?page=" . $i . "$param'>" . $i . "</a></li>";
}
echo "<li class='page-item'><a href='thread.php?page=" . ($page == $pages ? strval($page) : strval($page + 1)) . "$param'>&gt;</a></li>";
echo "<li class='page-item'><a href='thread.php?page=" . (strval($pages)) . "$param'>&gt;&gt;</a></li>";
echo "</ul>";
echo "</nav>";
echo "</div>";
?>
    <div class="reply-inputer-wrapper">
        <h2 class="reply-inputer-title">发表回复</h2>

        <?php
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    echo "<a class='redirect-login-link' href=../loginpage.php>请登录后发表内容</a>";
} else {
    ?>
        <form class="layui-form" method="POST" action="post.php?action=reply">
            <input type="hidden" name="tid" value="<?php echo $tid ?>">
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">内容</label>
                <div class="layui-input-block">
                <textarea placeholder="请输入内容" id="replyContent" class="layui-textarea" name="content" lay-verify="required"></textarea>
            </div>
            <div class="layui-form-item" style="margin-top:20px;">
                <div class="layui-input-block">
                    <button class="btn btn-primary noradius" type="submit" lay-submit="">提交</button>
                </div>
            </div>
        </form>
<?php
}
?>
    </div>
</div>

<script>
function reply(rid){
   var origin=$("#reply"+rid).text();
   origin="回复 :"+origin+"\n----------------------\n";
   $("#replyContent").text(origin);
   $("#replyContent").focus();
}
</script>

<?php require_once "../template/$OJ_TEMPLATE/discuss.php"?>
