<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>编辑评论</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
</head>

<body>
    <form class="layui-form" action="">
        <input type="hidden" name="id" value="{{ $comment->id }}">
        <div class="mainBox">
            <div class="main-container">
                <div class="layui-form-item">
                    <label class="layui-form-label">所属文章</label>
                    <div class="layui-input-block">
                        <select name="article_id" lay-verify="required" disabled>
                            <option value="">请选择文章</option>
                            @foreach ($articles as $article)
                                <option value="{{ $article->id }}" @if ($article->id == $comment->article_id) selected @endif>
                                    {{ $article->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">评论内容</label>
                    <div class="layui-input-block">
                        <textarea name="content" placeholder="请输入评论内容" class="layui-textarea" lay-verify="required">{{ $comment->content }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">审核状态</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="status" value="1" lay-skin="switch" lay-text="已审核|待审核"
                            @if ($comment->status) checked @endif>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom">
            <div class="button-container">
                <button type="submit" class="pear-btn pear-btn-primary pear-btn-sm" lay-submit=""
                    lay-filter="comment-update">
                    <i class="layui-icon layui-icon-ok"></i>
                    提交
                </button>
                <button type="reset" class="pear-btn pear-btn-sm">
                    <i class="layui-icon layui-icon-refresh"></i>
                    重置
                </button>
            </div>
        </div>
    </form>
    <script src="/app/admin/component/layui/layui.js"></script>
    <script src="/app/admin/component/pear/pear.js"></script>
    <script src="/app/acms/js/jquery.min.js"></script>
    <script>
        layui.use(['form', 'layer'], function() {
            var form = layui.form;
            var layer = layui.layer;
            form.on('submit(comment-update)', function(data) {
                // 处理审核switch
                data.field.status = data.field.status ? 1 : 0;
                var id = $('input[name=id]').val();
                $.ajax({
                    url: '/app/admin/acms/comment/update/' + id,
                    data: data.field,
                    dataType: 'json',
                    type: 'post',
                    success: function(result) {
                        if (result.code === 0) {
                            layer.msg('更新成功', {
                                icon: 1,
                                time: 1000
                            }, function() {
                                if (parent.layer) parent.layer.close(parent.layer
                                    .getFrameIndex(window.name));
                                if (parent.layui && parent.layui.table) parent.layui
                                    .table.reload('comment-table');
                            });
                        } else {
                            layer.msg(result.msg, {
                                icon: 2,
                                time: 1000
                            });
                        }
                    }
                });
                return false;
            });
        });
    </script>
</body>

</html>
