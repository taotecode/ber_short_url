<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/1
 * @filename url_list.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example Url列表
 */
include "../../init.php";
$user_id=$_SESSION['user.id'];

$title='短链列表';//页面标题
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
<div class="layui-fluid" id="edit" style="display: none">
    <div class="layui-row layui-col-space15">
            <div class="layui-card">
                <table class="layui-table">
                    <thead>
                    <tr><th>基本信息</th><th>数值</th></tr>
                    </thead>
                    <tbody>
                    <tr><td>生成ip</td><td id="jb_ip"></td></tr>
                    <tr><td>短链</td><td id="jb_short"></td></tr>
                    <tr><td>原链</td><td id="jb_url"></td></tr>
                    <tr><td>访问次数</td><td id="jb_visit"></td></tr>
                    </tbody>
                </table>
                <div class="layui-form layui-card-body layui-row layui-col-space10" lay-filter="edit_form">
                    <label class="layui-form-label">短链后缀</label>
                        <input type="text" id="short" name="short" placeholder="请输入短链后缀" autocomplete="off" class="layui-input">
                    <label class="layui-form-label">原来的链接</label>
                        <input type="text" id="url" name="url" placeholder="请输入原链" autocomplete="off" class="layui-input">
                    <div class="layui-card layui-form" lay-filter="component-form-element">
                        <div class="layui-card-header">选择短链属性</div>
                        <div class="layui-card-body layui-row layui-col-space10">
                            <div class="layui-col-md12">
                                <input type="radio" name="type" value="0" title="普通">
                                <input type="radio" name="type" value="1" title="防红">
                                <input type="radio" name="type" value="2" title="拉黑生成IP">
                                <input type="radio" name="type" value="3" title="拉黑原链">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
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
<script type="text/html" id="user">
    {{#  if(d.user === '0'){ }}
    <span style="color: #009688;">路人</span>
    {{# } else if(d.user=== '<?php echo $user_id; ?>') { }}
    <span style="color: #393D49">管理员</span>
    {{#  } else { }}
    <span style="color: #5FB878">{{ d.user }}</span>
    {{#  } }}
</script>
<script type="text/html" id="type">
    {{#  if(d.type === '0'){ }}
    <span style="color: #01AAED">普通</span>
    {{# } else if(d.type=== '1') { }}
    <span style="color: #FF5722">防红</span>
    {{# } else if(d.type=== '2') { }}
    <span style="color: #2F4056">ip已拉黑</span>
    {{# } else if(d.type=== '3') { }}
    <span style="color: #393D49">链接已拉黑</span>
    {{#  } else { }}
    数据错误：{{ d.type }}
    {{#  } }}
</script>

<script src="../js/php/url_list.js"></script>
<?php include "foot.php"; ?>
