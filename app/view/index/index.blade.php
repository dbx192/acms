@extends('layouts/app', ['plugin' => 'acms'])

@section('title', 'CMS系统 - 内容管理系统')

@section('content')
<!-- 文章列表 -->
<h3 class="mb-4">最新文章</h3>

@if(count($articles) > 0)
    @foreach($articles as $article)
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            @if($article->is_top)
            <span class="badge bg-danger mb-2">置顶</span>
            @endif
            
            @if($article->is_recommend)
            <span class="badge bg-success mb-2">推荐</span>
            @endif
            
            <h5 class="card-title">
                <a href="/app/acms/article/{{$article->id}}" class="text-decoration-none text-dark">{{$article->title}}</a>
            </h5>
            <p class="card-text">{{$article->summary}}</p>
            <div class="article-meta">
                <span><i class="far fa-folder"></i> {{$article->category->name ?? '未分类'}}</span>
                <span class="ms-3"><i class="far fa-clock"></i> {{date('Y-m-d', strtotime($article->created_at))}}</span>
                <span class="ms-3"><i class="far fa-eye"></i> {{$article->views}} 阅读</span>
            </div>
        </div>
    </div>
    @endforeach
    
    <!-- 分页 -->
    <div class="d-flex justify-content-center">
        {!! $paginator !!}
    </div>
@else
    <div class="alert alert-info">暂无文章</div>
@endif
@endsection

@section('sidebar')
    <!-- 推荐文章 -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">推荐文章</h5>
        </div>
        <div class="card-body">
            @if(count($recommendArticles) > 0)
                <ul class="list-unstyled">
                    @foreach($recommendArticles as $article)
                    <li class="sidebar-item">
                        <a href="/app/acms/article/{{$article->id}}" class="text-decoration-none text-dark">{{$article->title}}</a>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">暂无推荐文章</p>
            @endif
        </div>
    </div>
    
    <!-- 调用父模板的侧边栏内容 -->
    @parent
@endsection 