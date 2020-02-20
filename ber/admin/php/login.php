<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/31
 * @filename login.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 后台登录模板
 */
include "../../init.php";
if (isset($_SESSION['user.id'])){
    if ($M->IsExists('user', "`user`='".$_SESSION['user.id']."'"))
        \ber\c\Fan::jump_url('../','您已登录，无需重复登录！');
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BER分短网址后台登录</title>
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>
<body>
<div id="wrapper" class="login-page">
    <div id="login_form" class="form">
        <form class="login-form">
            <h2>管理登录</h2>
            <input type="text" placeholder="用户名" value="" id="user"/>
            <input type="password" placeholder="密码" id="pass"/>
            <button id="login" type="button">登　录</button>
        </form>
    </div>
</div>

<script src="../js/jquery.min.js"></script>
<script src="../js/php/login.js"></script>
<script type="text/javascript">
    $(function () {
        $("#login").click(function () {
            check_login();
            return false;
        })
    })
</script>
</body>
</html>
