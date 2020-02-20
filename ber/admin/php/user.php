<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/20
 * @filename user.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 用户信息更新文件
 */
include "../../init.php";
$user_id=$_SESSION['user.id'];

$title='个人信息';//页面标题
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
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md6">
            <form class="layui-form" action="" lay-filter="go">
                <div class="layui-card">
                    <div class="layui-card-header">信息区</div>
                    <div class="layui-card-body layui-row layui-col-space10">
                        <div class="layui-col-md12">
                            <input type="text" name="user" placeholder="账号" autocomplete="off"
                                   class="layui-input" value="<?php echo $user_id;?>">
                        </div>
                        <div class="layui-col-md12">
                            <input type="password" name="j_pass" placeholder="旧密码" autocomplete="off"
                                   class="layui-input">
                        </div>
                        <div class="layui-col-md12">
                            <input type="password" name="x_pass" placeholder="新密码" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                </div>
                <button lay-submit="" lay-filter="go" type="button" class="layui-btn layui-btn-normal">保存</button>
            </form>
        </div>
    </div>
</div>
<script>
    layui.use('form', function () {
        var form = layui.form;
        form.on('submit(go)', function (data) {
            if (data.field.user===''){
                layer.msg('请输入账号');
                return false;
            }
            if (data.field.j_pass===''){
                layer.msg('请输入旧密码');
                return false;
            }
            if (data.field.x_pass===''){
                layer.msg('请输入新密码');
                return false;
            }
            $.post("ajax.php", {mode: 'user_update', user: data.field.user, j_pass:data.field.j_pass, x_pass:data.field.x_pass},
                function (data) {
                    if (data.code === 200) {
                        layer.msg('更新成功，3秒后重新登录');
                        setInterval(function(){window.location.href="api.php?mode=logout";},3000);
                    } else {
                        layer.msg(data.error);
                        return false;
                    }
                }
            );
        });
    });
</script>