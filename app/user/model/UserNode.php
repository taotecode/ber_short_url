<?php
/**
 * UserNode
 * @project ber_Short_Url
 * @copyright
 * @author
 * @version
 * @createTime 17:15
 * @filename UserNode.php
 * @product_name PhpStorm
 * @link
 * @example
 */

namespace app\user\model;
use app\common\model\TimeModel;
class UserNode extends TimeModel
{
    public function getNodeTreeList()
    {
        $list = $this->select()->toArray();
        $list = $this->buildNodeTree($list);
        return $list;
    }

    protected function buildNodeTree($list)
    {
        $newList = [];
        $repeatString = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        foreach ($list as $vo) {
            if ($vo['type'] == 1) {
                $newList[] = $vo;
                foreach ($list as $v) {
                    if ($v['type'] == 2 && strpos($v['node'], $vo['node'] . '/') !== false) {
                        $v['node'] = "{$repeatString}├{$repeatString}" . $v['node'];
                        $newList[] = $v;
                    }
                }
            }
        }
        return $newList;
    }
}