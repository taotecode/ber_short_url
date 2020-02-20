<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/16
 * @filename ajax.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example
 */
include "../../init.php";
use ber\c\Fan;
use ber\Url_list;
use ber\black;
use ber\user;
$user_id=$_SESSION['user.id'];
if (empty($user_id))
    Fan::error('登录信息已失效，请重新登录！',0000);
$mode=DATA['mode'];
switch ($mode){
    case 'url_edit':
        Url_list::url_edit(DATA);
        break;//url编辑

    case 'url_del':
        Url_list::url_del(DATA);
        break;//短链单个删除

    case 'url_del_all':
        Url_list::url_del_all(DATA);
        break;//短链选中删除

    case 'new_url':
        Url_list::new_url(DATA);
        break;

    case 'black_del':
        black::del(DATA);
        break;

    case 'black_del_all':
        black::del_all(DATA);
        break;

    case 'user_update':
        user::update(DATA);
        break;

    default:
        Fan::error('请求方法错误！',000);
        break;
}