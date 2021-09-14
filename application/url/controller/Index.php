<?php


namespace app\url\controller;


use think\Request;

class Index extends Common
{
    public function index(){
        $request = Request::instance();
        echo '请求参数：name';
        dump($request->only(['name']));
        echo '请求参数：排除name';
        dump($request->except(['name']));
    }
}