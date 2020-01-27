<?php
include('ber/init.php');
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
    header('HTTP/1.1 301 Moved Permanently');
    header('Location:'.$data['url']);
}elseif ($data['url_type']==1) {
    exit(h_url($data['url']));
}
?>