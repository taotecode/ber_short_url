<?php
/**
 * UserLog
 * @project ber_Short_Url
 * @copyright
 * @author
 * @version
 * @createTime 17:18
 * @filename UserLog.php
 * @product_name PhpStorm
 * @link
 * @example
 */

namespace app\user\model;
use app\common\model\TimeModel;
class UserLog extends TimeModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->name = 'user_log_' . date('Ym');
    }

    public function setMonth($month)
    {
        $this->name = 'user_log_' . $month;
        return $this;
    }

    public function user()
    {
        return $this->belongsTo('app\user\model\User', 'user_id', 'id');
    }
}