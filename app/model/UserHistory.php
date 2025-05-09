<?php

namespace plugin\acms\app\model;

use support\Model;
use plugin\user\app\model\User;

class UserHistory extends Model
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