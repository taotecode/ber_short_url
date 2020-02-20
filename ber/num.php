<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/20
 * @filename num.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 访问次数计数
 */

namespace ber\c;
use M;

class num
{
    public static function record($coded){
        if (empty($coded))
            return false;
        $M=new M();
        $num = $M->GetOne("url", "visit", "url_coded='$coded'");
        if ($M->Update("url", array('visit'=>$num+1), "url_coded='$coded'"))
            return true;
        return false;
    }
}