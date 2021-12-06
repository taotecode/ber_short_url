<?php

// +----------------------------------------------------------------------
// | EasyAdmin
// +----------------------------------------------------------------------
// | PHP交流群: 763822524
// +----------------------------------------------------------------------
// | 开源协议  https://mit-license.org 
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/EasyAdmin
// +----------------------------------------------------------------------

namespace app\user\service;

use app\common\constants\AdminConstant;
use EasyAdmin\tool\CommonTool;
use think\facade\Db;

/**
 * 权限验证服务
 * Class AuthService
 * @package app\common\service
 */
class AuthService
{

    /**
     * 用户ID
     * @var null
     */
    protected $userId = null;

    /**
     * 默认配置
     * @var array
     */
    protected $config = [
        'auth_on'          => true,              // 权限开关
        'user'     => 'user',    // 用户表
        'user_auth'      => 'user_auth',     // 权限表
        'user_node'      => 'user_node',     // 节点表
        'user_auth_node' => 'user_auth_node',// 权限-节点表
    ];

    /**
     * 管理员信息
     * @var array|\think\Model|null
     */
    protected $userInfo;

    /**
     * 所有节点信息
     * @var array
     */
    protected $nodeList;

    /**
     * 管理员所有授权节点
     * @var array
     */
    protected $userNode;

    /***
     * 构造方法
     * AuthService constructor.
     * @param null $userId
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function __construct($userId = null)
    {
        $this->userId = $userId;
        $this->userInfo = $this->getUserInfo();
        $this->nodeList = $this->getNodeList();
        $this->userNode  = $this->getUserNode();
        return $this;
    }

    /**
     * 检测检测权限
     * @param null $node
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkNode($node = null)
    {
        // 判断是否为超级管理员
        if ($this->userId == AdminConstant::SUPER_ADMIN_ID) {
            return true;
        }
        // 判断权限验证开关
        if ($this->config['auth_on'] == false) {
            return true;
        }
        // 判断是否需要获取当前节点
        if (empty($node)) {
            $node = $this->getCurrentNode();
        } else {
            $node = $this->parseNodeStr($node);
        }
        // 判断是否加入节点控制，优先获取缓存信息
        if (!isset($this->nodeList[$node])) {
            return false;
        }
        $nodeInfo = $this->nodeList[$node];
        if ($nodeInfo['is_auth'] == 0) {
            return true;
        }
        // 用户验证，优先获取缓存信息
        if (empty($this->userInfo) || $this->userInfo['status'] != 1 || empty($this->userInfo['auth_ids'])) {
            return false;
        }
        // 判断该节点是否允许访问
        if (in_array($node, $this->userNode)) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前节点
     * @return string
     */
    public function getCurrentNode()
    {
        $node = $this->parseNodeStr(request()->controller() . '/' . request()->action());
        return $node;
    }

    /**
     * 获取当前管理员所有节点
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserNode()
    {
        $nodeList = [];
        $userInfo = Db::name($this->config['user'])
            ->where([
                'id'     => $this->userId,
                'status' => 1,
            ])->find();
        if (!empty($userInfo) && !empty($userInfo['auth_ids'])) {
            $buildAuthSql = Db::name($this->config['user_auth'])
                ->distinct(true)
                ->whereIn('id', $userInfo['auth_ids'])
                ->field('id')
                ->buildSql(true);
            $buildAuthNodeSql = Db::name($this->config['user_auth_node'])
                ->distinct(true)
                ->where("auth_id IN {$buildAuthSql}")
                ->field('node_id')
                ->buildSql(true);
            $nodeList = Db::name($this->config['user_node'])
                ->distinct(true)
                ->where("id IN {$buildAuthNodeSql}")
                ->column('node');
        }
        return $nodeList;
    }

    /**
     * 获取所有节点信息
     * @time 2021-01-07
     * @return array
     * @author zhongshaofa <shaofa.zhong@happy-seed.com>
     */
    public function getNodeList(){
        return  Db::name($this->config['user_node'])
            ->column('id,node,title,type,is_auth','node');
    }

    /**
     * 获取管理员信息
     * @time 2021-01-07
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhongshaofa <shaofa.zhong@happy-seed.com>
     */
    public function getUserInfo(){
        return  Db::name($this->config['user'])
            ->where('id', $this->userId)
            ->find();
    }

    /**
     * 驼峰转下划线规则
     * @param string $node
     * @return string
     */
    public function parseNodeStr($node)
    {
        $array = explode('/', $node);
        foreach ($array as $key => $val) {
            if ($key == 0) {
                $val = explode('.', $val);
                foreach ($val as &$vo) {
                    $vo = CommonTool::humpToLine(lcfirst($vo));
                }
                $val = implode('.', $val);
                $array[$key] = $val;
            }
        }
        $node = implode('/', $array);
        return $node;
    }

}