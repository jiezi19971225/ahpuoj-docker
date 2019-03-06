<?php
session_start();
require_once '../include/db_info.inc.php';
require_once '../include/my_func.inc.php';
require_once '../include/const.inc.php';

$tid = intval($_GET['tid']);

if ($_GET['target'] == 'reply') {
    $rid = intval($_GET['rid']);
    $stat = -1;
    if ($_GET['action'] == 'resume') {
        $stat = 0;
    }

    if ($_GET['action'] == 'disable') {
        $stat = 1;
    }

    if ($_GET['action'] == 'delete') {
        $stat = 2;
    }

    if ($stat == -1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "操作失败，参数错误";
        header("Location: thread.php?tid=$tid");
        exit(1);
    }

    $rid = intval($rid);
    if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        if ($stat != 2) {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "操作失败，参数错误";
            header("Location: thread.php?tid=$tid");
            exit(1);
        } else {
            $sql = "update reply SET status =? WHERE `rid` = ? AND author_id=?";
            if (!pdo_query($sql, $stat, $rid, $_SESSION[$OJ_NAME . '_' . 'user_id'])) {
                $_SESSION['operator_status'] = $OPERATOR_FAILURE;
                $_SESSION['error_message'] = "操作失败，参数错误";
                header("Location: thread.php?tid=$tid");
                exit(1);
            }
        }
    } else {
        $sql = "update reply SET status =? WHERE `rid` = ?";
        if (!pdo_query($sql, $stat, $rid)) {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "操作失败，参数错误";
            header("Location: thread.php?tid=$tid");
            exit(1);
        }
    }
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    header("Location: thread.php?tid=$tid");
    exit();
}

if ($_GET['target'] == 'thread') {
    echo 100;
    $tid = intval($_GET['tid']);
    $toplevel = -1;
    $stat = -1;
    if ($_GET['action'] == 'sticky') {
        if (isset($_GET['level']) && is_numeric($_GET['level']) && $_GET['level'] >= 0 && $_GET['level'] < 4) {
            $toplevel = intval($_GET['level']);
        } else {
            $_SESSION['operator_status'] = $OPERATOR_FAILURE;
            $_SESSION['error_message'] = "操作失败，参数错误";
            header("Location: discuss.php");
            exit(0);
        }
    }

    if ($_GET['action'] == 'resume') {
        $stat = 0;
    }

    if ($_GET['action'] == 'lock') {
        $stat = 1;
    }

    if ($_GET['action'] == 'delete') {
        $stat = 2;
    }

    if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "操作失败，没有权限";
        header("Location: discuss.php");
        exit(1);
    }

    if ($toplevel == -1 && $stat == -1) {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "操作失败，参数错误";
        header("Location: discuss.php");
        exit(1);
    }

    $tid = intval($tid);
    if ($stat == -1) {
        $sql = "UPDATE topic SET top_level = $toplevel WHERE `tid` = '$tid'";
    } else {
        $sql = "UPDATE topic SET status = $stat WHERE `tid` = '$tid'";
    }

    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;
    if (pdo_query($sql) > 0) {
        if ($stat != 2) {
            header("Location: thread.php?tid=$tid ");
            exit(0);
        } else {
            header("Location: discuss.php");
            exit(0);
        }

    } else {
        $_SESSION['operator_status'] = $OPERATOR_FAILURE;
        $_SESSION['error_message'] = "操作失败，话题不存在";
        header("Location: discuss.php");
        exit(1);
    }
}
header("Location: discuss.php");
exit(1);
