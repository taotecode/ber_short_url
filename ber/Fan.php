<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename Fan.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example Json接口返回数据文件
 */
namespace ber\c;
class Fan
{
    /**
     * @example 错误返回
     * @param $error : 错误内容
     * @param int $code : 状态码
     * @param string $msg : 说明
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
     * @example 正确返回并返回生成的短链
     * @param $url : 短链内容
     * @param int $code : 状态码
     * @param string $msg : 说明
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
     * @example 正确返回并返回生成的防红短链
     * @param $url : 防红短链内容
     * @param int $code : 状态码
     * @param string $msg : 说明
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

    /**
     * @example 返回ajax判断信息
     * @param int $code
     * @param $url
     * @param $msg
     */
    public static function ok($code=200,$msg='ok'){
        header('Content-type:text/json; charset=utf-8');
        echo json_encode(array(
            'code'  => $code,
            'msg' => $msg
        ),JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * @example 页面跳转
     * @param $url : 跳转到的页面
     * @param $msg : 跳转提示信息
     */
    public static function jump_url($url,$msg){
        header('Content-type:text/html; charset=utf-8');
        $html='
        <script language="javascript">
        alert("'.$msg.'");
        top.location="'.$url.'";
        </script>
        ';
        exit($html);
    }
}