@extends('layouts/app', ['plugin' => 'acms'])

@section('title', $tag->name . ' - 标签文章')

@section('content')
<h3 class="mb-4">标签：{{ $tag->name }}</h3>

@if(count($articles) > 0)
    @foreach($articles as $article)
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">
                <a href="/app/acms/article/{{ $article->id }}" class="text-decoration-none text-dark">{{ $article->title }}</a>
                @if($article->is_top)
                <span class="badge bg-danger">置顶</span>
                @endif
            </h5>
            <div class="article-meta mb-2">
                <span><i class="far fa-clock"></i> {{ date('Y-m-d H:i', strtotime($article->created_at)) }}</span>
                <span class="ms-3"><i class="far fa-eye"></i> {{ $article->views }} 阅读</span>
                <span class="ms-3"><i class="far fa-folder"></i> <a href="/app/acms/category/{{ $article->category->id }}" class="text-decoration-none">{{ $article->category->name }}</a></span>
            </div>
            <p class="card-text">{{ \Illuminate\Support\Str::limit(strip_tags($article->description ?? ''), 200) }}</p>
            <a href="/app/acms/article/{{ $article->id }}" class="btn btn-sm btn-primary">阅读全文</a>
        </div>
    </div>
    @endforeach
@else
    <div class="alert alert-info">该标签下暂无文章</div>
@endif

<!-- 分页 -->
<!-- 分页 -->
<div class="d-flex justify-content-center">
    {!! $paginator !!}
</div>
@endsection

@section('sidebar')
<!-- 当前标签突出显示 -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">当前标签</h5>
    </div>
    <div class="card-body">
        <div class="tag-cloud">
            <span class="tag bg-primary text-white">{{ $tag->name }}</span>
        </div>
    </div>
</div>

@parent
@endsection 