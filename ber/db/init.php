<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename Api.php
 * @link https://gitee.com/navyCaptain/php_mysqli_class
 * @example 数据库连接扩展配置文件
 */
define('DB_HOST', 'localhost'); 	// 数据库服务器地址
define('DB_USER', 'root');  		// 数据库用户名
define('DB_PWD', '');			// 数据库密码
define('DB_NAME', 'dwz');  		// 数据库名称
define('DB_PORT', '3306');  		// 数据库端口

require_once 'M.class.php';
require_once 'MysqliDb.class.php';
/**查询表
 * $user=$M->GetOne("SELECT `user` FROM `ber_user` WHERE id=1");
 * 'ber_user'是表名，'user'是查询的键名
 * */
