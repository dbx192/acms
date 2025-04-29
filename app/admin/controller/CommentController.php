<?php

namespace plugin\acms\app\admin\controller;

use plugin\acms\app\model\Comment;
use support\Request;
use support\Response;

class CommentController
{
    public function index(Request $request): Response
    {
        if ($request->expectsJson()) {
            return $this->getList($request);
        }
        // 获取所有分类
        $comments = Comment::orderBy('id', 'desc')->get();

        return view('comment/index', [
            'comments' => $comments
        ]);
    }

    public function getList(Request $request): Response
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $article_id = $request->input('article_id', 0);
        $user_id = $request->input('user_id', 0);
        $keyword = $request->input('keyword', '');
        $export = $request->input('export', 0);

        $query = Comment::with(['user', 'article'])
            ->orderBy('id', 'desc');

        if ($article_id > 0) {
            $query->where('article_id', $article_id);
        }
        if ($user_id > 0) {
            $query->where('user_id', $user_id);
        }
        if ($keyword) {
            $query->where('content', 'like', "%{$keyword}%");
        }

        $count = $query->count();

        if ($export) {
            $list = $query->get();
            return json(['code' => 0, 'data' => $list, 'title' => '评论列表']);
        }

        $list = $query->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();
        return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '获取成功']);
    }

    public function create(Request $request): Response
    {
        return view('comment/create');
    }

    public function store(Request $request): Response
    {
        $data = $request->all();
        $comment = new Comment();
        $comment->fill($data)->save();
        return json(['code' => 0, 'msg' => '添加成功']);
    }

    public function edit(Request $request, $id): Response
    {
        $comment = Comment::find($id);
        $articles = \plugin\acms\app\model\Article::all();
        return view('comment/edit', ['comment' => $comment, 'articles' => $articles]);
    }

    public function update(Request $request, $id): Response
    {
        $comment = Comment::find($id);
        $comment->fill($request->all())->save();
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
        $count = Comment::whereIn('id', $idsArr)->delete();
        return json(['code' => 0, 'msg' => '删除成功', 'count' => $count]);
    }

    public function audit(Request $request): Response
    {
        $id = $request->input('id');
        $status = $request->input('status', 1);
        $comment = Comment::find($id);
        if (!$comment) {
            return json(['code' => 1, 'msg' => '评论不存在']);
        }
        $comment->status = $status;
        $comment->save();
        return json(['code' => 0, 'msg' => '操作成功']);
    }
}
