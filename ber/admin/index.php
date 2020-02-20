<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename index.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 后台首页入口文件
 */
include "../init.php";

use ber\c\Fan;

$user_id = $_SESSION['user.id'];
if (empty($user_id))
    Fan::jump_url('php/api.php?mode=logout', '登录信息已过期，请重新登录。点击确定安全退出重新登录');
if (!$M->IsExists('user', "`user`='$user_id'"))
    Fan::jump_url('php/api.php?mode=logout', '登录信息已过期，请重新登录。请重新登录');
?>
<!doctype html>
<html lang="en">
<head id="tem">
    <meta charset="UTF-8">
    <title>后台-BER分短链服务</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>

    <link rel="stylesheet" href="css/font.css">
    <link rel="stylesheet" href="css/xadmin.css">

    <script src="js/jquery.min.js"></script>
    <script src="lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/xadmin.js"></script>

    <!--百度统计-->
    <?php echo $header_count; ?>
</head>
<body>
<!-- 顶部开始 -->
<div class="container">
    <div class="logo"><a href="../../public/index.php"><?php echo ber() ?></a></div>
    <div class="left_open">
        <i title="展开左侧栏" class="iconfont">&#xe699;</i>
    </div>
    <ul class="layui-nav left fast-add" lay-filter="">
        <li class="layui-nav-item">
            <a href="javascript:;">快捷功能</a>
            <dl class="layui-nav-child"> <!-- 二级菜单 -->
                <dd><a onClick="x_admin_show('新建短链','php/new_url.php')"><i class="iconfont">&#xe6a2;</i>新建管理员短链</a></dd>
            </dl>
        </li>
    </ul>
    <ul class="layui-nav right" lay-filter="">
        <li class="layui-nav-item">
            <a href="javascript:;"><?php echo $user_id; ?></a>
            <dl class="layui-nav-child"> <!-- 二级菜单 -->
                <dd><a onClick="x_admin_show('个人信息','php/user.php')">个人信息</a></dd>
                <dd><a href="php/api.php?mode=logout">退出</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item to-index"><a href="<?php echo $http_url; ?>" target="_blank">前台首页</a></li>
    </ul>

</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<!-- 左侧菜单开始 -->
<div class="left-nav">
    <div id="side-nav">
        <ul id="nav">
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe6eb;</i>
                    <cite>主页</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li><a _href="php/index.php"><i class="iconfont">&#xe6a7;</i><cite>控制台</cite></a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe6e4;</i>
                    <cite>链接控制</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li><a _href="php/new_url.php"><i class="iconfont">&#xe6a7;</i><cite>新建短链</cite></a></li>
                    <li><a _href="php/url_list.php"><i class="iconfont">&#xe6a7;</i><cite>短链列表</cite></a></li>
                    <li><a _href="php/black.php"><i class="iconfont">&#xe6a7;</i><cite>url黑名单</cite></a></li>
                </ul>
            </li>
            <!--<li>
                <a href="javascript:;"><i class="iconfont">&#xe6f6;</i><cite>用户管理</cite><i class="iconfont nav_right">&#xe6a7;</i></a>
                <ul class="sub-menu">
                    <li><a _href="html/page.html"><i class="iconfont">&#xe6a7;</i><cite>黑名单</cite></a></li>
                </ul>
            </li>-->
            <li>
                <a _href="php/user.php">
                    <i class="iconfont">&#xe6ae;</i>
                    <cite>个人信息</cite>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- <div class="x-slide_left"></div> -->
<!-- 左侧菜单结束 -->
<!-- 右侧主体开始 -->
<div class="page-content">
    <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
        <ul class="layui-tab-title">
            <li class="home"><i class="layui-icon">&#xe68e;</i>我的桌面</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe src='php/index.php' frameborder="0" scrolling="yes" class="x-iframe"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="page-content-bg"></div>
<!-- 右侧主体结束 -->
<!-- 中部结束 -->
<!-- 底部开始 -->
<div class="footer">
    <div class="copyright">Copyright ©2019 ber_Short_Url All Rights Reserved</div>
</div>
<!-- 底部结束 -->
<!--cnzz统计-->
<?php echo $footer_count; ?>
</body>
</html>