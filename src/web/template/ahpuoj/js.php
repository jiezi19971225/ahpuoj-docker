<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="<?php echo $path_fix . "template/$OJ_TEMPLATE/" ?>jquery.min.js"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="<?php echo $path_fix . "template/$OJ_TEMPLATE/" ?>bootstrap.min.js"></script>

<?php
if (file_exists("./admin/msg.txt")) {
    $view_marquee_msg = file_get_contents($OJ_SAE ? "saestor://web/msg.txt" : "./admin/msg.txt");
}

if (file_exists("../admin/msg.txt")) {
    $view_marquee_msg = file_get_contents($OJ_SAE ? "saestor://web/msg.txt" : "../admin/msg.txt");
}

?>
<!--  to enable mathjax in hustoj:
svn export http://github.com/mathjax/MathJax/trunk /home/judge/src/web/mathjax
<script type="text/javascript"
  src="mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
<script type="text/javascript"
  src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
-->
<script>
$(document).ready(function(){
  $("form").append("<div id='csrf' />");
  $("#csrf").load("<?php echo $path_fix ?>csrf.php");
  // $("body").append("<div id=footer class=center >GPLv2 licensed by <a href='https://github.com/zhblue/hustoj' >HUSTOJ</a> "+(new Date()).getFullYear()+" </div>");
  // $("body").append("<div class=center > <img src='http://hustoj.com/wx.jpg' width='96px'><img src='http://hustoj.com/alipay.png' width='96px'><br> 欢迎关注微信公众号onlinejudge</div>");
});

$(".hint pre").each(function(){
	var plus="<span class='glyphicon glyphicon-plus'>Click</span>";
	var content=$(this);
	$(this).before(plus);
	$(this).prev().click(function(){
		content.toggle();
	});

});
$(".primary-nav-profile").hover(function(){
  $(".profile-nav-list").slideDown("fast");
  $("#user_id").addClass("active");
  $(".profile-nav-list").hover(function(){
    $(".profile-nav-list").slideDown("fast");
  },function(){
    $(".profile-nav-list").slideUp("fast");
    $("#user_id").removeClass("active");
  })
},function(){
  $(".profile-nav-list").slideUp("fast");
  $("#user_id").removeClass("active");
})
$(".mobile-humber").click(function(){
  $(".mobile-nav-bar").slideToggle();
  $(".mobile-humber a").toggleClass("active");
})
$("#dropDownProfile").click(function(){
  $(".profile-list").slideToggle();
  $(this).toggleClass("active");
})
</script>

