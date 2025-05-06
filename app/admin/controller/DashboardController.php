<?php

namespace plugin\acms\app\admin\controller;

use support\Request;
use support\Response;
use plugin\acms\app\model\Article;
use plugin\acms\app\model\Comment;
use Webman\Db;

class DashboardController
{
    // 数据看板首页
    public function index(Request $request): Response
    {
        $today = date('Y-m-d');
        $total_article = Article::count();
        $total_views = Article::sum('views');
        $total_comment = Comment::count();
        $today_article = Article::whereRaw('DATE(created_at) = ?', [$today])->count();

        // 趋势数据
        $dates = [];
        $views = [];
        $comments = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i day"));
            $dates[] = date('m-d', strtotime($date));
            $views[] = Article::whereRaw('DATE(updated_at) = ?', [$date])->sum('views');
            $comments[] = Comment::whereRaw('DATE(created_at) = ?', [$date])->count();
        }

        $hot_articles = Article::orderBy('views', 'desc')->limit(10)->get(['id', 'title', 'views']);
        $hot_comments = Comment::orderBy('likes', 'desc')->limit(10)->get(['id', 'content', 'article_id', 'likes']);
        $articleIds = $hot_comments->pluck('article_id')->unique()->all();
        $articles = Article::whereIn('id', $articleIds)->pluck('title', 'id');
        foreach ($hot_comments as &$item) {
            $item['article_title'] = $articles[$item['article_id']] ?? '';
        }

        return view('dashboard/index', [
            'total_article' => $total_article,
            'total_views' => $total_views,
            'total_comment' => $total_comment,
            'today_article' => $today_article,
            'trend_dates' => $dates,
            'trend_views' => $views,
            'trend_comments' => $comments,
            'hot_articles' => $hot_articles,
            'hot_comments' => $hot_comments,
        ]);
    }
}
