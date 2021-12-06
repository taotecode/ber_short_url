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


use think\facade\Cache;

class TriggerService
{

    /**
     * 更新菜单缓存
     * @param null $adminId
     * @return bool
     */
    public static function updateMenu($adminId = null)
    {
        if(empty($adminId)){
            Cache::tag('initUser')->clear();
        }else{
            Cache::delete('initUser_' . $adminId);
        }
        return true;
    }

    /**
     * 更新节点缓存
     * @param null $adminId
     * @return bool
     */
    public static function updateNode($adminId = null)
    {
        if(empty($adminId)){
            Cache::tag('authUserNode')->clear();
        }else{
            Cache::delete('allAuthUserNode_' . $adminId);
        }
        return true;
    }

    /**
     * 更新系统设置缓存
     * @return bool
     */
    public static function updateSysconfig()
    {
        Cache::tag('sysconfig')->clear();
        return true;
    }

}