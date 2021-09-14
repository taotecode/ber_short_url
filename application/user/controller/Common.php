<?php
namespace app\user\controller;

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

    protected function count_url_data($data,$table=null){
        $where["user"]=$this->user_data['user'];
        if (!empty($table))
            $where['biao']=$table;
        //今天
        $where['time']=date("Y-m-d");
        $count_data['Today']=DB::name('url_count')
            ->where($where)
            ->sum($data);
        //昨天
        $where['time']=date("Y-m-d", strtotime("-1 day"));
        $count_data['Yesterday']=DB::name('url_count')
            ->where($where)
            ->sum($data);
        //前天
        $where['time']=date("Y-m-d", strtotime("-2 day"));
        $count_data['before']=DB::name('url_count')
            ->where($where)
            ->sum($data);
        //3天前
        $where['time']=date("Y-m-d", strtotime("-3 day"));
        $count_data['day3']=DB::name('url_count')
            ->where($where)
            ->sum($data);
        //4天前
        $where['time']=date("Y-m-d", strtotime("-4 day"));
        $count_data['day4']=DB::name('url_count')
            ->where($where)
            ->sum($data);
        //5天前
        $where['time']=date("Y-m-d", strtotime("-5 day"));
        $count_data['day5']=DB::name('url_count')
            ->where($where)
            ->sum($data);
        //6天前
        $where['time']=date("Y-m-d", strtotime("-6 day"));
        $count_data['day6']=DB::name('url_count')
            ->where($where)
            ->sum($data);
        return $count_data;
    }

}