<?php
/**
 * UserAuth
 * @project ber_Short_Url
 * @copyright
 * @author
 * @version
 * @createTime 17:14
 * @filename UserAuth.php
 * @product_name PhpStorm
 * @link
 * @example
 */

namespace app\user\model;

use app\common\model\TimeModel;

class UserAuth extends TimeModel
{
    protected $deleteTime = 'delete_time';

    /**
     * 根据角色ID获取授权节点
     * @param $authId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAuthorizeNodeListByUserId($authId)
    {
        $checkNodeList = (new UserAuthNode())
            ->where('auth_id', $authId)
            ->column('node_id');
        $systemNode = new UserNode();
        $nodelList = $systemNode
            ->where('is_auth', 1)
            ->field('id,node,title,type,is_auth')
            ->select()
            ->toArray();
        $newNodeList = [];
        foreach ($nodelList as $vo) {
            if ($vo['type'] == 1) {
                $vo = array_merge($vo, ['field' => 'node', 'spread' => true]);
                $vo['checked'] = false;
                $vo['title'] = "{$vo['title']}【{$vo['node']}】";
                $children = [];
                foreach ($nodelList as $v) {
                    if ($v['type'] == 2 && strpos($v['node'], $vo['node'] . '/') !== false) {
                        $v = array_merge($v, ['field' => 'node', 'spread' => true]);
                        $v['checked'] = in_array($v['id'], $checkNodeList) ? true : false;
                        $v['title'] = "{$v['title']}【{$v['node']}】";
                        $children[] = $v;
                    }
                }
                !empty($children) && $vo['children'] = $children;
                $newNodeList[] = $vo;
            }
        }
        return $newNodeList;
    }
}