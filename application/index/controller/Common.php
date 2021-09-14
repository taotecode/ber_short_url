<?php
namespace app\index\controller;

use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Session;



class Common extends Controller
{
    protected $user_is_login;
    protected $user_data;
    protected $config;

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
        $this->config=Cache::get('web_config');
        if (empty($this->web_config)){
            $web_config_data=$this->get_web_config();
            Cache::set('web_config',$web_config_data,'2592000');
            $this->config=$web_config_data;//重新设置缓存有效期30天
        }
    }

    private function get_web_config(){
        $config_data=Db::name('config')->column('k');
        foreach ($config_data as $k){
            $data[$k]=Db::name('config')->where('k',$k)->value('v');
        }
        return $data;
    }
}