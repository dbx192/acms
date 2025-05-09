<?php

namespace plugin\acms\app\service;

use plugin\acms\api\Category;
use plugin\user\app\model\User as UserModel;
use stdClass;

/**
 * 用户相关事件
 */
class UserEvent
{

     public const SIDEBAR = [
        [
            'name' => 'ACMS',
            'items' => [
                ['name' => 'CMS系统', 'url' => '/app/acms'],
            ],
        ],
    ];

    public function onUserNavRender(\stdClass $object)
    {
        $this->renderNavigation($object, 'navs');
    }


    private function renderNavigation(\stdClass $object, $property)
    {
        $request = request();
        $path = $request ? $request->path() : '';

        $categories = self::SIDEBAR;

        foreach ($categories as $category) {
            $category['items'] = array_map(function ($item) use ($path) {
                $item['class'] = ($path === $item['url']) ? 'active' : '';

                return $item;
            }, $category['items']);

            $object->{$property}[] = $category;
        }
    }

    /**
     * 当渲染用户中心左侧菜单时
     * @param stdClass $object
     * @return void
     */
    public function onUserSidebarRender(stdClass $object)
    {
        $request = request();
        $path = $request ? $request->path() : '';
        // 添加CMS自己的左侧用户中心菜单
        $object->sidebars[] = [
            'name' => 'CMS系统',
            'items' => [
                ['name' => '最近看过', 'url' => '/app/acms/user/history', 'class' => $path === '/app/acms/user/history' ? 'active' : ''],
                ['name' => '收藏文章', 'url' => '/app/acms/user/likes', 'class' => $path === '/app/acms/user/likes' ? 'active' : ''],
                ['name' => '评论文章', 'url' => '/app/acms/user/commented', 'class' => $path === '/app/acms/user/commented' ? 'active' : ''],
            ],
        ];
    }
}
