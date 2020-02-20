<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/20
 * @filename black.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 黑名单操作库
 */

namespace ber;
use M;
use ber\c\Fan;

class black
{
    public static function black_list($page,$limit){//黑名单列表
        $M=new M();
        $page=($page-1)*$limit;
        $ret= '{"code":0,"msg":"","count":'.$M->Total('black').',"data":';
        foreach ($M->FetchAll("black", "*", "", 'id DESC', $page.','.$limit) as $res){
            $data[] = array('id'=>$res['id'],'ip'=>$res['ip'],'url'=>$res['url']);
        }
        $ret .= json_encode($data).'}';
        return $ret;
    }

    public static function del($data){
        if (empty($data['id']))
            Fan::error('信息不完整');
        $M=new M;
        if (!$M->IsExists('black', "`id`=".$data['id']))
            Fan::error('数据不存在');
        if ($M->Del('black', '`id`='.$data['id']))
            Fan::ok();
        Fan::error('删除失败，服务器执行失败');
    }

    public static function del_all($data){
        if (empty($data['data']))
            Fan::error('信息不完整');
        $data=json_decode($data['data'],true);
        $M=new M;
        foreach ($data as $list){
            if (!$M->Del('black', '`id`='.$list['id']))
                Fan::error('删除失败，服务器执行失败');
        }
        Fan::ok();
    }
}