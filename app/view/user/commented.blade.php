<?= plugin\user\api\Template::header('我的评论') ?>

<?= plugin\user\api\Template::nav() ?>

<div class="container">
    <div class="row">
        <?= plugin\user\api\Template::sidebar() ?>

        <div class="col-md-9 col-12 pt-4">
            <div class="mb-4 card bg-white border-0 shadow-sm" style="min-height:80vh">
                <div class="card-body">
                    <h5 class="pb-3 mb-4 border-bottom">我的评论</h5>

                    @if($articles->count() > 0)
                    @foreach($articles as $item)
                    <div class="card mb-3 border-0 shadow-sm" onclick="window.location.href='/app/acms/article/{{ $item->article->id }}'" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $item->article->title ?? '' }}</h5>
                                <span class="text-muted"><i class="far fa-clock"></i> {{ $item->created_at }}</span>
                            </div>
                            <div class="mt-2 text-muted">
                                {{ \Illuminate\Support\Str::limit($item->content, 20) }}
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- 分页 -->
                    <div class="d-flex justify-content-center">
                        {!! $paginator !!}
                    </div>
                    @else
                        <div class="alert alert-info">暂无评论文章</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<?= plugin\user\api\Template::footer() ?>