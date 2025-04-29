<?php

namespace plugin\acms\app\admin\controller;

use plugin\acms\app\model\Article;
use plugin\acms\app\model\Category;
use plugin\acms\app\model\Tag;
use plugin\user\app\model\User;
use support\Request;
use support\Response;

// 正确路径，根据实际情况调整

class ArticleController
{
    /**
     * 文章列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($request->expectsJson()) {
            return $this->getList($request);
        }
        $categories = Category::orderBy('sort', 'desc')->select(['id', 'name'])->get();
        $tags = Tag::orderBy('sort', 'desc')->select(['id', 'name'])->get();
        $authors = User::orderBy('id', 'desc')->select(['id', 'username'])->get();
        return view('article/index', [
            'categories' => $categories,
            'tags' => $tags,
            'authors' => $authors
        ]);
    }

    /**
     * 添加文章页面
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        // 获取所有分类和标签
        $categories = Category::where('status', 1)->orderBy('sort', 'desc')->get();
        $tags = Tag::where('status', 1)->orderBy('sort', 'desc')->get();

        return view('article/add', [
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    /**
     * 保存文章
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $data = $request->post();

        // 验证必填字段
        if (empty($data['title'])) {
            return json(['code' => 1, 'msg' => '标题不能为空']);
        }

        if (empty($data['content'])) {
            return json(['code' => 1, 'msg' => '内容不能为空']);
        }

        if (empty($data['category_id'])) {
            return json(['code' => 1, 'msg' => '请选择分类']);
        }

        // 生成摘要（如果没有提供）
        if (empty($data['summary'])) {
            $data['summary'] = mb_substr(strip_tags($data['content']), 0, 200);
        }

        // 处理默认值
        $data['user_id'] = admin_id();
        $data['views'] = $data['views'] ?? 0;
        $data['likes'] = $data['likes'] ?? 0;
        $data['is_top'] = $data['is_top'] ?? 0;
        $data['is_recommend'] = $data['is_recommend'] ?? 0;
        $data['status'] = $data['status'] ?? 1;
        $data['sort'] = $data['sort'] ?? 0;
        $data['seo_title'] = $data['seo_title'] ?? '';
        $data['seo_keywords'] = $data['seo_keywords'] ?? '';
        $data['seo_description'] = $data['seo_description'] ?? '';
        $data['type'] = $data['type'] ?? 1; // 默认是Markdown格式

        // 安全处理内容，防止过长或有特殊字符
        $data['summary'] = mb_substr($data['summary'], 0, 500);

        try {
            // 保存文章
            $article = new Article();
            foreach ($data as $key => $value) {
                if (in_array($key, [
                    'title',
                    'content',
                    'summary',
                    'thumb',
                    'category_id',
                    'tags',
                    'views',
                    'likes',
                    'is_top',
                    'is_recommend',
                    'status',
                    'sort',
                    'seo_title',
                    'seo_keywords',
                    'seo_description',
                    'user_id',
                    'type'
                ])) {
                    $article->$key = $value;
                }
            }

            $article->save();

            // 处理标签关联关系
            if (!empty($data['tags'])) {
                $tagIds = explode(',', $data['tags']);
                $article->tags()->sync($tagIds);
            }

            return json(['code' => 0, 'msg' => '添加成功', 'data' => $article]);
        } catch (\Exception $e) {
            return json(['code' => 1, 'msg' => '添加失败: ' . $e->getMessage()]);
        }
    }

    /**
     * 编辑文章页面
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $article = Article::find($id);
        if (!$article) {
            return redirect('/app/admin/acms/article/index');
        }

        // 获取所有分类和标签
        $categories = Category::where('status', 1)->orderBy('sort', 'desc')->get();
        $tags = Tag::where('status', 1)->orderBy('sort', 'desc')->get();

        // 获取文章关联的标签ID
        $articleTags = $article->tags()->pluck('acms_tags.id')->toArray();

        return view('article/edit', [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'articleTags' => $articleTags
        ]);
    }

    /**
     * 更新文章
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $article = Article::find($id);
        if (!$article) {
            return json(['code' => 1, 'msg' => '文章不存在']);
        }

        $data = $request->post();

        // 验证必填字段
        if (empty($data['title'])) {
            return json(['code' => 1, 'msg' => '标题不能为空']);
        }

        if (empty($data['content'])) {
            return json(['code' => 1, 'msg' => '内容不能为空']);
        }

        if (empty($data['category_id'])) {
            return json(['code' => 1, 'msg' => '请选择分类']);
        }

        // 生成摘要（如果没有提供）
        if (empty($data['summary'])) {
            $data['summary'] = mb_substr(strip_tags($data['content']), 0, 200);
        }

        // 更新文章
        foreach ($data as $key => $value) {
            if (in_array($key, [
                'title',
                'content',
                'summary',
                'thumb',
                'category_id',
                'tags',
                'views',
                'likes',
                'is_top',
                'is_recommend',
                'status',
                'sort',
                'seo_title',
                'seo_keywords',
                'seo_description',
                'type'
            ])) {
                $article->$key = $value;
            }
        }

        $article->save();

        // 处理标签关联关系
        if (isset($data['tags'])) {
            $tagIds = !empty($data['tags']) ? explode(',', $data['tags']) : [];
            $article->tags()->sync($tagIds);
        }

        return json(['code' => 0, 'msg' => '更新成功']);
    }

    /**
     * 删除（支持单个和批量）
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $ids = $request->post('id');
        if (!$ids) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }
        $idsArr = is_array($ids) ? $ids : explode(',', $ids);
        $count = Article::whereIn('id', $idsArr)->delete();
        return json(['code' => 0, 'msg' => '删除成功', 'count' => $count]);
    }

    /**
     * 修改文章状态
     * @param Request $request
     * @return Response
     */
    public function changeStatus(Request $request): Response
    {
        $id = $request->post('id');
        $field = $request->post('field', 'status');
        $value = $request->post('value', 0);

        $article = Article::find($id);
        if (!$article) {
            return json(['code' => 1, 'msg' => '文章不存在']);
        }

        // 只允许修改特定字段
        if (!in_array($field, ['status', 'is_top', 'is_recommend'])) {
            return json(['code' => 1, 'msg' => '不允许的操作']);
        }

        $article->$field = $value;
        $article->save();

        return json(['code' => 0, 'msg' => '操作成功']);
    }

    /**
     * 获取文章列表数据
     * @param Request $request
     * @return Response
     */
    public function getList(Request $request): Response
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $keyword = $request->get('keyword', '');
        $categoryId = $request->get('category_id', '');
        $tagId = $request->get('tag_id', '');
        $authorId = $request->get('author_id', '');
        $export = $request->get('export', 0);
        $query = Article::query();
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($tagId) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('acms_tags.id', $tagId);
            });
        }
        if ($authorId) {
            $query->where('user_id', $authorId);
        }
        $articles = $query->select(['id', 'title', 'category_id', 'user_id', 'is_top', 'is_recommend', 'status', 'views', 'created_at'])
            ->with([
                'category:id,name',
                'tags:id,name',
                'user:id,username'
            ])
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
        $articlesData = [];
        foreach ($articles->items() as $article) {
            $articleData = $article->toArray();
            $articleData['category_name'] = $article->category ? $article->category->name : '未分类';
            $articleData['is_top_text'] = $article->is_top ? '是' : '否';
            $articleData['is_recommend_text'] = $article->is_recommend ? '是' : '否';
            $articleData['status_text'] = $article->status ? '显示' : '隐藏';
            $articleData['url'] = "/app/acms/article/{$article->id}";
            $articleData['tags'] = [];
            if ($article->tags) {
                $articleData['tags'] = $article->tags->map(function ($tag) {
                    return ['id' => $tag->id, 'name' => $tag->name];
                })->toArray();
            }
            $articleData['author_name'] = $article->user ? $article->user->username : '';
            $articlesData[] = $articleData;
        }
        $categories = Category::orderBy('sort', 'desc')->get();
        $tags = Tag::orderBy('sort', 'desc')->get();
        $authors = User::orderBy('id', 'desc')->get();
        return json([
            'code' => 0,
            'msg' => 'success',
            'count' => $articles->total(),
            'data' => $articlesData,
            'categories' => $categories,
            'tags' => $tags,
            'authors' => $authors
        ]);
    }
}
