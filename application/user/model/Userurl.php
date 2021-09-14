<?php


namespace app\user\model;
use think\Model;
use think\Request;


class Userurl extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'user_url';

    /**
     * @param $data array 修改数据
     * @param $coded string 短链编码
     * @return false|int
     * @example 修改用户短链数据
     */
    public function update_user_url($data,$coded){
        $url = model('User_url');
        $update_data=[
            'ip'=>Request::instance()->ip(),
            'url'=>$data['url'],
            'type'=>$data['type'],
            'config'=>$data['config'],
            'third_count'=>$data['third_count'],
            'third_url'=>$data['third_url'],
            'expire_time'=>$data['expire_time'],
            'update_time'=>date('Y-m-d H:i:s')
        ];
        return $url->save($update_data,['coded'=>$coded]);
    }

}