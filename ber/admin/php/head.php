<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/14
 * @filename head.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 头部模板
 */
include "../../init.php";
use ber\c\Fan;
$user_id=$_SESSION['user.id'];
if (empty($user_id))
    Fan::jump_url('php/api.php?mode=logout','登录信息已过期，请重新登录。点击确定安全退出重新登录');
if (!$M->IsExists('user', "`user`='$user_id'"))
    Fan::jump_url('php/api.php?mode=logout','登录信息已过期，请重新登录。请重新登录');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $title;?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/xadmin.css">
    <script src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="../js/xadmin.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--百度统计-->
    <?php echo $header_count;?>
</head>
<body>
