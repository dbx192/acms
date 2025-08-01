<?php

namespace plugin\acms\app\model;


/**
 * @property integer $id 主键(主键)
 * @property string $title 文章标题
 * @property string $content 文章内容
 * @property string $summary 文章摘要
 * @property string $thumb 缩略图
 * @property integer $category_id 分类ID
 * @property string $tags 标签
 * @property integer $views 浏览量
 * @property integer $likes 点赞数
 * @property integer $is_top 是否置顶
 * @property integer $is_recommend 是否推荐
 * @property integer $status 状态 0隐藏 1显示
 * @property integer $sort 排序值
 * @property string $seo_title SEO标题
 * @property string $seo_keywords SEO关键词
 * @property string $seo_description SEO描述
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property integer $user_id 发布者ID
 * @property integer $type 文章类型 1.markdown，2.html
 */
class Article extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acms_articles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 与文章关联的分类
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * 与文章关联的标签
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'acms_article_tag', 'article_id', 'tag_id');
    }

    /**
     * 与文章关联的用户
     */
    public function user()
    {
        return $this->belongsTo('plugin\user\app\model\User', 'user_id', 'id');
    }
} 