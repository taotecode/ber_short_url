<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename index.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 后台首页内容文件
 */
include "../../init.php";
$user_id=$_SESSION['user.id'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>欢迎</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />\
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/xadmin.css">
</head>
<body>
<div class="x-body layui-anim layui-anim-up">
    <blockquote class="layui-elem-quote">欢迎管理员：
        <span class="x-red"><?php echo $user_id;?></span>！当前时间:<?php echo date("Y年m月d日 H时i分");?></blockquote>
    <fieldset class="layui-elem-field">
        <legend>数据统计</legend>
        <div class="layui-field-box">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-carousel x-admin-carousel x-admin-backlog" lay-anim="" lay-indicator="inside" lay-arrow="none" style="width: 100%; height: 90px;">
                            <div carousel-item="">
                                <ul class="layui-row layui-col-space10 layui-this">
                                    <li class="layui-col-xs2">
                                        <a href="javascript:;" class="x-admin-backlog-body">
                                            <h3>短链接生成数量</h3>
                                            <p>
                                                <cite><?php echo $M->Total('url'); ?></cite></p>
                                        </a>
                                    </li>
                                    <li class="layui-col-xs2">
                                        <a href="javascript:;" class="x-admin-backlog-body">
                                            <h3>防红链接数</h3>
                                            <p>
                                                <cite><?php echo $M->Total('url','url_type=1'); ?></cite></p>
                                        </a>
                                    </li>
                                    <li class="layui-col-xs2">
                                        <a href="javascript:;" class="x-admin-backlog-body">
                                            <h3>用户数</h3>
                                            <p>
                                                <cite><?php echo $M->Total('user'); ?></cite></p>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>系统通知</legend>
        <div class="layui-field-box">
            <table class="layui-table" lay-skin="line">
                <tbody>
                <tr>
                    <td >
                        <a class="x-a" href="http://www.berfen.com" target="_blank">BER分官网</a>
                    </td>
                </tr>
                <tr>
                    <td >
                        <a class="x-a" href="https://jq.qq.com/?_wv=1027&k=5xn5iV5" target="_blank">交流qq:(831323734)</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>系统信息</legend>
        <div class="layui-field-box">
            <table class="layui-table">
                <tbody>
                <tr>
                    <th>BER分短链版本</th>
                    <td>1.0.1</td></tr>
                <tr>
                    <th>服务器地址</th>
                    <td><?php echo get_server_ip();?></td></tr>
                <tr>
                    <th>操作系统</th>
                    <td><?php echo php_uname('s'); ?></td></tr>
                <tr>
                    <th>运行环境</th>
                    <td><?php echo $_SERVER["SERVER_SOFTWARE"];?></td></tr>
                <tr>
                    <th>PHP版本</th>
                    <td><?php echo PHP_VERSION;?></td></tr>
                <tr>
                    <th>PHP运行方式</th>
                    <td><?php echo php_sapi_name();?>></td></tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <fieldset class="layui-elem-field">
        <legend>开发团队</legend>
        <div class="layui-field-box">
            <table class="layui-table">
                <tbody>
                <tr>
                    <th>项目开源地址</th>
                    <td>
                        <a href="https://gitee.com/yuanzhumc/ber_Short_Url" class='x-a' target="_blank">码云：ber_Short_Url</a></td>
                <tr>
                    <th>版权所有</th>
                    <td><?php echo ber()?>
                        <a href="http://www.berfen.com/" class='x-a' target="_blank">访问官网</a></td>
                </tr>
                <tr>
                    <th>开发者</th>
                    <td>院主网络科技团队(www@berfen.com)</td></tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <blockquote class="layui-elem-quote layui-quote-nm">本系统由院主网络科技团队提供技术支持。</blockquote>
</div>

</body>
</html>