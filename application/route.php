<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//首页路由组
Route::group('/', function(){
    //Route::rule('/', 'index');//首页
    Route::rule('index', 'index');
});
Route::rule('login', 'index/Login/login');//用户登录
Route::rule('logout', 'index/Login/logout');//用户退出
Route::rule('reg_mail_code', 'index/Login/reg_mail_code');//用户发送邮箱验证码
Route::rule('reg', 'index/Login/reg');//用户注册
//用户管理路由组
Route::group('user', function(){
    Route::rule('welcome','user/page/welcome');
    Route::rule('url_report','user/page/url_report');
    Route::rule('work_report','user/page/work_report');
    Route::rule('new_url','user/page/new_url');
    Route::rule('url_list','user/page/url_list');
    Route::rule('work_url_list','user/page/work_url_list');
    Route::rule('url_data','user/page/url_data');
    Route::rule('report_list','user/page/report_list');
    Route::rule('vip','user/page/vip');
    Route::rule('api','user/page/api');
    Route::rule('black','user/page/black');
    Route::rule('user_ranking','user/page/user_ranking');
    Route::rule('appeal','user/page/appeal');
    Route::rule('appeal_list','user/page/appeal_list');
    Route::rule('link_conf','user/page/link_conf');
    Route::rule('user','user/page/user');
    //用户后台AJAX接口
    Route::group('ajax', function(){
        Route::rule('new_url','user/ajax/new_url');
        Route::rule('new_work_url','user/ajax/new_work_url');
        Route::rule('check_intercept_url','user/ajax/check_intercept_url');
        Route::rule('suspend_url','user/ajax/suspend_url');
        Route::rule('recovery_url','user/ajax/recovery_url');
        Route::rule('del_url','user/ajax/del_url');
        Route::rule('update_url','user/ajax/update_url');
    });
    //用户后台JSON接口
    Route::group('json', function(){
        Route::rule('url_list','user/json/url_list');
        Route::rule('work_url_list','user/json/work_url_list');
    });
    Route::get('/','user/index/index');
});
//API路由组
Route::group('api', function(){
    Route::rule('index','api/Index/index');
});

//Route::controller('user/ajax', 'user/ajax');
//短链开始
Route::pattern('name','\w+');
Route::rule('/:name', 'url/index/index');



//最后再判断链接注册





/*Route::group('/', function(){
    Route::rule('/', 'index');
    Route::rule('/index', 'index');
    Route::rule('user/login', 'index/Login/login');
    Route::rule('user/register', 'index/Register/register');
    Route::rule('user/logout', 'index/Login/logout');
    Route::rule('/safeJump', 'index/index/safeJump');
});

//后台路由组
Route::group('admin', function(){
    //后台首页
    Route::rule('/', 'admin/index/index');
    Route::rule('/main', 'admin/index/main');
    //修改密码
    Route::rule('/editAdmin', 'admin/index/editadmin');
    Route::rule('/changeAdminInfo', 'admin/index/changeAdminInfo');
    //后台登录界面
    Route::rule('/login', 'admin/login/index');
    Route::rule('/adminLogin', "admin/login/login");
    //退出登录
    Route::rule('/logout', "admin/index/logout");
    //系统设置路由组
    Route::group('system', function(){
        Route::rule('/', 'admin/system/index');
    });
    //链接管理路由组
    Route::group('link', function(){
        Route::rule('/', 'admin/link/index');
        Route::rule('/linkList', 'admin/link/linkList');
        Route::rule('/linkSearch', 'admin/link/linkSearch');
        Route::rule('/changeLinkStatus', 'admin/link/changeLinkStatus');
        Route::rule('/delLink', 'admin/link/delLink');
    });

    //用户管理路由组
    Route::group('user', function(){
        Route::rule('/', 'admin/user/index');
        Route::rule('/userList', 'admin/user/userList');
        Route::rule('/searchUser', 'admin/user/searchUser');
        Route::rule('/changeUserStatus', 'admin/user/changeUserStatus');
        Route::rule('/delUser', 'admin/user/delUser');
    });
});

// Route::controller('api', 'api/index');
// 接口路由组
Route::group('api', function(){
    Route::rule('/', 'api/index/index');
    Route::rule('/getShortUrl', 'api/index/getshorturl');
    Route::rule('/getOriginalUrl', 'api/index/getOriginalUrl');
});
//最低优先级
Route::rule('/:name', 'index/getName');*/