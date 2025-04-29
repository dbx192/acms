<?php

namespace plugin\acms\app\model;

use plugin\user\app\model\User;
use support\Model;

class Comment extends Model
{
    protected $table = 'acms_comments';
    
    protected $fillable = [
        'content', 'user_id', 'article_id', 'parent_id', 'status'
    ];
    
    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // 关联文章
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    
    // 关联父评论
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    
    // 关联子评论
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
