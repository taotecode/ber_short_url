<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/20
 * @filename black.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 黑名单操作文件
 */
include "../../init.php";
$user_id=$_SESSION['user.id'];

$title='黑名单列表';//页面标题
include 'head.php';
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="">首页</a>
        <a>
          <cite><?php echo $title;?></cite></a>
      </span>
    <a class="layui-btn layui-btn-primary layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:38px">ဂ</i></a>
</div>
<div class="x-body">
    <table class="layui-hide" id="list" lay-filter="list"></table>
</div>
<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="del_all">删除选中</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="new_url">新建短链</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="Reload">刷新</button>
    </div>
</script>
<script type="text/html" id="tool_bar">
    <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="edit">操作</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs layui-btn-normal" lay-event="del">删除</a>
</script>
<script src="../js/php/black.js"></script>
<?php include "foot.php"; ?>