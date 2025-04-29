<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加评论</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
</head>
<body class="pear-container">
<div class="layui-card">
    <div class="layui-card-header">
        <h2>添加评论</h2>
    </div>
    <div class="layui-card-body">
        <form class="layui-form" lay-filter="comment-form">
            <div class="layui-form-item">
                <label class="layui-form-label">文章</label>
                <div class="layui-input-block">
                    <select name="article_id" lay-verify="required">
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}">{{ $article->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">内容</label>
                <div class="layui-input-block">
                    <textarea name="content" placeholder="请输入内容" class="layui-textarea" lay-verify="required"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="comment-form">提交</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="/app/admin/component/layui/layui.js"></script>
<script src="/app/admin/component/pear/pear.js"></script>
<script>
    layui.use(['form'], function(){
        var form = layui.form;
        form.on('submit(comment-form)', function(data){
            $.post('/app/admin/acms/comment/store', data.field, function(res){
                if(res.code === 0) {
                    layer.msg('添加成功', {icon:1});
                    setTimeout(function(){
                        if(window.parent){window.parent.layer.closeAll();window.parent.location.reload();}
                    }, 1000);
                } else {
                    layer.msg(res.msg, {icon:2});
                }
            });
            return false;
        });
    });
</script>
</body>
</html>