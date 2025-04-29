<?php

namespace plugin\acms\app\model;

use support\Model;

/**
 * @property integer $id 主键(主键)
 * @property string $name 标签名称
 * @property string $slug 标签别名
 * @property string $description 标签描述
 * @property integer $count 文章数量
 * @property integer $status 状态 0隐藏 1显示
 * @property integer $sort 排序值
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Tag extends Model
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
    protected $table = 'acms_tags';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 可批量赋值的属性
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'count', 'status', 'sort'
    ];

    /**
     * 与标签关联的文章
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'acms_article_tag', 'tag_id', 'article_id');
    }
} 