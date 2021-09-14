<?php

namespace app\index\controller;

use think\captcha;
use Geet\Geet;
use think\Request;

class Index extends Common
{
    public function index()
    {
        if ($this->user_is_login) {
            $user_is_login = 2;
        } else {
            $user_is_login= 1;
        }
        $this->assign('config', $this->config);
        $this->assign('user_is_login', $user_is_login);
        return $this->fetch();
    }



    public function link()
    {
        $link = input('?link');
        echo $link . '<br>';
        $queryData = request()->param();
        print_r($queryData);
        return url('jy');
    }
}
