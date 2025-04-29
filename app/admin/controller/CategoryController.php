<?php

namespace plugin\acms\app\admin\controller;

use plugin\acms\app\model\Category;
use support\Request;
use support\Response;

class CategoryController
{
    /**
     * 分类列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($request->expectsJson()) {
            $keyword = $request->get('keyword', '');
            $query = Category::query();
            if ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            }
            // 只查需要的字段
            $categories = $query->orderBy('sort', 'desc')->select(['id', 'parent_id', 'name', 'slug', 'sort', 'status', 'created_at'])->get();
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $categories
            ]);
        }

        // 获取所有分类
        $categories = Category::orderBy('sort', 'desc')->get();

        return view('category/index', [
            'categories' => $categories
        ]);
    }

    /**
     * 添加分类页面
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        // 获取所有分类供选择父级
        $categories = Category::orderBy('sort', 'desc')->get();

        return view('category/add', [
            'categories' => $categories
        ]);
    }

    /**
     * 保存分类
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $data = $request->post();

        // 验证必填字段
        if (empty($data['name'])) {
            return json(['code' => 1, 'msg' => '分类名称不能为空']);
        }

        // 处理别名（如果没有提供）
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // 处理默认值
        $data['parent_id'] = $data['parent_id'] ?? 0;
        $data['status'] = $data['status'] ?? 1;
        $data['sort'] = $data['sort'] ?? 0;

        // 保存分类
        $category = new Category();
        foreach ($data as $key => $value) {
            if (in_array($key, [
                'name',
                'slug',
                'parent_id',
                'description',
                'thumb',
                'status',
                'sort',
                'seo_title',
                'seo_keywords',
                'seo_description'
            ])) {
                $category->$key = $value;
            }
        }

        $category->save();

        return json(['code' => 0, 'msg' => '添加成功', 'data' => $category]);
    }

    /**
     * 编辑分类页面
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect('/app/admin/acms/category/index');
        }

        // 获取所有分类供选择父级
        $categories = Category::where('id', '<>', $id)
            ->orderBy('sort', 'desc')
            ->get();

        return view('category/edit', [
            'category' => $category,
            'categories' => $categories
        ]);
    }

    /**
     * 更新分类
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $category = Category::find($id);
        if (!$category) {
            return json(['code' => 1, 'msg' => '分类不存在']);
        }

        $data = $request->post();

        // 验证必填字段
        if (empty($data['name'])) {
            return json(['code' => 1, 'msg' => '分类名称不能为空']);
        }

        // 不能将自己设为自己的父级
        if (isset($data['parent_id']) && $data['parent_id'] == $id) {
            return json(['code' => 1, 'msg' => '不能将自己设为自己的父级']);
        }

        // 处理别名（如果没有提供）
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // 更新分类
        foreach ($data as $key => $value) {
            if (in_array($key, [
                'name',
                'slug',
                'parent_id',
                'description',
                'thumb',
                'status',
                'sort',
                'seo_title',
                'seo_keywords',
                'seo_description'
            ])) {
                $category->$key = $value;
            }
        }

        $category->save();

        return json(['code' => 0, 'msg' => '更新成功']);
    }

    /**
     * 删除（支持单个和批量）
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $ids = $request->post('id');
        if (!$ids) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }
        $idsArr = is_array($ids) ? $ids : explode(',', $ids);
        // 检查是否有子分类或文章
        foreach ($idsArr as $id) {
            $category = Category::find($id);
            if (!$category) continue;
            if (Category::where('parent_id', $id)->count() > 0) {
                return json(['code' => 1, 'msg' => '存在子分类，不能批量删除']);
            }
            if ($category->articles()->count() > 0) {
                return json(['code' => 1, 'msg' => '存在分类下有文章，不能批量删除']);
            }
        }
        $count = Category::whereIn('id', $idsArr)->delete();
        return json(['code' => 0, 'msg' => '删除成功', 'count' => $count]);
    }

    /**
     * 修改分类状态
     * @param Request $request
     * @return Response
     */
    public function changeStatus(Request $request): Response
    {
        $id = $request->post('id');
        $status = $request->post('status', 0);

        $category = Category::find($id);
        if (!$category) {
            return json(['code' => 1, 'msg' => '分类不存在']);
        }

        $category->status = $status;
        $category->save();

        return json(['code' => 0, 'msg' => '操作成功']);
    }

    /**
     * 构建分类树
     * @param $categories
     * @param int $parentId
     * @return array
     */
    private function buildCategoryTree($categories, $parentId = 0)
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildCategoryTree($categories, $category->id);
                if ($children) {
                    $category->children = $children;
                }

                $tree[] = $category;
            }
        }

        return $tree;
    }

    /**
     * 生成别名
     * @param string $name
     * @return string
     */
    private function generateSlug(string $name): string
    {
        $slug = preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', strtolower($name)));

        // 检查别名是否已存在
        $count = Category::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-' . time();
        }

        return $slug;
    }
}
