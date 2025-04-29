<?php

//添加菜单文档 https://www.workerman.net/q/10524

use plugin\acms\app\service\UserEvent;

return [
    // 'user.register' => [
    //     [UserEvent::class, 'register'],
    // ],
    // 'user.logout' => [
    // [UserEvent::class, 'logout'],
    // ...其它事件处理函数...
    // ],
    // 当渲染用户端导航菜单时
    'user.nav.render' => [
        [UserEvent::class, 'onUserNavRender'],
    ],

    // 当渲染用户中心左侧边栏时
    'user.sidebar.render' => [
        [UserEvent::class, 'onUserSidebarRender']
    ],
];