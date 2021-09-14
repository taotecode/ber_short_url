<?php


namespace app\user\controller;


use think\Db;
use think\Debug;
use think\Request;
use think\Session;

class Page extends Common
{
    /**
     * @example 首页
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function welcome(){
        $this->assign('web_config',$this->config);
        $this->assign('user_data',$this->user_data);
        $this->assign('html_title','用户中心');
        $this->assign('html_js_file','');

        //db('url')->where('id',1)->value('name')
        $url_num=db('user_url')->where('user',Session::get('name','user'))->count();
        $url_visit=db('user_url')->where('user',Session::get('name','user'))->sum('pv_visit');

        //报表统计
        $this->assign('count_pv_data',$this->count_url_data('pv'));
        $this->assign('count_pc_data',$this->count_url_data('pc'));
        $this->assign('count_pe_data',$this->count_url_data('pe'));
        $this->assign('count_ip_data',$this->count_url_data('ip'));
        $this->assign('count_qq_data',$this->count_url_data('qq'));
        $this->assign('count_wx_data',$this->count_url_data('wx'));

        $this->assign('url_num',$url_num+db('user_work_url')->where('user',Session::get('name','user'))->count());
        $this->assign('url_visit',$url_visit+db('user_work_url')->where('user',Session::get('name','user'))->sum('pv_visit'));

        $this->assign('news_list',db('news')->where('type','0')->select());
        $this->assign('ad_list',db('news')->where('type','1')->select());

        return $this->fetch();
    }

    /**
     * @example 用户短链报表
     * @return mixed
     */
    public function url_report(){
        $this->assign('web_config',$this->config);
        $this->assign('user_data',$this->user_data);
        $this->assign('html_title','用户中心');

        //报表统计
        $this->assign('count_pv_data',$this->count_url_data('pv','user_url'));
        $this->assign('count_pc_data',$this->count_url_data('pc','user_url'));
        $this->assign('count_pe_data',$this->count_url_data('pe','user_url'));
        $this->assign('count_ip_data',$this->count_url_data('ip','user_url'));
        $this->assign('count_qq_data',$this->count_url_data('qq','user_url'));
        $this->assign('count_wx_data',$this->count_url_data('wx','user_url'));
        return $this->fetch();
    }

    /**
     * @example 用户业务短链报表
     * @return mixed
     */
    public function work_report(){
        $this->assign('web_config',$this->config);
        $this->assign('user_data',$this->user_data);
        $this->assign('html_title','用户中心');

        //报表统计
        $this->assign('count_pv_data',$this->count_url_data('pv','user_work_url'));
        $this->assign('count_pc_data',$this->count_url_data('pc','user_work_url'));
        $this->assign('count_pe_data',$this->count_url_data('pe','user_work_url'));
        $this->assign('count_ip_data',$this->count_url_data('ip','user_work_url'));
        $this->assign('count_qq_data',$this->count_url_data('qq','user_work_url'));
        $this->assign('count_wx_data',$this->count_url_data('wx','user_work_url'));
        return $this->fetch();
    }

    /**
     * @example 新建短链
     * @return mixed
     */
    public function new_url(){
        $this->assign('web_config',$this->config);
        $this->assign('user_data',$this->user_data);
        $this->assign('html_title','用户中心');
        $this->assign('html_js_file','<script src="/public/static/home/js/user/new_url.js" charset="utf-8"></script>');
        $this->assign('url_type',json_decode($this->config['url_type'],true));
        $this->assign('domain_type',json_decode($this->config['domain_type'],true));
        return $this->fetch();
    }

    public function url_list(){
        //print_r(w_url_url('http://www.berfen.com/','2144680883'));//https://w.url.cn/s/ADKkJLG    https://w.url.cn/s/AVyiDjX
        //Debug::dump(check_str(file_get_contents('https://w.url.cn/s/AVyiDjX'),'已停止访问该网页'));https://w.url.cn/s/ABODQwj
        $this->assign('web_config',$this->config);
        $this->assign('user_data',$this->user_data);
        $this->assign('html_title','用户中心');
        $this->assign('html_js_file','<script src="/public/static/home/js/user/url_list.js" charset="utf-8"></script>');
        return $this->fetch();
    }

    public function work_url_list(){
        $this->assign('web_config',$this->config);
        $this->assign('user_data',$this->user_data);
        $this->assign('html_title','用户中心');
        $this->assign('html_js_file','<script src="/public/static/home/js/user/work_url_list.js" charset="utf-8"></script>');
        return $this->fetch();
    }

    public function url_data(){
        if (!Request::instance()->isGet())
            $this->error('非法请求');
        $get_data=Request::instance()->param();
        switch ($get_data['type']){
            case 'update'://编辑链接
                $url_coded=$get_data['coded'];
                if ($get_data['table']=='user')
                    $db_table='user_url';
                elseif ($get_data['table']=='work')
                    $db_table='user_work_url';
                else
                    $db_table='user_url';

                $url_data = db($db_table)->where('coded',$url_coded)->find();
                if (!$url_data)
                    $this->error('数据不存在！');
                $url_data['appoint_domain']=json_decode($this->config['domain_type'],true)[$this->config['appoint_domain_type']];
                $url_data['url']=urldecode($url_data['url']);
                $this->assign('web_config',$this->config);
                $this->assign('url_type',json_decode($this->config['url_type'],true));
                $this->assign('user_data',$this->user_data);
                $this->assign('url_data',$url_data);
                $this->assign('url_data_config',json_decode($url_data['config'],true));
                $this->assign('html_title','用户中心');
                $this->assign('html_js_file','<script src="/public/static/home/js/user/user_url_data.js?id='.rand(99,9999).'" charset="utf-8"></script>');
                return $this->fetch('user_url_data');
                break;
        }
    }

}