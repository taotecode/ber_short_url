<?php


namespace app\url\controller;


class Cs
{
    public function c(){
        //初始
        $i=0;

        //增加
        for ($i = 0; $i < 9; $i++) {
            //二维
            $b[] = array(
                'id' => $i,
                'username' => 'user_01',
                'ip' => '127.0.0.1',
                'regtime' => '1970.1.1'

            );
        }
        echo json_encode(array('code' => '', 'msg' => '', 'count' => 1000, 'data' => $b));
    }
}