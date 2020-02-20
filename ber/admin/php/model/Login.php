<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename Login.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 后台登录操作文件
 */
namespace ber;
use M;
class Login
{
    public static function logout(){
        unset($_SESSION['user.id']);
        return true;
    }

    public static function login_go($user,$pass){
        $M=new M();
        if (!$M->IsExists('user', "`user`='$user' and pass='$pass' and status='root'"))
            return false;
        $_SESSION['user.id']=$user;
        return true;
    }
}