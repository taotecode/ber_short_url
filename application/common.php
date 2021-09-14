<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use PHPMailer\PHPMailer;
// 应用公共文件
function check_url($url)
{
    if (preg_match("/^http(s)?:\\/\\/.+/", $url))
        return true;
    else
        return false;
}

function check_identification($id)
{
    if (!preg_match("/^\d*$/", $id)){//如果不是数字
        if (!preg_match("/^[A-Za-z]+$/",$id)){//如果不是英文
            return false;
        }else{//是英文
            if (strlen($id)>1){//如果长度大于1
                return false;
            }
            return true;
        }
    }else{//是数字或英文
        if (strlen($id)>1){//如果长度大于1
            return false;
        }
        return true;
    }
}
function is_url_encoded($url){
    if (is_urlencoded($url)){
        return $url;
    }else{
        return urlencode($url);
    }
}
function curl_post($url,$params=[],$head=[], $timeout=10)
{
    $ch = curl_init();//初始化
    curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);//请求头
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    return ($data);
}
function suo_url($url,$key,$time,$type=null)
{
    global $http_url;
    if ($type=='cs')
        $url=$http_url.'mb/suo-cs.html';
    $send_url = 'http://suo.im/api.htm?url='.$url.'&key='.$key.'&expireDate='.$time;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $send_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
//生成w.url.cn
function w_url_url($url,$user,$type=true){
    $data=curl_post('https://wx.berfen.com/app/BER/msg/short/mid/3','url='.$url);
    $data=json_decode($data,true);
    if ($data['code']=='200'){
        if ($type==true)
            w_url_reduce($user);
        return $data['url'];
    }
    else{
        return false;
    }
}
//减少一次用户的w.url.cn生成次数
function w_url_reduce($user){
    db('user')->where('user', $user)->setDec('w_url');
}
function douyin_url($url) {
    $s = json_decode(file_get_contents("https://app.10086.cn/short?url=".urlencode($url)),true);
    if ($s['retcode'] == '000000') {
        return $s['shorten'];
    } else {
        return false;
    }
}
function check_intercept_qq($url='https://www.berfen.com'){
    $json = json_decode(curl_post("https://api.oioweb.cn/api/ymjc.php?url={$url}"),true);
    if ($json['qq']=='拦截'){
        return false;
    }else{
        return true;
    }
}
function check_intercept_wx($url){
    if (check_str(file_get_contents($url),'已停止访问该网页')){
        return true;
    }else{
        return false;
    }
}
function check_str($str,$needle){
    $tmparray = explode($needle,$str);
    if(count($tmparray)>1){
        return true;
    } else{
        return false;
    }
}
function is_webBrowser(){
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'QQ') !== false){
        if(strpos($_SERVER['HTTP_USER_AGENT'], '_SQ_') !== false){
            return true;  //QQ内置浏览器
        }else{
            return false;  //QQ浏览器
        }
    }
    return false;
}
function is_wx_browser(){
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($ua, 'MicroMessenger') == false && strpos($ua, 'Windows Phone') == false) {
        return false;
    } else {
        return true;//微信
    }
}
function is_urlencoded($url){
    $str = strtoupper($url);
    $dontNeedEncoding = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789-_.";
    $encoded = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $needEncode = false;
    for ($i = 0; $i < strlen($str); $i++) {
        $c = substr($str, $i, 1);
        if (strpos($dontNeedEncoding, $c) !== false) {//不需要处理
            continue;
        }
        if ($c == '%' && ($i + 2) < strlen($str)) { // 判断是否符合urlEncode规范
            $c1 = substr($str, ++$i, 1);
            $c2 = substr($str, ++$i, 1);
            if (strpos($encoded, $c1) !== false && strpos($encoded, $c2) !== false) {
                continue;
            }
        } // 其他字符，肯定需要urlEncode
        $needEncode = true;
        break;
    } //如果有字符需要进行编码，那这个字符串肯定就是没有经过编码的
    return !$needEncode;
}
function get_server_ip()
{
    if (!empty($_SERVER['SERVER_ADDR']))
        return $_SERVER['SERVER_ADDR'];
    $result = shell_exec("/sbin/ifconfig");
    if (preg_match_all("/addr:(\d+\.\d+\.\d+\.\d+)/", $result, $match) !== 0) {
        foreach ($match[0] as $k => $v) {
            if ($match[1][$k] != "127.0.0.1")
                return $match[1][$k];
        }
    }
    return false;
}

function get_ip()
{//获取用户IP
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    }
    if (getenv('HTTP_X_REAL_IP')) {
        $ip = getenv('HTTP_X_REAL_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
        $ips = explode(',', $ip);
        $ip = $ips[0];
    } elseif (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    } else {
        $ip = '0.0.0.0';
    }
    return $ip;
}

/**
 * @title url短码算法-6位字符
 * @param string $url
 * @param string $key
 * @return mixed
 */
function url_coded_short($url,$key='ber')
{
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
/**
 * @title url短码算法-随机数生成 随机数组成一个指定数量的字符
 * @param int $length
 * @return string
 */
function url_coded_random($length=6)
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
 * @param $to_mail
 * @param $name
 * @param string $subject
 * @param string $body
 * @param null $attachment
 * @return bool|string
 * @throws \PHPMailer\Exception
 */
function send_mail($to_mail,$name,$subject = '', $body = '', $attachment = null) {
    $mail = new PHPMailer();           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';          // 使用安全协议
    $mail->Host = "smtp.exmail.qq.com"; // SMTP 服务器
    $mail->Port = 465;                  // SMTP服务器的端口号
    $mail->Username = "send@berfen.com";    // SMTP服务器用户名
    $mail->Password = "EUr4LSqbQxffoP23";     // SMTP服务器密码
    $mail->SetFrom('send@berfen.com', 'BER分系统');
    $replyEmail = '';                   //留空则为发件人EMAIL
    $replyName = '';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to_mail,$name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

function mail_mb_code($title, $code, $user){
    //邮件验证码
    $html = '
<head>
    <base target="_blank" />
    <style type="text/css">::-webkit-scrollbar{ display: none; }</style>
    <style id="cloudAttachStyle" type="text/css">#divNeteaseBigAttach, #divNeteaseBigAttach_bak{display:none;}</style>
    <style id="blockquoteStyle" type="text/css">blockquote{display:none;}</style>
    <style type="text/css">
        body{font-size:14px;font-family:arial,verdana,sans-serif;line-height:1.666;padding:0;margin:0;overflow:auto;white-space:normal;word-wrap:break-word;min-height:100px}
        td, input, button, select, body{font-family:Helvetica, \'Microsoft Yahei\', verdana}
        pre {white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;width:95%}
        th,td{font-family:arial,verdana,sans-serif;line-height:1.666}
        img{ border:0}
        header,footer,section,aside,article,nav,hgroup,figure,figcaption{display:block}
        blockquote{margin-right:0px}
    </style>
</head>
<body tabindex="0" role="listitem">
<table width="700" border="0" align="center" cellspacing="0" style="width:700px;">
    <tbody>
    <tr>
        <td>
            <div style="width:700px;margin:0 auto;border-bottom:1px solid #ccc;margin-bottom:30px;">
                <table border="0" cellpadding="0" cellspacing="0" width="700" height="39" style="font:12px Tahoma, Arial, 宋体;">
                    <tbody><tr><td width="210"></td></tr></tbody>
                </table>
            </div>
            <div style="width:680px;padding:0 10px;margin:0 auto;">
                <div style="line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;">
                    <strong style="display:block;margin-bottom:15px;">尊敬的用户：<span style="color:#f60;font-size: 16px;">' . $user . '</span>您好！</strong>
                    <strong style="display:block;margin-bottom:15px;">
                        您正在进行<span style="color: red">' . $title . '</span>操作，请在验证码输入框中输入：<span style="color:#f60;font-size: 24px">' . $code. '</span>，以完成操作。<br>
                        请在<b>10分钟</b>内完成操作，否则验证码将过期！
                    </strong>
                </div>
                <div style="margin-bottom:30px;">
                    <small style="display:block;margin-bottom:20px;font-size:12px;">
                        <p style="color:#747474;">
                            注意：此操作可能会修改您的密码、登录邮箱或绑定手机。如非本人操作，请及时登录并修改密码以保证帐户安全
                            <br>（工作人员不会向你索取此验证码，请勿泄漏！)
                        </p>
                    </small>
                </div>
            </div>
            <div style="width:700px;margin:0 auto;">
                <div style="padding:10px 10px 0;border-top:1px solid #ccc;color:#747474;margin-bottom:20px;line-height:1.3em;font-size:12px;">
                    <p>此为系统邮件，请勿回复<br>
                        请保管好您的邮箱，避免账号被他人盗用
                    </p>
                    <p>©BER分接口网——院主网络科技团队</p>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
</body>
';
    return $html;
}