@extends('layouts/app', ['plugin' => 'acms'])

@section('title', '搜索结果: ' . $keyword)

@section('content')
<h3 class="mb-4">搜索结果：{{ $keyword }} <small class="text-muted">共找到 {{ $articles->total() }} 篇文章</small></h3>

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
            <p class="card-text">
                @php
                    $description = strip_tags($article->description ?? '');
                    if (!empty($keyword)) {
                        $position = stripos($description, $keyword);
                        if ($position !== false) {
                            $start = max(0, $position - 50);
                            $length = min(200, strlen($description) - $start);
                            $excerpt = substr($description, $start, $length);
                            if ($start > 0) {
                                $excerpt = '...' . $excerpt;
                            }
                            if ($start + $length < strlen($description)) {
                                $excerpt .= '...';
                            }
                            $pattern = '/(' . preg_quote($keyword, '/') . ')/i';
                            $excerpt = preg_replace($pattern, '<span class="highlight">$1</span>', $excerpt);
                            echo $excerpt;
                        } else {
                            echo \Illuminate\Support\Str::limit($description, 200);
                        }
                    } else {
                        echo \Illuminate\Support\Str::limit($description, 200);
                    }
                @endphp
            </p>
            <a href="/app/acms/article/{{ $article->id }}" class="btn btn-sm btn-primary">阅读全文</a>
        </div>
    </div>
    @endforeach
@else
    <div class="alert alert-info">没有找到与"{{ $keyword }}"相关的文章</div>
@endif

<!-- 分页 -->
<div class="d-flex justify-content-center">
    {!! $paginator !!}
</div>
@endsection

@section('sidebar')
<!-- 搜索提示 -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">搜索提示</h5>
    </div>
    <div class="card-body">
        <p>您正在搜索: <strong>{{ $keyword }}</strong></p>
        <p>可以尝试更精确的关键词或查看分类导航。</p>
    </div>
</div>

@parent
@endsection 