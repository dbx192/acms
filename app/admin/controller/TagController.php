<?php

namespace plugin\acms\app\admin\controller;

use plugin\acms\app\model\Tag;
use support\Request;
use support\Response;

class TagController 
{
    /**
     * 标签列表页
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($request->expectsJson()) {
            return $this->getList($request);
        }
        return view('tag/index');
    }
    
    /**
     * 获取标签列表数据
     * @param Request $request
     * @return Response
     */
    public function getList(Request $request): Response
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $name = $request->input('name', '');
        
        $query = Tag::orderBy('sort', 'asc')->orderBy('id', 'desc');
        
        if (!empty($name)) {
            $query->where('name', 'like', "%$name%");
        }
        
        $count = $query->count();
        $list = $query->offset(($page - 1) * $limit)->limit($limit)->get();
        
        // 添加文章计数
        foreach ($list as $tag) {
            $tag->article_count = $tag->articles()->count();
        }
        
        return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '获取成功']);
    }
    
    /**
     * 添加标签页面
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        return view('tag/edit');
    }
    
    /**
     * 编辑标签页
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return json(['code' => 1, 'msg' => '标签不存在']);
        }
        
        return view('tag/edit', ['tag' => $tag]);
    }
    
    /**
     * 保存标签
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $id = $request->input('id', 0);
        $data = $request->post();
        
        if (empty($data['name'])) {
            return json(['code' => 1, 'msg' => '标签名称不能为空']);
        }
        
        // 如果没有填写别名，自动生成
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        if ($id > 0) {
            $tag = Tag::find($id);
            if (!$tag) {
                return json(['code' => 1, 'msg' => '标签不存在']);
            }
            
            $tag->fill($data);
            $tag->save();
            
            return json(['code' => 0, 'msg' => '更新成功']);
        }

        Tag::create($data);
        return json(['code' => 0, 'msg' => '添加成功']);
    }
    
    /**
     * 更新标签
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $data = $request->post();
        
        if (empty($data['name'])) {
            return json(['code' => 1, 'msg' => '标签名称不能为空']);
        }
        
        $tag = Tag::find($id);
        if (!$tag) {
            return json(['code' => 1, 'msg' => '标签不存在']);
        }
        
        // 如果没有填写别名，自动生成
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        $tag->fill($data);
        $tag->save();
        
        return json(['code' => 0, 'msg' => '更新成功']);
    }
    
    /**
     * 更改标签状态
     * @param Request $request
     * @return Response
     */
    public function status(Request $request): Response
    {
        $id = $request->input('id', 0);
        $status = $request->input('status', 0);
        
        if (empty($id)) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }
        
        $tag = Tag::find($id);
        if (!$tag) {
            return json(['code' => 1, 'msg' => '标签不存在']);
        }
        
        $tag->status = $status;
        $tag->save();
        
        return json(['code' => 0, 'msg' => '操作成功']);
    }
    
    /**
     * 更改标签状态
     * @param Request $request
     * @return Response
     */
    public function changeStatus(Request $request): Response
    {
        return $this->status($request);
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
        // 检查是否有关联文章
        $usedTags = Tag::whereIn('id', $idsArr)
            ->whereHas('articles')
            ->pluck('name')
            ->toArray();
        if (!empty($usedTags)) {
            return json([
                'code' => 1,
                'msg' => '标签 [' . implode(',', $usedTags) . '] 已被关联，不能删除'
            ]);
        }
        $count = Tag::whereIn('id', $idsArr)->delete();
        return json(['code' => 0, 'msg' => '删除成功', 'count' => $count]);
    }
    
    /**
     * 批量删除标签
     * @param Request $request
     * @return Response
     */
    public function batchRemove(Request $request): Response
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }
        
        // 检查是否有标签已被关联
        $usedTags = Tag::whereIn('id', $ids)
            ->whereHas('articles')
            ->pluck('name')
            ->toArray();
        
        if (!empty($usedTags)) {
            return json([
                'code' => 1, 
                'msg' => '标签 [' . implode(',', $usedTags) . '] 已被关联，不能删除'
            ]);
        }
        
        Tag::whereIn('id', $ids)->delete();
        
        return json(['code' => 0, 'msg' => '批量删除成功']);
    }
    
    /**
     * 根据名称生成标签别名
     * @param string $name
     * @return string
     */
    private function generateSlug(string $name): string
    {
        // 转为小写并移除特殊字符
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        
        // 检查是否已存在相同slug
        $original = $slug;
        $count = 1;
        
        while (Tag::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }
        
        return $slug;
    }
}