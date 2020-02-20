<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename index.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 入口文件
 */
include('../ber/init.php');
use ber\c\num;
//输出统计代码，如果不需要可以注释或删掉
echo $footer_count;
$url_coded=$_GET['link'];
if (empty($url_coded)){
    include "Template.php";
    return false;
}
if (!$M->IsExists('url', "url_coded='$url_coded'")) {
    include "Template.php";
    return false;
}
$data = $M->GetRow('url', 'url,url_type', "url_coded='$url_coded'");
if ($data['url_type']==0){
    num::record($url_coded);
    header('HTTP/1.1 301 Moved Permanently');
    header('Location:'.$data['url']);
}elseif ($data['url_type']==1) {
    num::record($url_coded);
    //生成防红预览页面，并统计，如果不需要，可以删除$header和$footer_count
    exit(h_url($data['url'],$header_count,$footer_count));
}
?>