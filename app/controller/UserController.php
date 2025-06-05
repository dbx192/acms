<?php

namespace plugin\acms\app\controller;

use support\Request;
use plugin\acms\app\model\Article;
use plugin\acms\app\model\UserLike;
use plugin\acms\app\model\CommentLike;
use plugin\acms\app\model\Comment;
use plugin\acms\app\model\UserHistory;

class UserController
{
    public function likes(Request $request)
    {
        $user = session('user');
        $page = $request->get('page', 1);
        $perPage = 10;

        $articles = UserLike::where('user_id', $user['id'])
            ->with(['article' => function ($query) {
                $query->select('id', 'title', 'created_at');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $pageUrl = '/app/acms/user/likes?page=(:num)';

        $paginator = new \JasonGrimes\Paginator(
            $articles->total(),
            $perPage,
            $page,
            $pageUrl
        );

        return view('user/like', [
            'articles' => $articles,
            'user' => $user,
            'paginator' => $paginator
        ]);
    }

    public function history(Request $request)
    {
        $user = session('user');
        $page = $request->get('page', 1);
        $perPage = 12;

        $articles = UserHistory::where('user_id', $user['id'])
            ->with(['article' => function ($query) {
                $query->select('id', 'title', 'created_at');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $pageUrl = '/app/acms/user/history?page=(:num)';

        $paginator = new \JasonGrimes\Paginator(
            $articles->total(),
            $perPage,
            $page,
            $pageUrl
        );

        return view('user/history', [
            'articles' => $articles,
            'user' => $user,
            'paginator' => $paginator
        ]);
    }

    public function commented(Request $request)
    {
        $user = session('user');
        $page = $request->get('page', 1);
        $perPage = 10;

        // 首先获取每个文章的最新评论ID
        $latestCommentIds = \plugin\acms\app\model\Comment::where('user_id', $user['id'])
            ->selectRaw('MAX(id) as latest_id, article_id')
            ->groupBy('article_id')
            ->pluck('latest_id');

        // 然后获取这些最新评论的详细信息
        $articles = \plugin\acms\app\model\Comment::whereIn('id', $latestCommentIds)
            ->where('status', 1)
            ->with(['article' => function ($query) {
                $query->select('id', 'title', 'created_at');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $pageUrl = '/app/acms/user/commented?page=(:num)';

        $paginator = new \JasonGrimes\Paginator(
            $articles->total(),
            $perPage,
            $page,
            $pageUrl
        );

        return view('user/commented', [
            'articles' => $articles,
            'user' => $user,
            'paginator' => $paginator
        ]);
    }
    // 用户like和unlike文章
    public function favorite(Request $request)
    {
        $user = session('user');
        $articleId = $request->input('article_id'); // 文章ID
        $userId = $user['id']; // 用户ID

        $like = UserLike::where('user_id', $userId)
            ->where('article_id', $articleId)
            ->first();  // 查找是否已存在

        if ($like) {
            $like->delete(); // 如果已存在，删除
        } else {
            UserLike::create([ // 如果不存在，创建
                'user_id' => $userId,
                'article_id' => $articleId,
            ]);
        }
        return json(['code' => 0, 'msg' => '操作成功']);
    }
    
    // 评论点赞
    public function likeComment(Request $request)
    {
        $user = session('user');
        $commentId = $request->input('comment_id');
        $article_id = $request->input('article_id');
        $userId = $user['id'];

        $like = CommentLike::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->first();

        $comment = Comment::find($commentId);
        
        if ($like) {
            $like->delete();
            $comment->decrement('likes');
            return json(['code' => 0, 'msg' => '取消点赞成功', 'data' => ['liked' => false, 'likes_count' => $comment->likes]]);
        }

        CommentLike::create([
            'user_id' => $userId,
            'article_id' => $article_id,
            'comment_id' => $commentId
        ]);
        $comment->increment('likes');
        return json(['code' => 0, 'msg' => '点赞成功', 'data' => ['liked' => true, 'likes_count' => $comment->likes]]);
    }
}
