<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename Api.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 首页接口文件
 */
include "../ber/init.php";
use ber\c\Fan;
use ber\c\Coded;
$user=DATA['api-user'];
$url=DATA['url'];
$mode=DATA['mode'];
if (empty($mode))
    Fan::error("方法错误");
switch ($mode){
    case 'api'://接口
        if (empty($url))
            Fan::error("请输入url");
        $url_type=DATA['type'];
        if ($url_type=='')
            Fan::error('请输入短链属性');
        if (!check_url($url))
            Fan::error("请输入正确的URL，并包含http://或https://");
        if ($url_type=="1")
            $url_type_f=0;
        elseif ($url_type=="0")
            $url_type_f=1;
        else
            Fan::error('请选择正确的短链属性');
        if ($M->IsExists('url', "url='$url' and url_type='$url_type_f'")){//存在URL
            if (!$M->Update("url", array('url_type'=>$url_type), "url='$url'"))
                Fan::error('生成失败',400);
            else{
                $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type='$url_type'");
                Fan::url($http_url.$url_coded);
            }
            $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type='$url_type_f'");
            Fan::url($http_url.$url_coded);
        }
        if ($M->IsExists('url', "url='$url' and url_type='$url_type'")){
            $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type='$url_type'");
            Fan::url($http_url.$url_coded);
        }
        $url_coded=Coded::url_coded_short($url);
        if (!$M->Insert("url", array("user","url","url_coded","url_type"), array($user,$url,$url_coded,$url_type)))
            Fan::error('生成失败',400);
        Fan::url($http_url.$url_coded);
        break;

    case 'api-s':
        if (empty($url))
            Fan::error("请输入url");
        if (!check_url($url))
            Fan::error("请输入正确的URL，并包含http://或https://");
        if (isset($_COOKIE["url"]))
            Fan::error('每个用户每分钟只能生成一次，请过一会再来吧');
        if ($M->IsExists('url', "url='$url' and url_type=1")){
            if (!$M->Update("url", array('url_type'=>0), "url='$url'"))
                Fan::error('生成失败',400);
            else{
                $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type=0");
                Fan::url($http_url.$url_coded);
            }
            $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type=1");
            Fan::url($http_url.$url_coded);
        }
        if ($M->IsExists('url', "url='$url' and url_type=0")){
            $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type=0");
            Fan::url($http_url.$url_coded);
        }
        $url_coded=Coded::url_coded_short($url);
        if (!$M->Insert("url", array("user","url","url_coded","url_type"), array($user,$url,$url_coded,'0')))
            Fan::error('生成失败',400);
        Fan::url($http_url.$url_coded);
        break;

    case 'api-h':
        if (empty($url))
            Fan::error("请输入url");
        if (!check_url($url))
            Fan::error("请输入正确的URL，并包含http://或https://");
        $url_coded = parse_url($url)['path'];//分割URL参数
        $url_coded=str_replace("/","",$url_coded);
        if ($M->IsExists('url', "url_coded='$url_coded' and url_type=0")){
            $url = $M->GetOne("url", "url", "url_coded='$url_coded' and url_type=0");
            Fan::url($url);
        }elseif ($M->IsExists('url', "url_coded='$url_coded' and url_type=1")){
            $url = $M->GetOne("url", "url", "url_coded='$url_coded' and url_type=1");
            Fan::url_f($url);
        }else
            Fan::error("该短网址未生成");
        break;

    case 'api-f':
        if (empty($url))
            Fan::error("请输入url");
        if (!check_url($url))
            Fan::error("请输入正确的URL，并包含http://或https://");
        if (isset($_COOKIE["url"]))
            Fan::error('每个用户每分钟只能生成一次，请过一会再来吧');
        if ($M->IsExists('url', "url='$url' and url_type=0")){
            if (!$M->Update("url", array('url_type'=>1), "url='$url'"))
                Fan::error('生成失败',400);
            else{
                $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type=1");
                Fan::url($http_url.$url_coded);
            }
        }
        if ($M->IsExists('url', "url='$url' and url_type=1")){
            $url_coded = $M->GetOne("url", "url_coded", "url='$url' and url_type=1");
            Fan::url($http_url.$url_coded);
        }
        $url_coded=Coded::url_coded_short($url);
        if (!$M->Insert("url", array("user","url","url_coded","url_type"), array($user,$url,$url_coded,'1')))
            Fan::error('生成失败',400);
        Fan::url($http_url.$url_coded);
        break;

    default:
        Fan::error("方法错误");
        break;
}