<?php

namespace plugin\acms\api;

use plugin\acms\app\model\Category as CategoryModel;

class Category
{
    /**
     * 获取分类列表
     * @param array $where 条件
     * @param int $limit 数量
     * @param string $orderBy 排序
     * @param string $sort 排序方式
     * @return array
     */
    public static function getList($where = [], $limit = 0, $orderBy = 'sort', $sort = 'desc')
    {
        $query = CategoryModel::where('status', 1);
        
        // 添加查询条件
        if (isset($where['parent_id'])) {
            $query->where('parent_id', $where['parent_id']);
        }
        
        if (isset($where['id'])) {
            $query->where('id', $where['id']);
        }
        
        // 设置排序
        $query->orderBy($orderBy, $sort);
        
        // 如果设置了数量限制
        if ($limit > 0) {
            $query->limit($limit);
        }
        
        return $query->get()->toArray();
    }
    
    /**
     * 获取分类树
     * @param int $parentId 父级ID
     * @return array
     */
    public static function getCategoryTree($parentId = 0)
    {
        $categories = CategoryModel::where('status', 1)
            ->orderBy('sort', 'desc')
            ->get();
            
        return self::buildCategoryTree($categories, $parentId);
    }
    
    /**
     * 构建分类树
     * @param $categories object 分类列表
     * @param int $parentId 父级ID
     * @return array
     */
    private static function buildCategoryTree($categories, $parentId = 0)
    {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = self::buildCategoryTree($categories, $category->id);
                if ($children) {
                    $category->children = $children;
                }
                
                $tree[] = $category;
            }
        }
        
        return $tree;
    }
    
    /**
     * 获取分类详情
     * @param int $id 分类ID
     * @return array|null
     */
    public static function getDetail($id)
    {
        $category = CategoryModel::where('id', $id)
            ->where('status', 1)
            ->first();
            
        if (!$category) {
            return null;
        }
        
        return $category->toArray();
    }
    
    /**
     * 获取父级分类路径
     * @param int $categoryId 当前分类ID
     * @return array
     */
    public static function getParentPath($categoryId)
    {
        $path = [];
        $category = CategoryModel::find($categoryId);
        
        if (!$category) {
            return $path;
        }
        
        $path[] = $category->toArray();
        
        if ($category->parent_id > 0) {
            $parentPath = self::getParentPath($category->parent_id);
            $path = array_merge($parentPath, $path);
        }
        
        return $path;
    }
} 