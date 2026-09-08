<?php

namespace plugin\acms\app\model;

use support\Model;

/**
 * @property integer $id 主键(主键)
 * @property string $name 分类名称
 * @property string $slug 分类别名
 * @property integer $parent_id 父级ID
 * @property string $description 分类描述
 * @property string $thumb 缩略图
 * @property integer $sort 排序值
 * @property integer $status 状态 0隐藏 1显示
 * @property string $seo_title SEO标题
 * @property string $seo_keywords SEO关键词
 * @property string $seo_description SEO描述
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Category extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acms_categories';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 与分类关联的文章
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id', 'id');
    }

    /**
     * 获取父级分类
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    /**
     * 获取子分类
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * 把扁平分类集合按层级整理为带深度(depth)的有序列表，用于下拉选择
     *
     * @param iterable $categories 分类集合（已按期望排序）
     * @param int $excludeId 需要排除的分类ID（编辑分类时排除自身及其所有后代，防止挂到自己下面形成环）
     * @return array [['id' => int, 'name' => string, 'depth' => int], ...]
     */
    public static function treeOptions(iterable $categories, int $excludeId = 0): array
    {
        // 按 parent_id 分组，保持集合原有排序
        $grouped = [];
        foreach ($categories as $category) {
            $grouped[(int) $category->parent_id][] = $category;
        }

        // 收集需要排除的分类及其全部后代
        $excluded = [];
        if ($excludeId) {
            $stack = [$excludeId];
            while ($stack) {
                $id = array_pop($stack);
                $excluded[$id] = true;
                foreach ($grouped[$id] ?? [] as $child) {
                    $stack[] = (int) $child->id;
                }
            }
        }

        $result = [];
        $traverse = function (int $parentId, int $depth) use (&$traverse, &$result, $grouped, $excluded) {
            foreach ($grouped[$parentId] ?? [] as $category) {
                if (isset($excluded[(int) $category->id])) {
                    continue;
                }
                $result[] = [
                    'id' => (int) $category->id,
                    'name' => $category->name,
                    'depth' => $depth,
                ];
                $traverse((int) $category->id, $depth + 1);
            }
        };
        $traverse(0, 0);

        return $result;
    }
}
