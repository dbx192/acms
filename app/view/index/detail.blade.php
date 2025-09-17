@extends('layouts/app', ['plugin' => 'acms'])

@section('title', $article->title . ' - CMS系统')

@section('additional-styles')
    <style>
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

        .article-content h1,
        .article-content h2,
        .article-content h3 {
            margin: 1.5rem 0 1rem;
        }

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
            cursor: zoom-out;
        }
    </style>
@endsection

@section('content')
    <div class="card p-4">
        <h1 class="mb-3">{{ $article->title }}</h1>

        <div class="article-meta mb-4">
            <span><i class="far fa-folder"></i> <a href="/app/acms/category/{{ $article->category_id }}"
                    class="text-decoration-none text-secondary">{{ $article->category->name ?? '未分类' }}</a></span>
            <span class="ms-3"><i class="far fa-user"></i> {{ $article->user->nickname ?? '管理员' }}</span>
            <span class="ms-3"><i class="far fa-clock"></i> {{ $article->created_at->format('Y-m-d H:i') }}</span>
            <span class="ms-3"><i class="far fa-eye"></i> {{ $article->views }} 阅读</span>
            @if (session('user'))
                <span class="ms-3">
                    <i class="{{ $article->is_favorite ? 'fas fa-heart text-danger' : 'far fa-heart text-secondary' }}"></i>
                    <a href="#" class="favorite-btn"
                        data-id="{{ $article->id }}">{{ $article->is_favorite ? '取消收藏' : '收藏' }}</a>
                </span>
            @endif
        </div>

        @if ($article->thumb)
            <div class="mb-4">
                <img src="{{ $article->thumb }}" class="img-fluid rounded" alt="{{ $article->title }}">
            </div>
        @endif

        <div class="article-content markdown-body">
            @if ($article->type == 1)
                <div class="markdown-content" data-content="{{ $article->content }}"></div>
            @else
                {!! $article->content !!}
            @endif
        </div>

        <div class="mt-5">
            <h4 class="mb-4">评论</h4>

            @if (session('user'))
                <form id="comment-form" class="mb-4">
                    <input type="hidden" name="article_id" value="{{ $article->id }}">
                    <input type="hidden" name="parent_id" id="comment_parent_id" value="0">
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" placeholder="发表你的评论..." required></textarea>
                        <div id="reply-to" class="mt-2 text-muted small d-none">回复给: <span id="reply-to-user"></span> <a
                                href="#" class="cancel-reply">取消</a></div>
                    </div>
                    <button type="button" class="btn btn-primary mt-2 submit-comment">提交评论</button>
                    <div id="comment-result" class="mt-2"></div>
                </form>
            @else
                <div class="alert alert-info">
                    请<a href="/app/user/login">登录</a>后发表评论
                </div>
            @endif

            <div class="comment-list">
                @foreach ($comments as $comment)
                    @if ($comment->parent_id == 0)
                        @include('components.comment', [
                            'comment' => $comment,
                            'level' => 0,
                            'likes_count' => $comment->likes_count ?? 0,
                            'is_liked' => in_array($comment->id, $liked_comments),
                        ])
                    @endif
                @endforeach
            </div>
        </div>

        @if ($article->tags)
            <div class="mt-4 tag-cloud">
                @foreach (explode(',', $article->tags) as $tagId)
                    @php $tag = $tags->firstWhere('id', $tagId); @endphp
                    @if ($tag)
                        <a href="/app/acms/tag/{{ $tag->id }}"
                            class="tag text-decoration-none text-dark">{{ $tag->name }}</a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div id="img-viewer-mask">
        <span id="img-viewer-close">×</span>
        <div id="img-viewer-left" class="img-viewer-arrow"><span>←</span></div>
        <img id="img-viewer-img" src="" />
        <div id="img-viewer-right" class="img-viewer-arrow"><span>→</span></div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Favorite toggle
            $('.favorite-btn').on('click', function(e) {
                e.preventDefault();
                const $this = $(this);
                const articleId = $this.data('id');
                $.post('/app/acms/user/favorite/toggle', {
                    article_id: articleId
                }, function(data) {
                    if (data.code === 0) {
                        $this.text($this.text() === '收藏' ? '取消收藏' : '收藏');
                        $this.prev('i').toggleClass(
                            'far fa-heart text-secondary fas fa-heart text-danger');
                    } else {
                        alert(data.msg);
                    }
                }, 'json');
            });

            // Comment submission
            $('.submit-comment').on('click', function() {
                const $form = $('#comment-form');
                const $result = $('#comment-result');
                $.ajax({
                    url: '/app/acms/comment/add',
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(data) {
                        $result.html(
                            `<div class="alert alert-${data.code === 0 ? 'success' : 'danger'}">${data.msg}</div>`
                            );
                        if (data.code === 0) {
                            $form[0].reset();
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function() {
                        $result.html('<div class="alert alert-danger">评论提交失败，请重试</div>');
                    }
                });
            });

            // Cancel reply
            $('.cancel-reply').on('click', function(e) {
                e.preventDefault();
                $('#comment_parent_id').val('0');
                $('#reply-to').addClass('d-none');
                $('#comment-form')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            });

            // Reply to comment
            window.replyTo = function(commentId, nickname) {
                $('#comment_parent_id').val(commentId);
                $('#reply-to-user').text(nickname);
                $('#reply-to').removeClass('d-none');
                $('#comment-form')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            };

            // Like comment
            window.toggleLike = function(commentId) {
                const $icon = $(`#like-icon-${commentId}`);
                const $count = $(`#like-count-${commentId}`);
                $.post('/app/acms/user/comment/like', {
                    comment_id: commentId,
                    article_id: {{ $article->id }}
                }, function(data) {
                    if (data.code === 0) {
                        $icon.toggleClass(
                            'far fa-thumbs-up text-secondary fas fa-thumbs-up text-primary');
                        if ($count.length) $count.text(data.data?.likes_count || 0);
                    } else {
                        alert(data.msg);
                    }
                }, 'json').fail(function(error) {
                    console.error('Error:', error);
                });
            };

            // Image viewer
            const $mask = $('#img-viewer-mask');
            const $imgEl = $('#img-viewer-img');
            const $closeBtn = $('#img-viewer-close');
            const $leftBtn = $('#img-viewer-left');
            const $rightBtn = $('#img-viewer-right');
            let imgs = [];
            let current = 0;

            function getAllImgs() {
                return $('.article-content img').toArray();
            }

            function show(index) {
                imgs = getAllImgs();
                if (!imgs.length) return;
                index = index < 0 ? imgs.length - 1 : index >= imgs.length ? 0 : index;
                current = index;
                $imgEl.attr('src', imgs[current].src);
                $mask.css('display', 'flex');
                $('body').css('overflow', 'hidden');
            }

            function hide() {
                $mask.css('display', 'none');
                $('body').css('overflow', '');
            }

            $('.article-content').on('click', 'img', function() {
                imgs = getAllImgs();
                const idx = imgs.indexOf(this);
                if (idx !== -1) show(idx);
            });

            $closeBtn.on('click', hide);
            $leftBtn.on('click', e => {
                e.stopPropagation();
                show(current - 1);
            });
            $rightBtn.on('click', e => {
                e.stopPropagation();
                show(current + 1);
            });
            $mask.on('click', function(e) {
                if (e.target === this || e.target === $imgEl[0]) hide();
            });

            $(document).on('keydown', function(e) {
                if ($mask.css('display') === 'flex') {
                    if (e.key === 'Escape') hide();
                    if (e.key === 'ArrowLeft') show(current - 1);
                    if (e.key === 'ArrowRight') show(current + 1);
                }
            });
        });
    </script>
@endsection

@section('sidebar')
    @if ($article->seo_keywords || $article->seo_description)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">文章信息</h5>
            </div>
            <div class="card-body">
                @if ($article->seo_keywords)
                    <p><strong>关键词:</strong> {{ $article->seo_keywords }}</p>
                @endif
                @if ($article->seo_description)
                    <p><strong>描述:</strong> {{ $article->seo_description }}</p>
                @endif
            </div>
        </div>
    @endif
    @parent
@endsection
