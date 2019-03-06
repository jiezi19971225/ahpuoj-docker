<?php
// 五个小时
$lifeTime = 5 * 3600;
session_set_cookie_params($lifeTime);
@session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION[$OJ_NAME . '_' . 'csrf_keys'])
        && is_array($_SESSION[$OJ_NAME . '_' . 'csrf_keys'])
        && isset($_POST['csrf'])
        && in_array($_POST['csrf'], $_SESSION[$OJ_NAME . '_' . 'csrf_keys'])
    ) {
//        echo "<!-csrf check passed->";
    } else {
        echo "<!-csrf check failed->";
        exit(1);
    }
}
