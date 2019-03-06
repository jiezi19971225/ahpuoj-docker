<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');

// 五个小时
$lifeTime = 5 * 3600;
session_set_cookie_params($lifeTime);
session_start();
require_once '../include/db_info.inc.php';
require_once '../include/my_func.inc.php';
require_once '../include/const.inc.php';

if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = "发表话题失败！";
    echo "<script>history.go(-1)</script>";
    exit(0);
}

$tid = null;

if ($_GET['action'] == 'new') {

    if (strlen($_POST['content']) > 5000) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "内容过长";
        echo "<script>history.go(-1)</script>";
        exit(0);
    }

    if (strlen($_POST['title']) > 60) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "标题过长";
        echo "<script>history.go(-1)</script>";
        exit(0);
    }

    if (isset($_POST['title']) && isset($_POST['content']) && $_POST['title'] != '' && $_POST['content'] != '') {

        $pid = (isset($_POST['pid']) && $_POST['pid'] != '') ? intval($_POST['pid']) : 0;

        if ($_POST['pid'] != "广场") {
            $sql = "SELECT 1 FROM problem WHERE problem_id=? and defunct = 'N'";
            $result = pdo_query($sql, $pid);

            if (count($result) < 1) {
                $_SESSION['operator_status'] = $OPERATOR_FAILURE;
                $_SESSION['error_message'] = "发表话题失败，该问题不存在！";
                echo "<script>history.go(-1)</script>";
                exit(0);
            }
        }

        $title = $_POST['title'];
        $sql = "INSERT INTO `topic` (`title`, `author_id`, `cid`, `pid`) values(?,?,?,?)";
        $tid = pdo_query($sql, $_POST['title'], $_SESSION[$OJ_NAME . '_' . 'user_id'], 0, $pid);
        if ($tid) {
            $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
        } else {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "发表话题失败！";
            echo "<script>history.go(-1)</script>";
            exit(0);
        }
    }
}

if ($_GET['action'] == 'reply' || !is_null($tid)) {
    if (is_null($tid)) {
        $tid = intval($_POST['tid']);
    }

    if (!is_null($tid) && isset($_POST['content']) && $_POST['content'] != '') {
        $result = pdo_query("select tid from topic where tid=?", $tid);
        if (count($result) > 0) {
            $ip = ($_SERVER['REMOTE_ADDR']);
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
                $tmp_ip = explode(',', $REMOTE_ADDR);
                $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
            }
            $sql = "insert INTO `reply` (`author_id`, `time`, `content`, `topic_id`,`ip`) values(?,NOW(),?,?,?)";
            if (pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], $_POST['content'], $tid, $ip)) {
                header('Location: thread.php?tid=' . $tid);
                exit(0);
            } else {
                $_SESSION['operator_status'] = $OPERATOR_FAILURE;
                $_SESSION['error_message'] = "发表回复失败！";
                echo "<script>history.go(-1)</script>";
                exit(0);
            }
        } else {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "发表回复失败，该话题不存在！";
            echo "<script>history.go(-1)</script>";
            exit(0);
        }
    } else {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "发表话题失败，内容为空！";
        echo "<script>history.go(-1)</script>";
        exit(0);
    }

}
$_SESSION['operator_status'] = $OPERATOR_SUCCESS;
echo "<script>history.go(-1)</script>";
exit(0);
