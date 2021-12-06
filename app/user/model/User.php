<?php
/**
 * User
 * @project ber_Short_Url
 * @copyright
 * @author
 * @version
 * @createTime 17:13
 * @filename User.php
 * @product_name PhpStorm
 * @link
 * @example
 */

namespace app\user\model;

use app\user\model\UserAuth;
use app\common\model\TimeModel;

class User extends TimeModel
{
    protected $deleteTime = 'delete_time';

    public function getAuthList()
    {
        $list = (new UserAuth())
            ->where('status', 1)
            ->column('title', 'id');
        return $list;
    }
}