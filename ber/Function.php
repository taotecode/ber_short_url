<?php
/**
 * @title 正则匹配URL
 * @param $url @url链接
 * @return bool
 */
function check_url($url)
{
    if(preg_match("/^http(s)?:\\/\\/.+/",$url))
        return true;
    else
        return false;
}

/**
 * @title 获取url标题
 * @param $url @url链接
 * @return mixed|string
 */
function url_title($url)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_HEADER => false,
        CURLOPT_URL => $url,
        //考虑到有些网站是301跳转的.
        CURLOPT_FOLLOWLOCATION => true,
        //连接的超时时间设置为5秒
        CURLOPT_CONNECTTIMEOUT => 5,
        //响应超时时间为5秒
        CURLOPT_TIMEOUT => 5,
        CURLOPT_VERBOSE => false,
        CURLOPT_AUTOREFERER => true,
        //接收所有的编码
        CURLOPT_ENCODING => '',
        //返回页面内容
        CURLOPT_RETURNTRANSFER => true,
    ));
    $response = curl_exec($ch);
//检测网页的编码,把非UTF-8编码的页面,统一转换为UTF-8处理.
    if ('UTF-8' !== ($encoding = mb_detect_encoding($response, array('UTF-8', 'CP936', 'ASCII')))) {
        $response = mb_convert_encoding($response, 'UTF-8', $encoding);
    }
//匹配一下title
    $title = '';
    if (preg_match('#<title>(.*)</title>#isU', $response, $match)) {
        $title = $match[1];
    }
    return $title;
}
function h_url($url){//展示url
    $html='<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>'.url_title($url).'</title>
    <link rel="stylesheet" type="text/css" href="https://www.layuicdn.com/layui/css/layui.css"><!--可以更改为你认为最快的layuiCDN-->
    <script src="https://www.layuicdn.com/layui/layui.js"></script><!--可以更改为你认为最快的layuiCDN-->
</head>
<body>
<script>
    layui.use(["layer"], function () {
        var layer = layui.layer;
        var url = "'.$url.'";
        var ua = navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
            hong(url);
        } else if (ua.match(/QQ/i) == "qq") {
            hong(url);
        } else if (ua.match(/Alipay/i) == "alipay" && payway == 2) {
            hong(url);
        } else {
            window.location.href = url;
        }
        function hong(url) {
            var tips = "因为QQ和微信和支付宝的限制，我们推荐您在系统或其他浏览器访问。" +
                "<br>您也可以使用由BER分短网址提供的在线预览服务，来临时预览网页。" +
                "<br>短网址的原地址与BER分短网址服务无任何关系，如有发生任何责任或其他，BER分短网址无需承担任何责任。";
            layer.confirm(tips, {
                btn: ["预览", "复制链接", "BER分短网址"], title: "BER分短链提示"
                , btn3: function () {window.location.href = "http://berf1.cn/";}
            }, function () {layer.open({type: 2, area: ["100%", "100%"], title: "BER分短链在线预览", move: false, content: url});}
            , function () {copyText(url, function () {layer.msg("复制成功！<br>您可以直接打开浏览器复制访问。");})
            });
        }
        function copyText(text, callback){
            var tag = document.createElement(\'input\');
            tag.setAttribute(\'id\', \'cp_hgz_input\');
            tag.value = text;
            document.getElementsByTagName(\'body\')[0].appendChild(tag);
            document.getElementById(\'cp_hgz_input\').select();
            document.execCommand(\'copy\');
            document.getElementById(\'cp_hgz_input\').remove();
            if(callback) {callback(text)}
        }
    });
</script>
</body>
</html>';
    return $html;
}
?>