<?php

use plugin\acms\app\admin\controller\ArticleController;
use plugin\acms\app\admin\controller\CategoryController;
use plugin\acms\app\admin\controller\TagController;

return [
    [
        'title' => 'CMS管理',
        'key' => 'acms',
        'icon' => 'layui-icon-list',
        'weight' => 0,
        'type' => 0,
        'children' => [
            [
                'title' => '文章管理',
                'key' => ArticleController::class,
                'href' => '/app/admin/acms/article/index',
                'type' => 1,
                'weight' => 2,
            ],
            [
                'title' => '分类管理',
                'key' => CategoryController::class,
                'href' => '/app/admin/acms/category/index',
                'type' => 1,
                'weight' => 1,
            ],
            [
                'title' => '标签管理',
                'key' => TagController::class,
                'href' => '/app/admin/acms/tag/index',
                'type' => 1,
                'weight' => 0,
            ],
            [
                'title' => '评论管理',
                'key' => 'plugin\\acms\\app\\admin\\controller\\CommentController',
                'href' => '/app/admin/acms/comment/index',
                'type' => 1,
                'weight' => -1,
            ],
            [
                'title' => '数据看板',
                'key' => 'plugin\\acms\\app\\admin\\controller\\DashboardController',
                'href' => '/app/admin/acms/dashboard/index',
                'type' => 1,
                'weight' => -2,
            ],

        ]
    ]
];
