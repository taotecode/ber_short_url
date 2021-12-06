<?php
/**
 * Ajax
 * @project ber_Short_Url
 * @copyright
 * @author
 * @version
 * @createTime 16:57
 * @filename Ajax.php
 * @product_name PhpStorm
 * @link
 * @example
 */

namespace app\user\controller;

use app\common\controller\UserController;
use app\user\service\MenuService;
use think\facade\Cache;

class Ajax extends UserController
{
    /**
     * 初始化后台接口地址
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function initAdmin()
    {
        $cacheData = Cache::get('initUser_' . session('user.id'));
        if (!empty($cacheData)) {
            return json($cacheData);
        }
        $menuService = new MenuService(session('user.id'));
        $data = [
            'logoInfo' => [
                'title' => sysconfig('site', 'logo_title'),
                'image' => sysconfig('site', 'logo_image'),
                'href'  => __url('index/index'),
            ],
            'homeInfo' => $menuService->getHomeInfo(),
            'menuInfo' => $menuService->getMenuTree(),
        ];
        Cache::tag('initUser')->set('initUser_' . session('user.id'), $data);
        return json($data);
    }
}