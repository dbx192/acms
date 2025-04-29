<?php

namespace plugin\acms\app\controller;

use plugin\acms\app\model\Article;
use plugin\acms\app\model\Category;
use plugin\acms\app\model\Tag;
use support\Request;
use support\Response;
use JasonGrimes\Paginator;

class IndexController
{
    protected $noNeedLogin = '*';

    /**
     * 首页
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        $page = $request->get('page', 1);
        $limit = 10;

        // 获取文章列表，按创建时间倒序排列
        $articles = Article::where('status', 1)
            ->orderBy('is_top', 'desc')
            ->orderBy('created_at', 'desc')
            ->with('category')
            ->paginate($limit, ['*'], 'page', $page);

        // 创建分页器
        $paginator = new Paginator(
            $articles->total(),
            $limit,
            $page,
            '/app/acms?page=(:num)'
        );

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

        // 获取推荐文章
        $recommendArticles = Article::where('status', 1)
            ->where('is_recommend', 1)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('index/index', [
            'articles' => $articles,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'tags' => $tags,
            'recommendArticles' => $recommendArticles,
            'paginator' => $paginator
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

        if (!$article) {
            return redirect('/app/acms');
        }

        // 更新浏览量
        $article->increment('views');

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

        // 获取文章评论
        $comments = \plugin\acms\app\model\Comment::where('article_id', $id)
            ->where('status', 1)
            ->where('parent_id', 0)
            ->with(['user', 'replies'])
            ->orderBy('id', 'desc')
            ->get();

        return view('index/detail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'tags' => $tags,
            'comments' => $comments
        ]);
    }

    /**
     * 分类页面
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function category(Request $request, int $id): Response
    {
        $page = $request->get('page', 1);
        $limit = 10;

        // 获取分类信息
        $category = Category::find($id);
        if (!$category || $category->status != 1) {
            return redirect('/app/acms');
        }

        // 获取该分类下的文章
        $articles = Article::where('status', 1)
            ->where('category_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        // 创建分页器
        $paginator = new Paginator(
            $articles->total(),
            $limit,
            $page,
            "/app/acms/category/{$id}?page=(:num)"
        );

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

        return view('index/category', [
            'category' => $category,
            'articles' => $articles,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'tags' => $tags,
            'paginator' => $paginator
        ]);
    }

    /**
     * 标签页面
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function tag(Request $request, int $id): Response
    {
        $page = $request->get('page', 1);
        $limit = 10;

        // 获取标签信息
        $tag = Tag::find($id);
        if (!$tag || $tag->status != 1) {
            return redirect('/app/acms');
        }

        // 获取该标签下的文章
        $articles = $tag->articles()
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        // 创建分页器
        $paginator = new Paginator(
            $articles->total(),
            $limit,
            $page,
            "/app/acms/tag/{$id}?page=(:num)"
        );

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

        return view('index/tag', [
            'tag' => $tag,
            'articles' => $articles,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'tags' => $tags,
            'paginator' => $paginator
        ]);
    }

    /**
     * 搜索页面
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 1);
        $limit = 10;

        // 搜索文章
        $articles = Article::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhere('summary', 'like', "%{$keyword}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        // 创建分页器
        $paginator = new Paginator(
            $articles->total(),
            $limit,
            $page,
            "/app/acms/search?keyword={$keyword}&page=(:num)"
        );

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

        return view('index/search', [
            'keyword' => $keyword,
            'articles' => $articles,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'tags' => $tags,
            'paginator' => $paginator
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
