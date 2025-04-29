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

    /**
     * 当渲染用户端顶部导航菜单时
     * @param stdClass $object
     * @return void
     */
    public function onUserNavRender(\stdClass $object)
    {
        $request = request();
        $path = $request ? $request->path() : '';
        $categories = Category::getCategoryTree();

        $navs = [];
        foreach ($categories as $category) {
            $navs[] = $this->buildMenuItem($category, $path);
        }
        // dump($navs);
        $object->navs = $navs;
    }

    private function buildMenuItem($category, $path)
    {
        $item = [
            'name' => $category->name,
            'url' => '/app/acms/category/' . $category->id,
            'class' => ($path === '/app/acms/category/' . $category->id) ? 'active' : '',
        ];
        if (!empty($category->children)) {
            $item['items'] = [];
            foreach ($category->children as $child) {
                $item['items'][] = $this->buildMenuItem($child, $path);
            }
        }
        return $item;
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
                ['name' => '用户中心菜单1', 'url' => '/app/nat/apps', 'class' => $path === '/app/nat/apps' ? 'active' : ''],
                ['name' => '用户中心菜单2', 'url' => '/app/nat/token', 'class' => $path === '/app/nat/token' ? 'active' : ''],
            ],
        ];
    }
}
