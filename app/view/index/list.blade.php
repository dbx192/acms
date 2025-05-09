@extends('layouts/app', ['plugin' => 'acms'])

@section('title', $title)

@section('content')
<h3 class="mb-4">{{ $title }}</h3>

@if(count($articles) > 0)
    @foreach($articles as $article)
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">
                <a href="/app/acms/article/{{ $article->id }}" class="text-decoration-none text-dark">{{ $article->title }}</a>
                @if($article->is_top)
                <span class="badge bg-danger">置顶</span>
                @endif
                @if($article->is_recommend)
                <span class="badge bg-success">推荐</span>
                @endif
            </h5>
            <div class="article-meta mb-2">
                <span><i class="far fa-clock"></i> {{ date('Y-m-d H:i', strtotime($article->created_at)) }}</span>
                <span class="ms-3"><i class="far fa-eye"></i> {{ $article->views }} 阅读</span>
                <span class="ms-3"><i class="far fa-folder"></i>
                    @if($article->category)
                        <a href="/app/acms/list?category_id={{ $article->category->id }}" class="text-decoration-none">{{ $article->category->name }}</a>
                    @else
                        未分类
                    @endif
                </span>
            </div>
            <p class="card-text">{{ \Illuminate\Support\Str::limit(strip_tags($article->summary ?? $article->description ?? ''), 200) }}</p>
            <a href="/app/acms/article/{{ $article->id }}" class="btn btn-sm btn-primary">阅读全文</a>
        </div>
    </div>
    @endforeach
@else
    <div class="alert alert-info">暂无文章</div>
@endif

<!-- 分页 -->
<div class="d-flex justify-content-center">
    {!! $paginator !!}
</div>
@endsection

@section('sidebar')
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">{{ $sidebarTitle }}</h5>
    </div>
    <div class="card-body">
        @if(!empty($sidebarContent))
            <ul class="list-unstyled mb-0">
                @foreach($sidebarContent as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @else
            <span class="text-muted">无</span>
        @endif
    </div>
</div>
@parent
@endsection
