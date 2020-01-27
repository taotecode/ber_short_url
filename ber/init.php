<?php
//关闭PHP报错
error_reporting(0);
/**定义路径**/
define('ROOT', dirname(__FILE__).'/');
define('DB', dirname(__FILE__).'/db/');

/**
 * 定义变量
 */
define('DATA', $_POST);//接口请求方式
//当前域名
$http_url='http://'.$_SERVER['SERVER_NAME'].'/';

/**全局时区设置**/
date_default_timezone_set("PRC");

/**连接数据库文件**/
include_once DB."init.php";
$M=new M();


/**
 * 扩展包
 */
include_once(ROOT . "Function.php");
include_once(ROOT."Fan.php");
include_once(ROOT."Coded.php");
?>