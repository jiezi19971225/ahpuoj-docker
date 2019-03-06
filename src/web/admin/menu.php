<html>
<head>
<?php require_once "admin-header.php";
?>
<title><?php echo $MSG_ADMIN ?></title>
</head>

<body style="background: #252525;">
  <ul class="nav nav-pills nav-stacked admin-menu-bar">
    <li class="menu-item" role="presentation"><a href="../status.php" target="_top" title="<?php echo $MSG_HELP_SEEOJ ?>"><?php echo $MSG_SEEOJ ?></a>
    <?php if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {?>
    <li class="menu-item" role="presentation"><a href="setmsg.php" target="main" title="<?php echo $MSG_HELP_SETMESSAGE ?>"><?php echo $MSG_SETMESSAGE ?></a>
    <li class="menu-item" role="presentation"><a href="news_list.php" target="main" title="<?php echo $MSG_HELP_NEWS_LIST ?>"><?php echo $MSG_NEWS . $MSG_LIST ?></a>
    <li class="menu-item" role="presentation"><a href="news_add_page.php" target="main" title="<?php echo $MSG_HELP_ADD_NEWS ?>"><?php echo $MSG_ADD . $MSG_NEWS ?></a>
    <li class="menu-item" role="presentation"><a href="user_list.php" target="main" title="<?php echo $MSG_HELP_USER_LIST ?>"><?php echo $MSG_USER . $MSG_LIST ?></a>
    <li class="menu-item" role="presentation"><a href="team_add_page.php" target="main" title="<?php echo $MSG_ADD . $MSG_TEAM ?>"><?php echo $MSG_ADD . $MSG_TEAM ?></a>
    <li class="menu-item" role="presentation"><a href="team_list.php" target="main" title="<?php echo $MSG_HELP_TEAM_LIST ?>"><?php echo $MSG_TEAM . $MSG_LIST ?></a>
    <li class="menu-item" role="presentation"><a href="user_set_ip.php" target="main" title="<?php echo $MSG_SET_LOGIN_IP ?>"><?php echo $MSG_SET_LOGIN_IP ?></a>
    <?php
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'password_setter'])) {?>
    <li class="menu-item" role="presentation"><a href="changepass.php" target="main" title="<?php echo $MSG_HELP_SETPASSWORD ?>"><?php echo $MSG_SETPASSWORD ?></a>
    <?php
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {?>
    <li class="menu-item" role="presentation"><a href="source_give.php" target="main" title="<?php echo $MSG_HELP_GIVESOURCE ?>"><?php echo $MSG_GIVESOURCE ?></a>
    <li class="menu-item" role="presentation"><a href="privilege_list.php" target="main" title="<?php echo $MSG_HELP_PRIVILEGE_LIST ?>"><?php echo $MSG_PRIVILEGE . $MSG_LIST ?></a>
    <li class="menu-item" role="presentation"><a href="privilege_add.php" target="main" title="<?php echo $MSG_HELP_ADD_PRIVILEGE ?>"><?php echo $MSG_ADD . $MSG_PRIVILEGE ?></a>
    <?php
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor'])) {?>
    <li class="menu-item" role="presentation"><a href="problem_list.php" target="main" title="<?php echo $MSG_HELP_PROBLEM_LIST ?>">问题列表</a>
    <?php
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor'])) {?>
    <li class="menu-item" role="presentation"><a href="problem_add_page.php" target="main" title="<?php echo $MSG_HELP_ADD_PROBLEM ?>"><?php echo $MSG_ADD . $MSG_PROBLEM ?></a>
    <?php
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {?>
    <li class="menu-item" role="presentation"><a href="problem_import.php" target="main" title="<?php echo $MSG_HELP_IMPORT_PROBLEM ?>"><?php echo $MSG_IMPORT . $MSG_PROBLEM ?></a>
    <li class="menu-item" role="presentation"><a href="problem_export.php" target="main" title="<?php echo $MSG_HELP_EXPORT_PROBLEM ?>"><?php echo $MSG_EXPORT . $MSG_PROBLEM ?></a>
    <?php
}?>
    <li class="menu-item" role="presentation"><a href="https://github.com/zhblue/freeproblemset/" target="_blank">免费问题集</a>
    <?php
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])) {?>
    <li class="menu-item" role="presentation"><a href="contest_list.php" target="main"  title="<?php echo $MSG_HELP_CONTEST_LIST ?>"><?php echo $MSG_CONTEST . $MSG_LIST ?></a>
    <li class="menu-item" role="presentation"><a href="contest_add.php" target="main"  title="<?php echo $MSG_HELP_ADD_CONTEST ?>"><?php echo $MSG_ADD . $MSG_CONTEST ?></a>
    <li class="menu-item" role="presentation"><a href="series_add_page.php" target="main"  title="<?php echo $MSG_ADD . $MSG_SERIES ?>"><?php echo $MSG_ADD . $MSG_SERIES ?></a>
    <li class="menu-item" role="presentation"><a href="series_list.php" target="main"  title="<?php echo $MSG_SERIES . $MSG_LIST ?>"><?php echo $MSG_SERIES . $MSG_LIST ?></a>
    <?php
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {?>
    <li class="menu-item" role="presentation"><a href="team_generate.php" target="main" title="<?php echo $MSG_HELP_TEAMGENERATOR ?>"><?php echo $MSG_TEAMGENERATOR ?></a>
    <li class="menu-item" role="presentation"><a href="user_generate.php" target="main" title="<?php echo $MSG_USERGENERATOR ?>"><?php echo $MSG_USERGENERATOR ?></a>
    <?php
}
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {?>
    <li class="menu-item" role="presentation"><a href="rejudge.php" target="main" title="<?php echo $MSG_HELP_REJUDGE ?>"><?php echo $MSG_REJUDGE ?></a>
    <?php
}?>
    <li class="menu-item" role="presentation"><a href="https://github.com/zhblue/hustoj/" target="_blank">HUSTOJ</a>
    <?php
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {?>
    <!-- <li class="menu-item" role="presentation"><a href="update_db.php" target="main" title="<?php echo $MSG_HELP_UPDATE_DATABASE ?>"><?php echo $MSG_UPDATE_DATABASE ?></a> -->
    <?php
}
if (isset($OJ_ONLINE) && $OJ_ONLINE) {?>
    <li class="menu-item" role="presentation"><a href="../online.php" target="main"><?php echo $MSG_ONLINE ?></a>
    <?php
}?>
    <li class="menu-item" role="presentation"><a href="http://tk.hustoj.com" target="_blank">自助题库</a>
    <li class="menu-item" role="presentation"><a href="problem_copy.php" target="main" title="Create your own data"><?php echo $MSG_COPY_PROBLEM ?></a>
  <li class="menu-item" role="presentation"><a href="problem_changeid.php" target="main" title="Danger,Use it on your own risk"><?php echo $MSG_REORDER_PROBLEM ?></a>
  </ul>
<?php if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && !$OJ_SAE) {
    ?>

<?php
}
?>
<script>
  $('.menu-item').click(function(){
    $('.menu-item').removeClass('active');
    $(this).addClass('active');
  })
</script>
</body>
</html>
