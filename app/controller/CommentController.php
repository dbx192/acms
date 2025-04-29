<?php

namespace plugin\acms\app\controller;

use plugin\acms\app\model\Comment;
use support\Request;
use support\Response;

class CommentController
{
    /**
     * 评论列表页
     */
    public function index(Request $request): Response
    {
        if ($request->expectsJson()) {
            return $this->getList($request);
        }
        return view('admin/comment/index');
    }
    
    /**
     * 获取评论列表
     */
    public function getList(Request $request): Response
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $article_id = $request->input('article_id', 0);
        
        $query = Comment::with(['user', 'article', 'replies'])
            ->where('parent_id', 0)
            ->orderBy('created_at', 'desc');
            
        if ($article_id > 0) {
            $query->where('article_id', $article_id);
        }
        
        $count = $query->count();
        $list = $query->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();
            
        return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '获取成功']);
    }
    
    /**
     * 添加评论
     */
    public function add(Request $request): Response
    {
        $data = $request->post();
        $data['user_id'] = session('user.id') ?? 0;
        if (empty($data['user_id'])) {
            return json(['code' => 1, 'msg' => '用户未登录']);
        }
        
        if (empty($data['content'])) {
            return json(['code' => 1, 'msg' => '评论内容不能为空']);
        }
        
        Comment::create($data);
        
        return json(['code' => 0, 'msg' => '评论添加成功']);
    }
    
    /**
     * 回复评论
     */
    public function reply(Request $request): Response
    {
        $data = $request->post();
        $data['user_id'] = $request->userId;
        
        if (empty($data['content'])) {
            return json(['code' => 1, 'msg' => '回复内容不能为空']);
        }
        
        if (empty($data['parent_id'])) {
            return json(['code' => 1, 'msg' => '回复的评论不存在']);
        }
        
        Comment::create($data);
        
        return json(['code' => 0, 'msg' => '回复成功']);
    }
    
    /**
     * 删除评论
     */
    public function delete(Request $request, int $id): Response
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return json(['code' => 1, 'msg' => '评论不存在']);
        }
        
        // 删除子评论
        Comment::where('parent_id', $id)->delete();
        $comment->delete();
        
        return json(['code' => 0, 'msg' => '删除成功']);
    }
}
