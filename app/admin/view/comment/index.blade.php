<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>评论管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
</head>
<body class="pear-container">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" id="comment-search-form">
                <div class="layui-form-item">
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">评论内容</label>
                        <div class="layui-input-inline">
                            <input type="text" name="keyword" placeholder="请输入评论内容" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">文章</label>
                        <div class="layui-input-inline">
                            <select name="article_id" id="article_id">
                                <option value="">全部文章</option>
                                @foreach(\plugin\acms\app\model\Article::all() as $article)
                                <option value="{{$article->id}}">{{$article->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">用户</label>
                        <div class="layui-input-inline">
                            <select name="user_id" id="user_id">
                                <option value="">全部用户</option>
                                @foreach(\plugin\acms\app\model\Comment::with('user')->get()->pluck('user')->unique('id') as $user)
                                @if($user)
                                <option value="{{$user->id}}">{{$user->nickname}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="comment-query">
                            <i class="layui-icon layui-icon-search"></i>
                            查询
                        </button>
                        <button type="reset" class="pear-btn pear-btn-md">
                            <i class="layui-icon layui-icon-refresh"></i>
                            重置
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-body">
            <table class="layui-table" id="comment-table" lay-filter="comment-table"></table>
        </div>
    </div>

    <script type="text/html" id="comment-bar">
        <button class="pear-btn pear-btn-primary pear-btn-sm" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
        <button class="pear-btn pear-btn-danger pear-btn-sm" lay-event="delete"><i class="layui-icon layui-icon-delete"></i></button>
    </script>
    <script type="text/html" id="comment-user">
        <a href="javascript:;" class="pear-text-primary" lay-event="user-filter">@{{d.user && d.user.nickname ? d.user.nickname : ''}}</a>
    </script>
    <script type="text/html" id="comment-article">
        <a href="javascript:;" class="pear-text-primary" lay-event="article-filter">@{{d.article && d.article.title ? d.article.title : ''}}</a>
    </script>
    <script type="text/html" id="comment-toolbar">
        <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove">
            <i class="layui-icon layui-icon-delete"></i>
            批量删除
        </button>
    </script>

    <script src="/app/admin/component/layui/layui.js"></script>
    <script src="/app/admin/component/pear/pear.js"></script>
    <script>
    layui.use(["table", "form", "layer", "jquery"], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let layer = layui.layer;
        const PRIMARY_KEY = "id";
        const MODULE_PATH = "/app/admin/acms/comment/";
        // 表头参数
        let cols = [[
            {type: 'checkbox'},
            {field: 'id', title: 'ID', width:80, sort: true},
            {field: 'user.nickname', title: '用户', width:120, templet: function(d){
                return d.user && d.user.nickname ? '<a href="javascript:;" class="pear-text-primary user-filter" data-id="'+d.user.id+'">'+d.user.nickname+'</a>' : '';
            }},
            {field: 'article.title', title: '文章', width:200, templet: function(d){
                return d.article && d.article.title ? '<a href="javascript:;" class="pear-text-primary article-filter" data-id="'+d.article.id+'">'+d.article.title+'</a>' : '';
            }},
            {field: 'content', title: '内容', minWidth:200},
            {field: 'created_at', title: '发布时间', width:180, sort: true},
            {field: 'status', title: '审核状态', width:100, templet: function(d){
                return '<input type="checkbox" name="status" value="'+d.id+'" lay-skin="switch" lay-text="已审核|待审核" lay-filter="comment-status" '+(d.status==1?'checked':'')+'>';
            }},
            {title: '操作', width:180, align:'center', fixed:'right', toolbar:'#comment-bar'}
        ]];
        // 渲染表格
        function renderTable() {
            table.render({
                elem: "#comment-table",
                url: MODULE_PATH + "index",
                page: true,
                cols: cols,
                skin: "line",
                toolbar: "#comment-toolbar",
                defaultToolbar: [{
                    title: "刷新",
                    layEvent: "refresh",
                    icon: "layui-icon-refresh",
                }, "filter", "print", "exports"],
                done: function () {}
            });
        }
        renderTable();
        // 工具条事件
        table.on("tool(comment-table)", function(obj) {
            if(obj.event === 'edit'){
                edit(obj);
            } else if(obj.event === 'delete'){
                doRemove([obj.data[PRIMARY_KEY]]);
            }
        });
        // toolbar事件
        table.on("toolbar(comment-table)", function(obj) {
            if (obj.event === "batchRemove") {
                batchRemove(obj);
            }
        });
        // 查询事件
        form.on("submit(comment-query)", function(data) {
            table.reload("comment-table", {
                page: {curr: 1},
                where: data.field
            });
            return false;
        });
        // 单条删除
        function doRemove(ids) {
            layer.confirm("确定删除?", {icon: 3, title: "提示"}, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.ajax({
                    url: MODULE_PATH + "delete",
                    data: {id: ids},
                    dataType: "json",
                    type: "post",
                    success: function(res) {
                        layer.close(loading);
                        if (res.code === 0) {
                            layer.msg(res.msg, {icon: 1}, refreshTable);
                        } else {
                            layer.msg(res.msg, {icon: 2});
                        }
                    }
                });
            });
        }
        // 批量删除
        function batchRemove(obj) {
            let checkStatus = table.checkStatus("comment-table");
            let data = checkStatus.data;
            if (data.length === 0) {
                layer.msg("未选中数据", {icon: 3, time: 1000});
                return false;
            }
            let ids = data.map(function(item){ return item.id; });
            doRemove(ids);
        }
        // 刷新表格
        window.refreshTable = function() {
            table.reloadData("comment-table", {
                scrollPos: "fixed",
                done: function (res, curr) {
                    if (curr > 1 && res.data && !res.data.length) {
                        curr = curr - 1;
                        table.reloadData("comment-table", {
                            page: {curr: curr}
                        });
                    }
                }
            });
        }
        // 编辑
        function edit(obj) {
            layer.open({
                type: 2,
                title: "编辑评论",
                shade: 0.1,
                area: ["600px", "400px"],
                content: MODULE_PATH + "edit/" + obj.data[PRIMARY_KEY]
            });
        }
        // 用户/文章筛选点击事件
        $(document).on('click', '.user-filter', function(){
            var id = $(this).data('id');
            $('#user_id').val(id);
            form.render('select');
            table.reload('comment-table', {
                where: {
                    user_id: id,
                    article_id: $('#article_id').val(),
                    keyword: $('input[name=keyword]').val()
                },
                page: {curr: 1}
            });
        });
        $(document).on('click', '.article-filter', function(){
            var id = $(this).data('id');
            $('#article_id').val(id);
            form.render('select');
            table.reload('comment-table', {
                where: {
                    article_id: id,
                    user_id: $('#user_id').val(),
                    keyword: $('input[name=keyword]').val()
                },
                page: {curr: 1}
            });
        });
        // 状态switch事件
        form.on('switch(comment-status)', function(obj){
            var id = this.value;
            var status = obj.elem.checked ? 1 : 0;
            $.post(MODULE_PATH + 'audit', {id: id, status: status}, function(res){
                if(res.code === 0){
                    layer.msg('操作成功', {icon:1});
                } else {
                    layer.msg(res.msg, {icon:2});
                }
            });
        });
    });
    </script>
</body>
</html>