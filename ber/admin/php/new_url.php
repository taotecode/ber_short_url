<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/1
 * @filename new_url.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 新建短链
 */
include "../../init.php";
$user_id = $_SESSION['user.id'];

$title = '新建短链';//页面标题
include 'head.php';
?>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="">首页</a>
        <a>
          <cite><?php echo $title; ?></cite></a>
      </span>
        <a class="layui-btn layui-btn-primary layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right"
           href="javascript:location.replace(location.href);" title="刷新">
            <i class="layui-icon" style="line-height:38px">ဂ</i></a>
    </div>
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md6">
                <form class="layui-form" action="" lay-filter="go">
                    <div class="layui-card">
                        <div class="layui-card-header">内容区</div>
                        <div class="layui-card-body layui-row layui-col-space10">
                            <div class="layui-col-md12">
                                <input type="text" name="url" placeholder="请输入原链长链" autocomplete="off"
                                       class="layui-input">
                            </div>
                            <div class="layui-col-md12">
                                <input type="text" name="coded" placeholder="自定义短链后缀(无需加http://...)" autocomplete="off"
                                       class="layui-input">
                                <p>可以为空，为空则自动生成算法</p>
                            </div>
                        </div>
                    </div>
                    <div class="layui-card layui-form" lay-filter="component-form-element">
                        <div class="layui-card-header">身份选择</div>
                        <div class="layui-card-body layui-row layui-col-space10">
                            <div class="layui-col-md12">
                                <select name="user" lay-verify="">
                                    <option value="">请选择一个身份</option>
                                    <option value="0">路人</option>
                                    <option value="<?php echo $user_id; ?>">管理员</option>
                                </select>
                            </div>
                            <div class="layui-col-md12">
                                <select name="type" lay-verify="">
                                    <option value="">请选择短链属性</option>
                                    <option value="0">普通</option>
                                    <option value="1">防红</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button lay-submit="" lay-filter="go" type="button" class="layui-btn layui-btn-normal">添加</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/php/new_url.js"></script>
<?php include "foot.php"; ?>