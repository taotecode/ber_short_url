<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename Coded.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 短链算法文件
 */

/**
 * Class Coded : 算法类
 */
namespace ber\c;
class Coded
{

    /**
     * @title url短码算法-随机数生成 随机数组成一个指定数量的字符
     * @param int $length 生成字符串数量
     * @return string
     */
    public static function url_coded_random($length=6)
    {
        $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 3 => "123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~#$%^*|.");
        $string = $arr[3];
        //选择打乱的编码方式
        $count = strlen($string) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[rand(0, $count)];
        }
        return $code;
    }

    /**
     * @title url短码算法-6位字符
     * @param $url
     * @return mixed
     */
    public static function url_coded_short($url)
    {
        $key = 'ber';
        $urlhash = md5($key . $url);
        $len = strlen($urlhash);
        $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        //将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
        for ($i = 0; $i < 4; $i++) {
            $urlhash_piece = substr($urlhash, $i * $len / 4, $len / 4);
            //将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
            //此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
            $hex = hexdec($urlhash_piece) & 0x3fffffff;
            $short_url = '';
            //生成6位短网址
            for ($j = 0; $j < 6; $j++) {
                //将得到的值与0x0000003d,3d为61，即charset的坐标最大值
                $short_url .= $charset[$hex & 0x0000003d];
                //循环完以后将hex右移5位
                $hex = $hex >> 5;
            }
            $short_url_list[] = $short_url;
        }
        return $short_url_list[0];
    }
}