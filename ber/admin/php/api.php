<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename api.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 后台ajax接口文件
 */
include "../../init.php";
$mode=DATA['mode'];
use ber\c\Fan;
use ber\Login;
use ber\Url_list;
use ber\black;
switch ($mode){
    case "logout":
        if (Login::logout())
            Fan::jump_url('login.php','请重新登陆');
        break;

    case "login":
        if (!Login::login_go(DATA['user'],md5(DATA['pass'])))
            Fan::error('账号或密码错误',400);
        Fan::url('../');
        break;

    case 'url_list':
        exit(Url_list::json_list(DATA['page'],DATA['limit']));
        break;

    case 'black_list':
        exit(black::black_list(DATA['page'],DATA['limit']));
        break;
}