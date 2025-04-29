@props(['comment', 'level'])

<div class="card mb-3" style="margin-left: {{ $level * 30 }}px">
    <div class="card-body">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
                <img src="{{ $comment->user->avatar ?? '/plugin/user/app/public/images/default-avatar.png' }}" class="rounded-circle" width="50" height="50" alt="用户头像">
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div>
                        <h5 class="mt-0 mb-0 me-2 d-inline-block">{{ $comment->user->nickname ?? '匿名用户' }}</h5>
                        <small class="text-muted">{{ date('Y-m-d H:i', strtotime($comment->created_at)) }}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="replyTo({{ $comment->id }}, '{{ $comment->user->nickname ?? '匿名用户' }}')">回复</button>
                </div>
                <p class="mt-1 mb-2">{{ $comment->content }}</p>
            </div>
        </div>
    </div>
</div>

@if($comment->replies && count($comment->replies) > 0)
    @foreach($comment->replies as $reply)
        @include('components.comment', ['comment' => $reply, 'level' => $level + 1])
    @endforeach
@endif