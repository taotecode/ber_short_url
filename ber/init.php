<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename init.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 主配置文件
 */
error_reporting(0);
/**定义路径**/
define('ROOT', dirname(__FILE__).'/');
define('DB', dirname(__FILE__).'/db/');
define('ADMIN_MODEL', dirname(__FILE__).'/admin/php/model/');
/**
 * 定义变量
 */
define('BER','QkVS5YiG');
define('DATA', $_REQUEST);//接口请求方式
//当前域名
$http_url='http://'.$_SERVER['SERVER_NAME'].'/';

//百度统计代码-header头部
$header_count='<script>var _hmt = _hmt || [];(function() {var hm = document.createElement("script");hm.src = "https://hm.baidu.com/hm.js?371ce948a102b716eb63910ec861334e";var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hm, s);})();</script>';
//cnzz网站统计代码-底部
$footer_count='<div style="display: none"><script type="text/javascript" src="https://s4.cnzz.com/z_stat.php?id=1278591828&web_id=1278591828"></script></div>';


/**连接数据库文件**/
include_once DB."init.php";
$M=new M();

/**全局时区设置**/
date_default_timezone_set("PRC");

session_start();

/**
 * 扩展包
 */
include_once(ROOT."function.php");
include_once(ROOT."Fan.php");
include_once(ROOT."Coded.php");
include_once(ROOT."num.php");
/**
 * admin文件的方式
 */
include_once ADMIN_MODEL.'Login.php';
include_once ADMIN_MODEL.'Url_list.php';
include_once ADMIN_MODEL.'black.php';
include_once ADMIN_MODEL.'user.php';
?>