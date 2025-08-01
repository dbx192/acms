<?php

namespace plugin\acms\app\model;


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
class Category extends BaseModel
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
} 