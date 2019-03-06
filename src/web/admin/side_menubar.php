<?php
$url = basename($_SERVER['REQUEST_URI']);
?>
<div class="layui-side layui-bg-black">
  <div class="layui-side-scroll">
    <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
    <ul class="layui-nav layui-nav-tree"  lay-filter="test">
<?php
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    ?>
      <li class="layui-nav-item <?php echo in_array($url, ['setmsg.php', 'news_add.php', 'news_list.php']) || preg_match('/^news_edit\.php.*/', $url) ? 'layui-nav-itemed' : '' ?>">
        <a class="" href="javascript:;">新闻公告</a>
        <dl class="layui-nav-child">
          <dd class="<?php echo $url == 'setmsg.php' ? 'layui-this' : '' ?>"><a href="setmsg.php">设置公告</a></dd>
          <dd class="<?php echo $url == 'news_add.php' ? 'layui-this' : '' ?>"><a href="news_add.php">添加新闻</a></dd>
          <dd class="<?php echo $url == 'news_list.php' ? 'layui-this' : '' ?>"><a href="news_list.php">新闻列表</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item <?php echo in_array($url, ['user_list.php', 'changepass.php']) ? 'layui-nav-itemed' : '' ?>">
        <a href="javascript:;">用户管理</a>
        <dl class="layui-nav-child">
          <dd class="<?php echo $url == 'user_list.php' ? 'layui-this' : '' ?>"><a href="user_list.php">用户列表</a></dd>
          <dd class="<?php echo $url == 'changepass.php' ? 'layui-this' : '' ?>"><a href="changepass.php">重设密码</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item"><a href="team_list.php">团队列表</a></li>
      <li class="layui-nav-item <?php echo in_array($url, ['problem_add_page.php', 'problem_import.php', 'problem_copy.php', 'problem_changeid.php', 'rejudge.php', 'problem_list.php', 'problem_import_xml.php']) ? 'layui-nav-itemed' : '' ?>">
        <a href="javascript:;">问题管理</a>
        <dl class="layui-nav-child">
          <dd class="<?php echo $url == 'problem_add_page.php' ? 'layui-this' : '' ?>"><a href="problem_add_page.php">添加问题</a></dd>
          <dd class="<?php echo $url == 'problem_import.php' ? 'layui-this' : '' ?>"><a href="problem_import.php">导入问题</a></dd>
          <dd class="<?php echo $url == 'problem_copy.php' ? 'layui-this' : '' ?>"><a href="problem_copy.php">复制题目</a></dd>
          <dd class="<?php echo $url == 'problem_changeid.php' ? 'layui-this' : '' ?>"><a href="problem_changeid.php">重排题目</a></dd>
          <dd class="<?php echo $url == 'rejudge.php' ? 'layui-this' : '' ?>"><a href="rejudge.php">重判题目</a></dd>
          <dd><a href="https://github.com/zhblue/freeproblemset/" target="_blank">免费问题集</a></dd>
          <dd><a href="http://tk.hustoj.com" target="_blank">自助题库</a></dd>
          <dd class="<?php echo $url == 'problem_list.php' ? 'layui-this' : '' ?>"><a href="problem_list.php">问题列表</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item <?php echo in_array($url, ['privilege_add.php', 'privilege_list.php']) ? 'layui-nav-itemed' : '' ?>">
        <a href="javascript:;">权限管理</a>
        <dl class="layui-nav-child">
          <dd class="<?php echo $url == 'privilege_add.php' ? 'layui-this' : '' ?>"><a href="privilege_add.php">权限授予</a></dd>
          <dd class="<?php echo $url == 'privilege_list.php' ? 'layui-this' : '' ?>"><a href="privilege_list.php">权限列表</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item <?php echo in_array($url, ['contest_add.php', 'contest_list.php', 'series_list.php']) || preg_match('/^(series_edit\.php.*|contest_edit\.php.*|contest_add_user\.php.*)/', $url) ? 'layui-nav-itemed' : '' ?>">
        <a href="javascript:;">竞赛作业&管理</a>
        <dl class="layui-nav-child">
          <dd class="<?php echo $url == 'contest_add.php' ? 'layui-this' : '' ?>"><a href="contest_add.php">添加竞赛&作业</a></dd>
          <dd class="<?php echo $url == 'contest_list.php' ? 'layui-this' : '' ?>"><a href="contest_list.php">竞赛&作业列表</a></dd>
          <dd class="<?php echo $url == 'series_list.php' ? 'layui-this' : '' ?>"><a href="series_list.php">系列赛列表</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item <?php echo in_array($url, ['team_generate.php', 'user_generate.php']) ? 'layui-nav-itemed' : '' ?>">
        <a href="javascript:;">账号生成器</a>
        <dl class="layui-nav-child">
          <dd class="<?php echo $url == 'team_generate.php' ? 'layui-this' : '' ?>"><a href="team_generate.php">比赛队账号生成器</a></dd>
          <dd class="<?php echo $url == 'user_generate.php' ? 'layui-this' : '' ?>"><a href="user_generate.php">用户账号生成器</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item"><a href="user_set_ip.php">指定登录IP</a></li>
      <li class="layui-nav-item"><a href="source_give.php">转移源码</a></li>
      <li class="layui-nav-item"><a href="config.php">设置</a></li>
      <li class="layui-nav-item"><a href="https://github.com/zhblue/hustoj/" target="_blank">HUSTOJ</a></li>
<?php
if (isset($OJ_ONLINE) && $OJ_ONLINE) {
        ?>
      <li class="layui-nav-item"><a href="../online.php">在线列表</a>
      <?php
}
}
?>

<?php
if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && isset($_SESSION[$OJ_NAME . '_' . 'password_setter'])) {?>
      <li class="layui-nav-item"><a href="changepass.php">重设密码</a></li>
  <?php
}
if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && (isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']))) {?>
      <li class="layui-nav-item"><a href="problem_list.php">问题列表</a>
  <?php
}
if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && isset($_SESSION[$OJ_NAME . '_' . 'problem_editor'])) {?>
      <li class="layui-nav-item"><a href="problem_add_page.php">添加问题</a>
  <?php
}
if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])) {?>
  <li class="layui-nav-item"><a href="contest_list.php">竞赛&作业列表</a>
  <li class="layui-nav-item"><a href="contest_add.php">添加竞赛&作业</a>
  <?php
}
?>
    </ul>
  </div>
</div>
