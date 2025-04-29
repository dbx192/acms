@extends('layouts/app', ['plugin' => 'acms'])

@section('title', $article->title . ' - CMS系统')

@section('additional-styles')
    .article-content {
    line-height: 1.8;
    font-size: 1.1rem;
    }
    .article-content img {
    max-width: 100%;
    height: auto;
    margin: 10px 0;
    }
    .article-content p {
    margin-bottom: 1.2rem;
    }
    .article-content h1, .article-content h2, .article-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    }
@endsection

@section('content')
    <!-- 文章详情 -->
    <div class="card p-4">
        <h1 class="mb-3">{{ $article->title }}</h1>

        <div class="article-meta">
            <span><i class="far fa-folder"></i> <a href="/app/acms/category/{{ $article->category_id }}"
                    class="text-decoration-none text-secondary">{{ $article->category->name ?? '未分类' }}</a></span>
            <span class="ms-3"><i class="far fa-user"></i> {{ $article->user->nickname ?? '管理员' }}</span>
            <span class="ms-3"><i class="far fa-clock"></i> {{ date('Y-m-d H:i', strtotime($article->created_at)) }}</span>
            <span class="ms-3"><i class="far fa-eye"></i> {{ $article->views }} 阅读</span>
        </div>

        @if (!empty($article->thumb))
            <div class="mb-4">
                <img src="{{ $article->thumb }}" class="img-fluid rounded" alt="{{ $article->title }}">
            </div>
        @endif

        <div class="article-content markdown-body">
            @if ($article->type == 1)
                <!-- Markdown内容 -->
                <div class="markdown-content" data-content="{{ $article->content }}"></div>
            @else
                <!-- HTML内容 -->
                {!! $article->content !!}
            @endif
        </div>

        <!-- 评论区域 -->
        <div class="mt-5">
            <h4 class="mb-4">评论</h4>

            @if (session('user'))
                <form id="comment-form" class="mb-4">
                    @csrf
                    <input type="hidden" name="article_id" value="{{ $article->id }}">
                    <input type="hidden" name="parent_id" id="comment_parent_id" value="0">
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" placeholder="发表你的评论..." required></textarea>
                        <div id="reply-to" class="mt-2 text-muted small" style="display:none">回复给: <span
                                id="reply-to-user"></span> <a href="#" onclick="cancelReply()">取消</a></div>
                    </div>
                    <button type="button" onclick="submitComment()" class="btn btn-primary mt-2">提交评论</button>
                    <div id="comment-result" class="mt-2"></div>
                </form>
                <script>
                    function cancelReply() {
                        document.getElementById('comment_parent_id').value = '0';
                        document.getElementById('reply-to').style.display = 'none';
                        document.getElementById('comment-form').scrollIntoView({
                            behavior: 'smooth'
                        });
                    }

                    function replyTo(commentId, nickname) {
                        document.getElementById('comment_parent_id').value = commentId;
                        document.getElementById('reply-to-user').innerText = nickname;
                        document.getElementById('reply-to').style.display = 'block';
                        document.getElementById('comment-form').scrollIntoView({
                            behavior: 'smooth'
                        });
                    }

                    function submitComment() {
                        const form = document.getElementById('comment-form');
                        const resultDiv = document.getElementById('comment-result');
                        const formData = new FormData(form);

                        fetch('/app/acms/comment/add', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.code === 0) {
                                    resultDiv.innerHTML = '<div class="alert alert-success">' + data.msg + '</div>';
                                    form.reset();
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    resultDiv.innerHTML = '<div class="alert alert-danger">' + data.msg + '</div>';
                                }
                            })
                            .catch(error => {
                                resultDiv.innerHTML = '<div class="alert alert-danger">评论提交失败，请重试</div>';
                            });
                    }
                </script>
            @else
                <div class="alert alert-info">
                    请<a href="/app/user/login">登录</a>后发表评论
                </div>
            @endif

            <div class="comment-list">
                @foreach ($comments as $comment)
                    @if ($comment->parent_id == 0)
                        @include('components.comment', ['comment' => $comment, 'level' => 0])
                    @endif
                @endforeach
            </div>
        </div>

        @if (isset($article->tags) && !empty($article->tags))
            <div class="mt-4">
                <div class="tag-cloud">
                    @foreach (explode(',', $article->tags) as $tagId)
                        @php
                            $tag = $tags->firstWhere('id', $tagId);
                        @endphp
                        @if ($tag)
                            <a href="/app/acms/tag/{{ $tag->id }}"
                                class="tag text-decoration-none text-dark">{{ $tag->name }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- 图片预览遮罩层 -->
    <style>
        #img-viewer-mask {
            display: none;
            position: fixed;
            z-index: 99999;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        #img-viewer-close {
            position: absolute;
            top: 30px;
            right: 40px;
            font-size: 2.5rem;
            color: #fff;
            cursor: pointer;
            z-index: 100001;
            transition: color 0.2s;
        }

        #img-viewer-close:hover {
            color: #f5576c;
        }

        .img-viewer-arrow {
            position: absolute;
            top: 0;
            width: 48px;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100001;
            background: rgba(0, 0, 0, 0.01);
            transition: background 0.2s;
        }

        .img-viewer-arrow:hover {
            background: rgba(0, 0, 0, 0.10);
        }

        #img-viewer-left {
            left: 0;
        }

        #img-viewer-right {
            right: 0;
        }

        .img-viewer-arrow span {
            font-size: 2.5rem;
            color: #fff;
            user-select: none;
            text-shadow: 0 2px 8px #000;
            transition: color 0.2s, transform 0.2s;
        }

        .img-viewer-arrow:hover span {
            color: #f7971e;
            transform: scale(1.12);
        }

        #img-viewer-img {
            max-width: 90vw;
            max-height: 80vh;
            box-shadow: 0 0 20px #000;
            border-radius: 8px;
            z-index: 100000;
            cursor: zoom-out;
        }
    </style>
    <div id="img-viewer-mask">
        <span id="img-viewer-close">&times;</span>
        <div id="img-viewer-left" class="img-viewer-arrow"><span>&#8592;</span></div>
        <img id="img-viewer-img" src="" />
        <div id="img-viewer-right" class="img-viewer-arrow"><span>&#8594;</span></div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            const mask = document.getElementById('img-viewer-mask');
            const imgEl = document.getElementById('img-viewer-img');
            const closeBtn = document.getElementById('img-viewer-close');
            const leftBtn = document.getElementById('img-viewer-left');
            const rightBtn = document.getElementById('img-viewer-right');
            let imgs = [];
            let current = 0;

            function getAllImgs() {
                return Array.from(document.querySelectorAll('.article-content img'));
            }

            function show(index) {
                imgs = getAllImgs();
                if (imgs.length === 0) return;
                if (index < 0) index = imgs.length - 1;
                if (index >= imgs.length) index = 0;
                current = index;
                imgEl.src = imgs[current].src;
                mask.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function hide() {
                mask.style.display = 'none';
                document.body.style.overflow = '';
            }

            function prev() {
                show(current - 1);
            }

            function next() {
                show(current + 1);
            }

            // 事件委托，保证markdown渲染后图片也能响应
            document.addEventListener('click', function(e) {
                if (e.target.tagName === 'IMG' && e.target.closest('.article-content')) {
                    imgs = getAllImgs();
                    let idx = imgs.indexOf(e.target);
                    if (idx !== -1) show(idx);
                }
            });
            closeBtn.addEventListener('click', hide);
            leftBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                prev();
            });
            rightBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                next();
            });
            // 点击遮罩空白或图片本身关闭
            mask.addEventListener('click', function(e) {
                if (e.target === mask || e.target === imgEl) hide();
            });
            document.addEventListener('keydown', function(e) {
                if (mask.style.display === 'flex') {
                    if (e.key === 'Escape') hide();
                    if (e.key === 'ArrowLeft') prev();
                    if (e.key === 'ArrowRight') next();
                }
            });
        })();
    </script>
@endsection

@section('sidebar')
    @if (!empty($article->seo_keywords) || !empty($article->seo_description))
        <!-- SEO信息 -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">文章信息</h5>
            </div>
            <div class="card-body">
                @if (!empty($article->seo_keywords))
                    <p><strong>关键词:</strong> {{ $article->seo_keywords }}</p>
                @endif
                @if (!empty($article->seo_description))
                    <p><strong>描述:</strong> {{ $article->seo_description }}</p>
                @endif
            </div>
        </div>
    @endif

    @parent
@endsection
