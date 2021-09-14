<?php


namespace app\user\controller;


use think\Cache;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Json extends Controller
{
    protected $user_is_login;
    protected $user_data;
    protected $config;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        if (Session::has('name', 'user')) {
            $this->user_data = db('user')->where('user', Session::get('name', 'user'))->find();
            if ($this->user_data) {
                $this->user_is_login = true;
            }
        } else {
            $this->user_is_login = false;
            return ['code' => 00000, 'msg' => '登录信息已失效！请重新登录重试'];
        }
        $this->config = Cache::get('web_config');
        if (empty($this->web_config)) {
            $web_config_data = $this->get_web_config();
            Cache::set('web_config', $web_config_data, '2592000');
            $this->config = $web_config_data;//重新设置缓存有效期30天
        }
    }

    private function get_web_config()
    {
        $config_data = Db::name('config')->column('k');
        foreach ($config_data as $k) {
            $data[$k] = Db::name('config')->where('k', $k)->value('v');
        }
        return $data;
    }

    public function url_list(){
        if (!Request::instance()->isPost())
            return ['code'=>400,'msg'=>'非法请求'];
        $page=input('post.page');
        $limit=input('post.limit');
        $re = Db::name('user_url')->where('user','=',$this->user_data['user'])->order('time desc')->limit($limit)->page($page)->select();
        $count=Db::name('user_url')->where('user','=',$this->user_data['user'])->order('time desc')->count();
        foreach ($re as $res) {
            /*if (empty($res['short'])){
                $coded=$res['coded'];
            }else{
                $coded=$res['short'];
            }*/
            $data[] = array('coded' => $res['coded'], 'type' => $res['type'],
                'url' =>urldecode($res['url']),'short'=>$res['short'], 'state' =>$res['state'],
                'pv_visit'=>$res['pv_visit'],'pc_visit'=>$res['pc_visit'],'pe_visit'=>$res['pe_visit'],
                'qq_visit'=>$res['qq_visit'],'wx_visit'=>$res['wx_visit'],'ip_visit'=>$res['ip_visit']
            );
        }
        if ($count<=0)
            $data=null;
        return ["code"=>0,"msg"=>"","count"=>$count,"data"=>$data];
    }

    public function work_url_list(){
        if (!Request::instance()->isPost())
            return ['code'=>400,'msg'=>'非法请求'];
        $page=input('post.page');
        $limit=input('post.limit');
        $re = Db::name('user_work_url')->where('user','=',$this->user_data['user'])->order('time desc')->limit($limit)->page($page)->select();
        $count=Db::name('user_work_url')->where('user','=',$this->user_data['user'])->order('time desc')->count();
        foreach ($re as $res) {
            $data[] = array('coded' => $res['coded'], 'type' => $res['type'],
                'url' =>urldecode($res['url']),'short'=>$res['short'], 'state' =>$res['state'],
                'pv_visit'=>$res['pv_visit'],'pc_visit'=>$res['pc_visit'],'pe_visit'=>$res['pe_visit'],
                'qq_visit'=>$res['qq_visit'],'wx_visit'=>$res['wx_visit'],'ip_visit'=>$res['ip_visit']
            );
        }
        if ($count<=0)
            $data=null;
        return ["code"=>0,"msg"=>"","count"=>$count,"data"=>$data];
    }
}