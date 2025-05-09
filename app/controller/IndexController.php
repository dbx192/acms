<?php

namespace plugin\acms\app\controller;

use plugin\acms\app\model\Article;
use plugin\acms\app\model\Category;
use plugin\acms\app\model\Comment;
use plugin\acms\app\model\UserHistory;
use plugin\acms\app\model\Tag;
use support\Request;
use support\Response;
use JasonGrimes\Paginator;

class IndexController
{
    protected $noNeedLogin = '*';

    /**
     * 通用文章列表（全部通过参数控制）
     * 支持参数：category_id, tag_id, keyword, is_top, is_recommend, status, page, limit
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        $categoryId = $request->get('category_id');
        $tagId = $request->get('tag_id');
        $keyword = $request->get('keyword', '');
        $isTop = $request->get('is_top');
        $isRecommend = $request->get('is_recommend');
        $status = $request->get('status', 1);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $query = Article::query();

        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($tagId) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('acms_tags.id', $tagId);
            });
        }
        if ($isTop !== null && $isTop !== '') {
            $query->where('is_top', $isTop);
        }
        if ($isRecommend !== null && $isRecommend !== '') {
            $query->where('is_recommend', $isRecommend);
        }
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhere('summary', 'like', "%{$keyword}%");
            });
        }

        $articles = $query->orderBy('is_top', 'desc')
            ->orderBy('created_at', 'desc')
            ->with('category')
            ->paginate($limit, ['*'], 'page', $page);

        // 拼接分页url，保留所有参数
        $params = $request->get();
        $params['page'] = '(:num)';
        $pageUrl = '/app/acms/list?' . http_build_query($params);

        $paginator = new \JasonGrimes\Paginator(
            $articles->total(),
            $limit,
            $page,
            $pageUrl
        );

        $categories = Category::where('status', 1)
            ->orderBy('sort', 'desc')
            ->withCount('articles')
            ->get();
        $categoryTree = $this->buildCategoryTree($categories);

        $tags = Tag::where('status', 1)
            ->orderBy('sort', 'desc')
            ->withCount('articles')
            ->get();

        // 页面标题和侧边栏提示自动化
        $title = '文章列表';
        $sidebarTitle = '筛选条件';
        $sidebarContent = [];
        if ($categoryId) {
            $category = $categories->where('id', $categoryId)->first();
            $title = ($category ? $category->name : '分类') . ' - 文章列表';
            $sidebarContent[] = '分类：' . ($category ? $category->name : $categoryId);
        }
        if ($tagId) {
            $tag = $tags->where('id', $tagId)->first();
            $title = ($tag ? $tag->name : '标签') . ' - 文章列表';
            $sidebarContent[] = '标签：' . ($tag ? $tag->name : $tagId);
        }
        if ($keyword) {
            $title = '搜索：' . $keyword . ' - 文章列表';
            $sidebarContent[] = '关键词：' . $keyword;
        }
        if ($isTop !== null && $isTop !== '') {
            $sidebarContent[] = '置顶：' . ($isTop ? '是' : '否');
        }
        if ($isRecommend !== null && $isRecommend !== '') {
            $sidebarContent[] = '推荐：' . ($isRecommend ? '是' : '否');
        }

        return view('index/list', [
            'title' => $title,
            'articles' => $articles,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'tags' => $tags,
            'paginator' => $paginator,
            'sidebarTitle' => $sidebarTitle,
            'sidebarContent' => $sidebarContent,
            'params' => $request->get(),
        ]);
    }

    /**
     * 文章详情
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function detail(Request $request, int $id): Response
    {
        // 获取文章详情
        $article = Article::where('id', $id)
            ->where('status', 1)
            ->with(['category', 'user', 'tags'])
            ->first();

        // 检查用户是否收藏了该文章
        $article->is_favorite = false;
        if (session('user') && class_exists('\plugin\acms\app\model\UserLike')) {
            $article->is_favorite = \plugin\acms\app\model\UserLike::where('user_id', session('user.id'))
                ->where('article_id', $id)
                ->exists();
        }

        if (!$article) {
            return redirect('/app/acms');
        }

        // 更新浏览量
        $article->increment('views');

        // 记录用户浏览历史
        if (session('user')) {
            UserHistory::updateOrCreate(
                ['user_id' => session('user.id'), 'article_id' => $id],
                ['updated_at' => date('Y-m-d H:i:s')]
            );
        }

        // 获取相关文章
        $relatedArticles = Article::where('status', 1)
            ->where('category_id', $article->category_id)
            ->where('id', '<>', $article->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 获取分类列表，并统计文章数量
        $categories = Category::where('status', 1)
            ->orderBy('sort', 'desc')
            ->withCount('articles')
            ->get();
        $categoryTree = $this->buildCategoryTree($categories);

        // 获取标签列表，并统计文章数量
        $tags = Tag::where('status', 1)
            ->orderBy('sort', 'desc')
            ->withCount('articles')
            ->get();

        // 获取文章评论（如有评论功能，可根据实际情况调整）
        $comments = Comment::where('article_id', $id)
            ->where('status', 1)
            ->where('parent_id', 0)
            ->with(['replies'])
            ->orderBy('id', 'desc')
            ->get();
        // 获取这个文章，用户点赞过的所有的评论id
        $liked_comments = \plugin\acms\app\model\CommentLike::where('user_id', session('user.id'))
            ->where('article_id', $id)
            ->pluck('comment_id')
            ->toArray();
        // dump($liked_comments);
        return view('index/detail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'tags' => $tags,
            'comments' => $comments,
            'liked_comments' => $liked_comments,
        ]);
    }

    /**
     * 构建分类树
     * @param $categories
     * @param int $parentId
     * @return array
     */
    protected function buildCategoryTree($categories, $parentId = 0)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildCategoryTree($categories, $category->id);
                $item = $category->toArray();
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
