<?php

/**
 * Class Fan 返回Json♂
 */
class Fan
{
    /**
     * 遇到错误时返回错误信息
     * @param $error
     * @param int $code
     * @param string $msg
     */
    public static function error($error,$code=500,$msg='no'){
        header('Content-type:text/json; charset=utf-8');
        echo json_encode(array(
            'code'  => $code,
            'msg' => $msg,
            'error'=>$error
        ),JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * 返回生成成功的短链
     * @param $url
     * @param int $code
     * @param string $msg
     */
    public static function url($url,$code=200,$msg='ok'){
        header('Content-type:text/json; charset=utf-8');
        echo json_encode(array(
            'code'  => $code,
            'msg' => $msg,
            "url"=>$url
        ),JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * 生成成功的防红短链
     * @param $url
     * @param int $code
     * @param string $msg
     */
    public static function url_f($url,$code=201,$msg='ok'){
        header('Content-type:text/json; charset=utf-8');
        echo json_encode(array(
            'code'  => $code,
            'msg' => $msg,
            "url"=>$url
        ),JSON_UNESCAPED_UNICODE);
        exit();
    }
}