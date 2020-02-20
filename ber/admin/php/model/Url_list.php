<?php
/**
 * @project ber_Short_Url
 * @copyright 院主网络科技团队
 * @author 院主网络科技团队
 * @version 1.2
 * @createTime 2020/2/1
 * @filename Url_list.php
 * @link https://gitee.com/yuanzhumc/ber_Short_Url
 * @example 短链列表的操作
 */

namespace ber;
use M;
use ber\c\Fan;
use ber\c\Coded;
class Url_list
{
    public static function json_list($page,$limit){//url列表
        global $http_url;
        $M=new M();
        $page=($page-1)*$limit;
        $ret= '{"code":0,"msg":"","count":'.$M->Total('url').',"data":';
        foreach ($M->FetchAll("url", "*", "", 'id DESC', $page.','.$limit) as $res){
            $data[] = array('id'=>$res['id'],'user'=>$res['user'],'ip'=>$res['ip'],
                'short'=>$http_url.$res['url_coded'],'url'=>$res['url'],'type'=>$res['url_type'],
                'visit'=>$res['visit'],'time'=>$res['time']
            );
        }
        $ret .= json_encode($data).'}';
        return $ret;
    }

    public static function new_url($data){//新建短链
        if (empty($data['url']))
            Fan::error('请输入长链');
        if (empty($data['user']))
            Fan::error('请选择用户');
        if (!check_url($data['url']))
            Fan::error('长链格式不正确');
        if ($data['type']<0||$data['type']>1)
            Fan::error('短链属性不正确');
        $M=new M();
        if ($data['user'] != 0){
            if (!$M->IsExists('user', "`user`='".$data['user']."'"))
                Fan::error('管理员用户不存在');
        }
        if (empty($data['coded'])){
            $coded=Coded::url_coded_short($data['url']);
        }else
            $coded=$data['coded'];
        if ($M->IsExists('url', "url_coded='$coded'"))
            Fan::error('该短链后缀已存在，请更换');
        if ($M->IsExists('url', "url='".$data['url']."'"))
            Fan::error('该长链已存在，请更换');
        $sql_table=array('user','ip','url','url_coded','url_type');
        $sql_data=array($data['user'],get_ip(),$data['url'],$coded,$data['type']);
        if ($M->Insert("url",$sql_table,$sql_data))
            Fan::ok();
        Fan::error('添加失败，服务器执行失败');
    }

    public static function url_edit($data){//url编辑
        if (empty($data['short']))
            Fan::error('请输入短链后缀');
        if (empty($data['url']))
            Fan::error('请输入原长链');
        if ($data['type']<0||$data['type']>1){
            if ($data['type']==2){
                Url_list::url_ip($data);
            }elseif ($data['type']==3){
                Url_list::url_url($data);
            }else
                Fan::error('请选择正确的短链属性');
        }else{
            $M=new M;
            if (!$M->IsExists('url', "`id`=".$data['id']))
                Fan::error('数据不存在');
            if ($M->Update("url", array('url'=>$data['url'], 'url_coded'=>$data['short'],'url_type'=>$data['type']), "id=".$data['id']))
                Fan::ok();
            Fan::error('保存失败，服务器执行失败');
        }
    }

    public static function url_ip($data){//拉黑IP
        if (empty($data))
            Fan::error('信息不完整');
        $M=new M;
        if (!$M->IsExists('url', "`id`=".$data['id']))
            Fan::error('数据不存在');
        if ($M->Update("url", array('url_type'=>2), "id=".$data['id'])){
            if (!$M->IsExists('black', "ip='".$data['ip']."'")){
                if ($M->Insert("black",array('ip'), array($data['ip'])))
                    Fan::ok();
                Fan::error('保存失败，服务器执行失败');
            }
            Fan::ok();
        }
        Fan::error('保存失败，服务器执行失败');
    }

    public static function url_url($data){//拉黑链接
        if (empty($data))
            Fan::error('信息不完整');
        $M=new M;
        if (!$M->IsExists('url', "`id`=".$data['id']))
            Fan::error('数据不存在');
        if ($M->Update("url", array('url_type'=>3), "id=".$data['id'])){
            if (!$M->IsExists('black', "url='".$data['url']."'")){
                if ($M->Insert("black",array('url'), array($data['url'])))
                    Fan::ok();
                Fan::error('保存失败，服务器执行失败');
            }
            Fan::ok();
        }
        Fan::error('保存失败，服务器执行失败');
    }

    public static function url_del($data){//单个短链删除
        if (empty($data['id']))
            Fan::error('信息不完整');
        $M=new M;
        if (!$M->IsExists('url', "`id`=".$data['id']))
            Fan::error('数据不存在');
        if ($M->Del('url', '`id`='.$data['id']))
            Fan::ok();
        Fan::error('删除失败，服务器执行失败');
    }

    public static function url_del_all($data){//选中删除
        if (empty($data['data']))
            Fan::error('信息不完整');
        $data=json_decode($data['data'],true);
        $M=new M;
        foreach ($data as $list){
            if (!$M->Del('url', '`id`='.$list['id']))
                Fan::error('删除失败，服务器执行失败');
        }
        Fan::ok();
    }
}