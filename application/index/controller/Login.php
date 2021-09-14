<?php


namespace app\index\controller;


use Geet\Geet;
use think\Cookie;
use think\Debug;
use think\Request;
use think\Session;

class Login extends Common
{

    /**
     * 登录
     */
    public function login()
    {
        if (request()->isPost()) {
            $post=array(
                'geetest_challenge'=>input('post.geetest_challenge'),
                'geetest_validate'=>input('post.geetest_validate'),
                'geetest_seccode'=>input('post.geetest_seccode')
            );
            if(!geetest_check($post)){
                $this->error('安全环境不通过！');
            }
            if (db('user')->where(['user'=>input('post.user'),'pass'=>md5(input('post.pass'))])->find()){
                $user_data=db('user')->where(['user'=>input('post.user'),'pass'=>md5(input('post.pass'))])->find();
                if ($user_data['status']=='ban'){
                    $this->error('您的账户因违反平台规则，已被管理员永久封禁！');
                }elseif ($user_data['status']=='temporary-ban'){
                    $this->error('您的账户因违反平台规则，已被管理员临时封禁！到期时间['.$user_data['ban_time'].']');
                }else{
                    Cookie::clear('reg_');
                    Session::set('name',input('post.user'),'user');
                    $this->success('登录成功');
                }
            }else{
                $this->error('账号或密码错误！');
            }
        } else {
            $this->error('非法请求！！');
        }
    }

    /**
     * 退出登录
     */
    public function logout(){
        Session::delete('user');
        Session::clear('user');
        $this->success('您已成功退出登录！','/');
    }

    public function reg_mail_code(){
        if (request()->isPost()) {
            $post=array(
                'user'=>input('post.user'),
                'mail'=>input('post.mail')
            );
            if (empty($post['user']))
                return ['code'=>400,'msg'=>'请先填写账号'];
            if (empty($post['mail']))
                return ['code'=>400,'msg'=>'请填写收件邮箱！'];
            if (db('user')->where('user',$post['user'])->find())
                return ['code'=>400,'msg'=>'该账号已被注册'];
            if (db('user')->where('mail',$post['mail'])->find())
                return ['code'=>400,'msg'=>'该邮箱已被注册'];
            $code=rand(10000,100000);
            Cookie::set('code',md5($code),['prefix'=>'reg_','expire'=>600000]);
            $content=mail_mb_code('注册账号',$code,$post['user']);
            //$send=send_mail($post['mail'],$post['user'],'BER分短网址注册服务-验证码',$content);
            $send=true;
            if ($send)
                return ['code'=>200,'a'=>$code];
            else
                return ['code'=>400,'msg'=>$send];
        } else {
            $this->error('非法请求！！');
            return '';
        }
    }

    public function reg(){
        if (request()->isPost()) {
            $post=array(
                'user'=>input('post.user'),
                'pass'=>input('post.pass'),
                'mail'=>input('post.mail'),
                'code'=>input('post.code')
            );
            $geet_post=array(
                'geetest_challenge'=>input('post.geetest_challenge'),
                'geetest_validate'=>input('post.geetest_validate'),
                'geetest_seccode'=>input('post.geetest_seccode')
            );
            if(!geetest_check($geet_post)){
                $this->error('安全环境不通过！');
            }
            if (empty($post['user'])){
                Cookie::delete('code','reg_');
                $this->error('请输入账号');
            }
            if (empty($post['pass'])){
                Cookie::delete('code','reg_');
                $this->error('请输入密码');
            }
            if (empty($post['code'])){
                Cookie::delete('code','reg_');
                $this->error('请输入邮件验证码');
            }
            if (empty($post['mail'])){
                Cookie::delete('code','reg_');
                $this->error('请输入安全邮箱');
            }
            if (md5($post['code'])!=Cookie::get('code','reg_')){
                Cookie::delete('code','reg_');
                $this->error('验证码不正确');
            }
            if (strlen($post['user'])<6||strlen($post['user'])>10){
                Cookie::delete('code','reg_');
                $this->error('账号需要6-10位的数字');
            }
            if (!is_numeric($post['user'])){
                Cookie::delete('code','reg_');
                $this->error('请输入数字类型的账号');
            }
            if (db('user')->where('user',$post['user'])->find()){
                Cookie::delete('code','reg_');
                $this->error('该账号已被注册');
            }
            if (db('user')->where('mail',$post['mail'])->find()){
                Cookie::delete('code','reg_');
                $this->error('该邮箱已被注册');
            }
            $sql_data = ['user'=>$post['user'],'pass'=>md5($post['pass']),'mail'=>$post['mail'],'status'=>'user'];
            if (db('user')->insert($sql_data)){
                Cookie::delete('code','reg_');
                Session::set('name',input('post.user'),'user');
                $this->success('注册成功！已为您自动登录');
            }else{
                $this->error('注册失败，服务器处理错误！！');
            }
        } else {
            $this->error('非法请求！！');
        }
    }
}