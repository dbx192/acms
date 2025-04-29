<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>文章管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
</head>
<body class="pear-container">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="">
                <div class="layui-form-item">
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">模糊搜索</label>
                        <div class="layui-input-inline">
                            <input type="text" name="keyword" placeholder="请输入文章标题/内容" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">分类</label>
                        <div class="layui-input-inline">
                            <select name="category_id">
                                <option value="">全部分类</option>
                                @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">标签</label>
                        <div class="layui-input-inline">
                            <select name="tag_id">
                                <option value="">全部标签</option>
                                @if(isset($tags))
                                    @foreach($tags as $tag)
                                    <option value="{{$tag->id}}">{{$tag->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">作者</label>
                        <div class="layui-input-inline">
                            <select name="author_id">
                                <option value="">全部作者</option>
                                @if(isset($authors))
                                    @foreach($authors as $author)
                                    <option value="{{$author->id}}">{{$author->username}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="article-query">
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
            <table id="article-table" lay-filter="article-table"></table>
        </div>
    </div>

    <script type="text/html" id="article-toolbar">
        <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add">
            <i class="layui-icon layui-icon-add-1"></i>
            新增
        </button>
        <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove">
            <i class="layui-icon layui-icon-delete"></i>
            删除
        </button>
    </script>

    <script type="text/html" id="article-bar">
        <button class="pear-btn pear-btn-primary pear-btn-sm" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
        <button class="pear-btn pear-btn-danger pear-btn-sm" lay-event="remove"><i class="layui-icon layui-icon-delete"></i></button>
    </script>

    <script type="text/html" id="article-top">
        <input type="checkbox" name="is_top" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="article-top" @{{ d.is_top == 1 ? 'checked' : '' }}>
    </script>

    <script type="text/html" id="article-recommend">
        <input type="checkbox" name="is_recommend" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="article-recommend" @{{ d.is_recommend == 1 ? 'checked' : '' }}>
    </script>

    <script type="text/html" id="article-status">
        <input type="checkbox" name="status" value="@{{d.id}}" lay-skin="switch" lay-text="显示|隐藏" lay-filter="article-status" @{{ d.status == 1 ? 'checked' : '' }}>
    </script>

    <script type="text/html" id="article-createTime">
        @{{layui.util.toDateString(d.created_at, 'yyyy-MM-dd HH:mm:ss')}}
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
            const MODULE_PATH = "/app/admin/acms/article/";
            // 表头参数
            let cols = [[
                {type: "checkbox"},
                {title: "ID", field: "id", align: "center", width: 80, sort: true},
                {title: "标题", field: "title", align: "left", templet: function(d){ return '<a href="'+d.url+'" target="_blank">'+layui.util.escape(d.title)+'</a>'; }},
                {title: "分类", field: "category_name", align: "center", templet: function(d){
                    return d.category_name ? '<a href="javascript:;" class="pear-text-primary category-filter" data-id="'+d.category_id+'">'+layui.util.escape(d.category_name)+'</a>' : '-';
                }},
                {title: "标签", field: "tags", align: "center", templet: function(d){
                    if(!d.tags || d.tags.length === 0) return '-';
                    return d.tags.map(function(tag){ return '<span class="layui-badge layui-bg-blue tag-filter" data-id="'+tag.id+'" style="cursor:pointer;">'+layui.util.escape(tag.name)+'</span>'; }).join(' ');
                }},
                {title: "作者", field: "author_name", align: "center", templet: function(d){
                    return d.author_name ? '<a href="javascript:;" class="pear-text-primary author-filter" data-id="'+d.user_id+'">'+layui.util.escape(d.author_name)+'</a>' : '-';
                }},
                {title: "置顶", field: "is_top", align: "center", templet: '#article-top', width: 100},
                {title: "推荐", field: "is_recommend", align: "center", templet: '#article-recommend', width: 100},
                {title: "状态", field: "status", align: "center", templet: '#article-status', width: 100},
                {title: "浏览量", field: "views", align: "center", width: 100},
                {title: "创建时间", field: "created_at", align: "center", templet: '#article-createTime', width: 180},
                {title: "操作", toolbar: '#article-bar', align: 'center', width: 130}
            ]];
            // 渲染表格
            function renderTable() {
                table.render({
                    elem: "#article-table",
                    url: MODULE_PATH + "index",
                    page: true,
                    cols: cols,
                    skin: "line",
                    toolbar: "#article-toolbar",
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
            table.on("tool(article-table)", function(obj) {
                if (obj.event === "remove") {
                    doRemove(obj.data[PRIMARY_KEY]);
                } else if (obj.event === "edit") {
                    edit(obj);
                }
            });
            // toolbar事件
            table.on("toolbar(article-table)", function(obj) {
                if (obj.event === "add") {
                    add();
                } else if (obj.event === "refresh") {
                    refreshTable();
                } else if (obj.event === "batchRemove") {
                    batchRemove(obj);
                }
            });
            // 查询事件
            form.on("submit(article-query)", function(data) {
                table.reload("article-table", {
                    page: {curr: 1},
                    where: data.field
                });
                return false;
            });
            window.remove = function(obj) {
                doRemove([obj.data[PRIMARY_KEY]]);
            }
            window.batchRemove = function(obj) {
                let data = table.checkStatus(obj.config.id).data;
                if (data.length === 0) {
                    layer.msg("未选中数据", {icon: 3, time: 1000});
                    return false;
                }
                let ids = data.map(function(item){ return item.id; });
                doRemove(ids);
            }
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
            // 刷新表格
            window.refreshTable = function() {
                table.reloadData("article-table", {
                    scrollPos: "fixed",
                    done: function (res, curr) {
                        if (curr > 1 && res.data && !res.data.length) {
                            curr = curr - 1;
                            table.reloadData("article-table", {
                                page: {curr: curr}
                            });
                        }
                    }
                });
            }
            // 新增
            function add() {
                layer.open({
                    type: 2,
                    title: "新增文章",
                    shade: 0.1,
                    area: ["90%", "90%"],
                    content: MODULE_PATH + "add"
                });
            }
            // 编辑
            function edit(obj) {
                layer.open({
                    type: 2,
                    title: "修改文章",
                    shade: 0.1,
                    area: ["90%", "90%"],
                    content: MODULE_PATH + "edit/" + obj.data[PRIMARY_KEY]
                });
            }
            // 状态switch事件
            form.on('switch(article-top)', function(obj) {
                let id = obj.value;
                let status = obj.elem.checked ? 1 : 0;
                $.ajax({
                    url: MODULE_PATH + 'change-status',
                    data: {id: id, field: 'is_top', value: status},
                    dataType: 'json',
                    type: 'post',
                    success: function(result) {
                        if (result.code === 0) {
                            layer.msg(result.msg, {icon: 1});
                        } else {
                            layer.msg(result.msg, {icon: 2});
                            $(obj.elem).prop('checked', !obj.elem.checked);
                            form.render();
                        }
                    }
                });
            });
            form.on('switch(article-recommend)', function(obj) {
                let id = obj.value;
                let status = obj.elem.checked ? 1 : 0;
                $.ajax({
                    url: MODULE_PATH + 'change-status',
                    data: {id: id, field: 'is_recommend', value: status},
                    dataType: 'json',
                    type: 'post',
                    success: function(result) {
                        if (result.code === 0) {
                            layer.msg(result.msg, {icon: 1});
                        } else {
                            layer.msg(result.msg, {icon: 2});
                            $(obj.elem).prop('checked', !obj.elem.checked);
                            form.render();
                        }
                    }
                });
            });
            form.on('switch(article-status)', function(obj) {
                let id = obj.value;
                let status = obj.elem.checked ? 1 : 0;
                $.ajax({
                    url: MODULE_PATH + 'change-status',
                    data: {id: id, field: 'status', value: status},
                    dataType: 'json',
                    type: 'post',
                    success: function(result) {
                        if (result.code === 0) {
                            layer.msg(result.msg, {icon: 1});
                        } else {
                            layer.msg(result.msg, {icon: 2});
                            $(obj.elem).prop('checked', !obj.elem.checked);
                            form.render();
                        }
                    }
                });
            });
            // 分类/标签/作者筛选点击事件
            $(document).on('click', '.category-filter', function(){
                var id = $(this).data('id');
                $('select[name=category_id]').val(id);
                form.render('select');
                table.reload('article-table', {
                    where: {
                        category_id: id,
                        tag_id: $('select[name=tag_id]').val(),
                        author_id: $('select[name=author_id]').val(),
                        keyword: $('input[name=keyword]').val()
                    },
                    page: {curr: 1}
                });
            });
            $(document).on('click', '.tag-filter', function(){
                var id = $(this).data('id');
                $('select[name=tag_id]').val(id);
                form.render('select');
                table.reload('article-table', {
                    where: {
                        tag_id: id,
                        category_id: $('select[name=category_id]').val(),
                        author_id: $('select[name=author_id]').val(),
                        keyword: $('input[name=keyword]').val()
                    },
                    page: {curr: 1}
                });
            });
            $(document).on('click', '.author-filter', function(){
                var id = $(this).data('id');
                $('select[name=author_id]').val(id);
                form.render('select');
                table.reload('article-table', {
                    where: {
                        author_id: id,
                        category_id: $('select[name=category_id]').val(),
                        tag_id: $('select[name=tag_id]').val(),
                        keyword: $('input[name=keyword]').val()
                    },
                    page: {curr: 1}
                });
            });
        });
    </script>
</body>
</html>