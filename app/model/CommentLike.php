<?php

namespace plugin\acms\app\model;

use support\Model;
use plugin\user\app\model\User;


class CommentLike extends Model
{
    protected $table = 'acms_comment_likes';

    protected $fillable = ['user_id', 'comment_id', 'article_id'];

    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联评论
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
