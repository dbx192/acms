@extends('layouts/app', ['plugin' => 'acms'])

@section('title', $category->name . ' - 分类文章')

@section('content')
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
    <div class="alert alert-info">该分类下暂无文章</div>
@endif

<!-- 分页 -->
<div class="d-flex justify-content-center">
    {!! $paginator !!}
</div>
@endsection 