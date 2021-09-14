<?php


namespace app\user\controller;


class Index extends Common
{
    public function index(){
        if (!$this->user_is_login)
            $this->error('您的登录信息已过期，请重新登录！','/logout');
        $this->assign('web_config',$this->config);
        $this->assign('user_data',$this->user_data);
        $this->assign('html_title','用户中心');
        return view();
    }
}