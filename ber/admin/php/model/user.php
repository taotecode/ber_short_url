<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/20
 * @filename user.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 用户信息操作文件
 */

namespace ber;
use M;
use ber\c\Fan;

class user
{
    public static function update($data){
        global $user_id;
        if (empty($data['user']))
            Fan::error('请输入账号');
        if (empty($data['j_pass']))
            Fan::error('请输入旧密码');
        if (empty($data['x_pass']))
            Fan::error('请输入新密码');
        $M=new M();
        if (!$M->IsExists('user', "`user`='$user_id' and pass='".md5($data['j_pass'])."'"))
            Fan::error('旧密码错误');
        if ($M->Update("user", array('user'=>$data['user'], 'pass'=>md5($data['x_pass'])), "`user`='$user_id'")){
            unset($_SESSION['user.id']);
            Fan::ok();
        }
        Fan::error('服务器执行失败');
    }
}