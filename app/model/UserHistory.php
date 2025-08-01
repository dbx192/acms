<?php

namespace plugin\acms\app\model;

use plugin\user\app\model\User;

class UserHistory extends BaseModel
{
    protected $table = 'acms_user_histories';
    
    protected $fillable = ['user_id', 'article_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}