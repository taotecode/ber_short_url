<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/1/28
 * @filename Template.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 首页模板文件
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>BER分短网址服务-BER分接口网</title>
    <link rel="stylesheet" type="text/css" href="https://www.layuicdn.com/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="static/css/index.css">
    <script src="https://www.layuicdn.com/layui/layui.js"></script>
    <script src="static/js/jquery.min.js"></script>
    <script src="static/js/jquery.cookie.min.js"></script>
    <script>layui.use(['layer', 'form'], function () {var layer = layui.layer, form = layui.form;});</script>
    <?php echo $header_count;?>
</head>
<body>
<h1 class="h1 bt">BER分短网址服务</h1>
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-form">
            <div class="layui-form-item">
                <input type="text" name="url" required lay-verify="required" placeholder="请输入网址" autocomplete="off"
                       class="layui-input" id="url">
            </div>
            <div class="layui-form-item" style="text-align: center">
                <button type="button" class="layui-btn layui-btn-lg layui-btn-normal" id="s">生成</button>
                <button type="button" class="layui-btn layui-btn-lg layui-btn-normal" id="h">还原</button>
                <button type="button" class="layui-btn layui-btn-lg layui-btn-normal" id="f">生成防红链接</button>
            </div>
        </div>
        <div id="display" style="display: none">
            <div style="padding: 20px; background-color: #F2F2F2;">
                <div class="layui-card">
                    <div class="layui-card-header">成功</div>
                    <div class="layui-card-body">
                        <div class="layui-btn-container">
                            <p>原网址:<span id="y-url"></span> </p>
                            <p>短网址:<span id="d-url"></span> </p>
                            <p id="f-h"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding: 20px; background-color: #F2F2F2;">
            <div class="layui-card">
                <div class="layui-card-header">最新消息</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container">
                        <p>
                        免费使用-永久保存<br>
                        每个用户每分钟只可使用一次
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-footer" style="text-align: center;margin-top: 30px">
        © <a href="http://www.berfen.com/" target="_blank">www.berfen.com-BER分接口网</a> 版权所属：院主网络科技团队-苏ICP备19068321号-2
    </div>
</div>
<script src="static/js/index.js"></script>
<?php echo $footer_count;?>
</body>
</html>