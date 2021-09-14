<?php
namespace app\url\controller;

use think\Controller;
use think\Cookie;
use think\Session;



class Common extends Controller
{
    protected $user_is_login;
    protected $user_data;
    protected $web_config;

    public function _initialize()
    {
        if (Session::has('name','user')){
            $this->user_data=db('user')->where('user',Session::get('name','user'))->find();
            if ($this->user_data){
                $this->user_is_login=true;
            }
        }else{
            $this->user_is_login=false;
        }

        //$this->web_config=db('config')->where('user',config('web_admin.user'))->find();
    }
}