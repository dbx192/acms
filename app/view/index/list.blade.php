@extends('layouts/app', ['plugin' => 'acms'])

@section('title', $title)

@section('additional-styles')
<style>
    .acms-hero {
        position: relative;
        padding: 28px 24px;
        border-radius: 16px;
        background: linear-gradient(135deg, #eef2ff 0%, #f8fbff 50%, #eaf7ff 100%);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        margin-bottom: 18px;
    }
    .acms-hero:before {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(closest-side, rgba(102,126,234,0.25), rgba(118,75,162,0));
        border-radius: 50%;
        filter: blur(2px);
    }
    .acms-hero-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2b2e4a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .acms-hero-subtitle {
        margin-top: 6px;
        color: #6b7280;
        font-size: 0.95rem;
    }
    .acms-hero-badge {
        display: inline-block;
        padding: 4px 10px;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 999px;
        font-size: 0.8rem;
        color: #6366f1;
        box-shadow: 0 2px 6px rgba(99,102,241,0.08);
    }

    .article-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.05);
        transition: transform .25s ease, box-shadow .25s ease;
        position: relative;
    }
    .article-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.08);
    }
    .article-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 6px 0;
    }
    .article-title a {
        color: #1f2937;
    }
    .article-title a:hover {
        color: #0d6efd;
    }
    .article-meta i { color: #9aa0a6; }
    .article-meta span + span { margin-left: 12px; }

    .badge-top {
        background: linear-gradient(90deg, #ff8a8a, #ff3d68);
        color: #fff;
        border: 0;
    }
    .badge-recommend {
        background: linear-gradient(90deg, #34d399, #10b981);
        color: #fff;
        border: 0;
    }

    .btn-readmore {
        border-radius: 20px;
        padding: 6px 14px;
    }
</style>
@endsection

@section('content')
<div class="acms-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <h3 class="acms-hero-title">
                {{ $title }}
                <span class="acms-hero-badge">内容精选</span>
            </h3>
            <div class="acms-hero-subtitle">本页共 {{ count($articles) }} 篇内容</div>
        </div>
    </div>
    </div>

@if(count($articles) > 0)
    @foreach($articles as $article)
    <div class="card mb-3 article-card">
        <div class="card-body">
            <h5 class="article-title">
                <a href="/app/acms/article/{{ $article->id }}" class="text-decoration-none">{{ $article->title }}</a>
                @if($article->is_top)
                <span class="badge badge-top">置顶</span>
                @endif
                @if($article->is_recommend)
                <span class="badge badge-recommend">推荐</span>
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
            <a href="/app/acms/article/{{ $article->id }}" class="btn btn-sm btn-primary btn-readmore">阅读全文</a>
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
