<?php
$url = basename($_SERVER['REQUEST_URI']);
$dir = basename(getcwd());
if ($dir == "discuss3") {
    $path_fix = "../";
} else {
    $path_fix = "";
}

if (isset($OJ_NEED_LOGIN) && $OJ_NEED_LOGIN && (
    $url != 'loginpage.php' &&
    $url != 'lostpassword.php' &&
    $url != 'lostpassword2.php' &&
    $url != 'registerpage.php'
) && !isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {

    header("location:loginpage.php");
    exit();
}

if ($OJ_ONLINE) {
    require_once $path_fix . 'include/online.php';
    $on = new online();
}

$ACTIVE = "class='active'"
?>

<header class="primary-header">
  <div class="container-fluid clearfix">
    <div class="logo-wrapper">
      <p>
        <a href="<?php echo $path_fix ?>index.php">AHPUOJ</a>
      </p>
    </div>
    <nav class="primary-nav">
      <ul class="primary-nav-bar clear-fix">
        <li class="primary-nav-item">
          <a <?php echo ($url == "problemset.php" ? "class='active'" : "") ?> href="<?php echo $path_fix ?>problemset.php">
            <i class="glyphicon glyphicon-list-alt"></i>问题集</a>
        </li>
        <li class="primary-nav-item">
          <a <?php echo ($dir == "discuss3" ? "class='active'" : "") ?> href="<?php echo $path_fix ?>bbs.php">
            <i class="glyphicon glyphicon-comment"></i>讨论版</a>
        </li>
        <li class="primary-nav-item">
          <a <?php echo ($url == "status.php" ? "class='active'" : "") ?> href="<?php echo $path_fix ?>status.php">
            <i class="glyphicon glyphicon-th-large"></i>评测姬</a>
        </li>
        <li class="primary-nav-item">
          <a <?php echo ($url == "ranklist.php" ? "class='active'" : "") ?> href="<?php echo $path_fix ?>ranklist.php">
            <i class="glyphicon glyphicon-stats"></i>个人排名</a>
        </li>
        <li class="primary-nav-item">
          <a <?php echo ($url == "contest.php" ? "class='active'" : "") ?> href="<?php echo $path_fix ?>contest.php">
            <i class="glyphicon glyphicon-fire"></i>竞赛&作业</a>
        </li>
        <li class="primary-nav-item">
          <a <?php echo ($url == "series.php" ? "class='active'" : "") ?>  href="<?php echo $path_fix ?>series.php">
            <i class="glyphicon glyphicon-flag"></i>系列赛</a>
        </li>
        <li class="primary-nav-item">
          <a <?php echo ($url == "recent-contest.php" ? "class='active'" : "") ?> href="<?php echo $path_fix ?>recent-contest.php">
          <i class="glyphicon glyphicon-link"></i>近期比赛</a>
        </li>
<?php
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $profile = "";
    $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];
    $profile .= "<li><a href=" . $path_fix . "modifypage.php>注册信息</a></li><li><a href='" . $path_fix . "userinfo.php?user=$user_id'><span id=red>个人主页</span></a></li>";
    $profile .= "<li><a href='" . $path_fix . "status.php?user_id=$user_id'><span id=red>我的提交</span></a></li>";
    $profile .= "<li><a href='" . $path_fix . "contest.php?my'><span id=red>我的竞赛&作业</span></a></li>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'balloon'])) {
        $profile .= "<li><a href='" . $path_fix . "balloon.php'>气球</a></li>";
    }
    $profile .= "<li><a href=" . $path_fix . "logout.php>注销</a></li>";

    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor'])) {
        $profile .= "<li><a href=" . $path_fix . "admin/>管理</a></li>";
    }
    ?>
            <li class="primary-nav-item primary-nav-profile fr">
              <a id="user_id"><i class="glyphicon glyphicon-user"></i><?php echo $user_id ?></a>
              <ul class="profile-nav-list">
              <?php echo $profile ?>
              </ul>
            </li>
<?php
} else {
    ?>
            <li class="primary-nav-item fr">
              <a href="<?php echo $path_fix ?>loginpage.php">
                <i class="glyphicon glyphicon-log-in"></i>登录</a>
            </li>

<?php
}?>
      </ul>
    </nav>
    <nav class="mobile-nav">
      <div class="mobile-humber">
        <a href="#">
          <span class="line"></span>
          <span class="line"></span>
          <span class="line"></span>
        </a>
      </div>
    </nav>
    <ul class="mobile-nav-bar">
      <li class="mobile-nav-item">
        <a href="<?php echo $path_fix ?>problemset.php">
          <i class="glyphicon glyphicon-list-alt"></i>问题集</a>
      </li>
      <li class="mobile-nav-item">
        <a href="<?php echo $path_fix ?>bbs.php">
          <i class="glyphicon glyphicon-comment"></i>讨论版</a>
      </li>
      <li class="mobile-nav-item">
        <a href="<?php echo $path_fix ?>status.php">
          <i class="glyphicon glyphicon-th-large"></i>评测姬</a>
      </li>
      <li class="mobile-nav-item">
        <a href="<?php echo $path_fix ?>ranklist.php">
          <i class="glyphicon glyphicon-stats"></i>个人排名</a>
      </li>
      <li class="mobile-nav-item">
        <a href="<?php echo $path_fix ?>contest.php">
          <i class="glyphicon glyphicon-fire"></i>竞赛&作业</a>
      </li>
      <li class="mobile-nav-item">
        <a href="<?php echo $path_fix ?>series.php">
          <i class="glyphicon glyphicon-flag"></i>系列赛</a>
      </li>
      <li class="mobile-nav-item">
        <a href="<?php echo $path_fix ?>recent-contest.php">
        <i class="glyphicon glyphicon-link"></i>近期比赛</a>
      </li>
      <?php
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $profile = "";
    $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];
    $profile .= "<li class='mobile-nav-item'><a href=" . $path_fix . "modifypage.php>注册信息</a></li><li class='mobile-nav-item'><a href='" . $path_fix . "userinfo.php?user=$user_id'><span id=red>个人主页</span></a></li>";
    $profile .= "<li class='mobile-nav-item'><a href='" . $path_fix . "status.php?user_id=$user_id'><span id=red>我的提交</span></a></li>";
    $profile .= "<li class='mobile-nav-item'><a href='" . $path_fix . "contest.php?my'><span id=red>我的竞赛&作业</span></a></li>";
    if (isset($_SESSION[$OJ_NAME . '_' . 'balloon'])) {
        $profile .= "<li class='mobile-nav-item'><a href='" . $path_fix . "balloon.php'>气球</a></li>";
    }
    $profile .= "<li class='mobile-nav-item'><a href=" . $path_fix . "logout.php>注销</a></li>";

    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor'])) {
        $profile .= "<li class='mobile-nav-item'><a href=" . $path_fix . "admin/>管理</a></li>";
    }
    ?>
        <li class="mobile-nav-item" style="height:auto;">
          <a id="dropDownProfile"><span>
          <i class="glyphicon glyphicon-user"></i><?php echo $user_id ?></span>
          <i class="triangle"></i>
          </a>
          <ul class="profile-list">
            <?php
echo $profile;
    ?>
          </ul>
        </li>
      <?php
} else {
    ?>
        <li class="mobile-nav-item">
          <a href="<?php echo $path_fix ?>loginpage.php">
            <i class="glyphicon glyphicon-log-in"></i>登录</a>
        </li>
      <?php
}?>
            </ul>
  </div>
</header>