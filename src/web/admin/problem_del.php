<?php
require_once "admin-header.php";
ini_set("display_errors", "On");
require_once "../include/check_get_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
if (function_exists('system')) {
    $problem_id = intval($_GET['id']);

    $basedir = "$OJ_DATA/$problem_id";
    if (strlen($basedir) > 16) {
        system("rm -rf $basedir");
    }

    // 删除相关源码和评测记录 用户ac记录在judged守护进程中会通过更新用户数据的方式更新
    $sql = "DELETE `source_code` FROM `source_code` INNER JOIN `solution` ON `source_code`.solution_id = `solution`.solution_id
    WHERE `solution`.problem_id = ?";
    pdo_query($sql, $problem_id);

    $sql = "DELETE `source_code_user` FROM `source_code_user` INNER JOIN `solution` ON `source_code_user`.solution_id = `solution`.solution_id
    WHERE `solution`.problem_id = ?";
    pdo_query($sql, $problem_id);

    $sql = "DELETE FROM `solution` WHERE problem_id = ?";
    pdo_query($sql, $problem_id);

    // 删除题目
    $sql = "delete FROM `problem` WHERE `problem_id`=?";
    pdo_query($sql, $problem_id);
    $sql = "select max(problem_id) FROM `problem`";
    $result = pdo_query($sql);
    $row = $result[0];
    $max_id = $row[0];
    $max_id++;
    if ($max_id < 1000) {
        $max_id = 1000;
    }

    $sql = "ALTER TABLE problem AUTO_INCREMENT = $max_id";
    pdo_query($sql);
    pdo_query($sql);
    $_SESSION['operator_status'] = $OPERATOR_SUCCESS;

} else {
    $_SESSION['operator_status'] = $OPERATOR_FAILURE;
    $_SESSION['error_message'] = '你需要在php.ini中设置允许 system() 函数运行';
}

header('Location:problem_list.php');
exit(0);
