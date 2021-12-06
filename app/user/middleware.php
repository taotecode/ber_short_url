<?php
// 全局中间件定义文件
return [

    // Session初始化
    \think\middleware\SessionInit::class,

    // 系统操作日志
    \app\user\middleware\SystemLog::class,

    // Csrf安全校验
    \app\admin\middleware\CsrfMiddleware::class,
];
