<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>标签管理</title>
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
                        <label class="layui-form-label">标签名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="请输入标签名称" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="tag-query">
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
            <table id="tag-table" lay-filter="tag-table"></table>
        </div>
    </div>

    <script type="text/html" id="tag-toolbar">
        <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add">
            <i class="layui-icon layui-icon-add-1"></i>
            新增
        </button>
        <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove">
            <i class="layui-icon layui-icon-delete"></i>
            删除
        </button>
    </script>

    <script type="text/html" id="tag-bar">
        <button class="pear-btn pear-btn-primary pear-btn-sm" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
        <button class="pear-btn pear-btn-danger pear-btn-sm" lay-event="remove"><i class="layui-icon layui-icon-delete"></i></button>
    </script>

    <script type="text/html" id="tag-status">
        <input type="checkbox" name="status" value="@{{d.id}}" lay-skin="switch" lay-text="显示|隐藏" lay-filter="tag-status" @{{d.status == 1 ? 'checked' : ''}}>
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
            const MODULE_PATH = "/app/admin/acms/tag/";
            let cols = [
                [
                    {type: 'checkbox'},
                    {title: 'ID', field: 'id', width: 80, align: 'center'},
                    {title: '标签名称', field: 'name', align: 'left'},
                    {title: '别名', field: 'slug', align: 'left'},
                    {title: '文章数', field: 'article_count', align: 'center'},
                    {title: '排序', field: 'sort', align: 'center'},
                    {title: '状态', field: 'status', align: 'center', templet: '#tag-status'},
                    {title: '创建时间', field: 'created_at', align: 'center'},
                    {title: '操作', toolbar: '#tag-bar', width: 130, align: 'center'}
                ]
            ];
            function renderTable() {
                table.render({
                    elem: "#tag-table",
                    url: MODULE_PATH + 'index',
                    page: true,
                    cols: cols,
                    skin: 'line',
                    toolbar: '#tag-toolbar',
                    defaultToolbar: [{
                        title: '刷新',
                        layEvent: 'refresh',
                        icon: 'layui-icon-refresh',
                    }, 'filter', 'print', 'exports'],
                    done: function () {}
                });
            }
            renderTable();
            table.on('tool(tag-table)', function(obj) {
                if (obj.event === 'remove') {
                    doRemove([obj.data[PRIMARY_KEY]]);
                } else if (obj.event === 'edit') {
                    edit(obj);
                }
            });
            table.on('toolbar(tag-table)', function(obj) {
                if (obj.event === 'add') {
                    add();
                } else if (obj.event === 'refresh') {
                    refreshTable();
                } else if (obj.event === 'batchRemove') {
                    batchRemove(obj);
                }
            });
            form.on('submit(tag-query)', function(data) {
                table.reload('tag-table', {
                    page: {curr: 1},
                    where: data.field
                });
                return false;
            });
            function doRemove(ids) {
                layer.confirm('确定要删除?', {icon: 3, title: '提示'}, function(index) {
                    layer.close(index);
                    let loading = layer.load();
                    $.ajax({
                        url: MODULE_PATH + 'delete',
                        data: {id: ids},
                        dataType: 'json',
                        type: 'post',
                        success: function(result) {
                            layer.close(loading);
                            if (result.code === 0) {
                                layer.msg(result.msg, {icon: 1}, refreshTable);
                            } else {
                                layer.msg(result.msg, {icon: 2});
                            }
                        }
                    })
                });
            }
            function batchRemove(obj) {
                let checkStatus = table.checkStatus('tag-table');
                let data = checkStatus.data;
                if (data.length === 0) {
                    layer.msg('未选中数据', {icon: 3, time: 1000});
                    return false;
                }
                let ids = data.map(function(item){ return item.id; });
                doRemove(ids);
            }
            window.refreshTable = function() {
                table.reloadData('tag-table', {
                    scrollPos: "fixed",
                    done: function (res, curr) {
                        if (curr > 1 && res.data && !res.data.length) {
                            curr = curr - 1;
                            table.reloadData('tag-table', {
                                page: {curr: curr}
                            });
                        }
                    }
                });
            }
            function add() {
                layer.open({
                    type: 2,
                    title: '新增标签',
                    shade: 0.1,
                    area: ['500px', '400px'],
                    content: MODULE_PATH + 'add'
                });
            }
            function edit(obj) {
                layer.open({
                    type: 2,
                    title: '修改标签',
                    shade: 0.1,
                    area: ['500px', '400px'],
                    content: MODULE_PATH + 'edit/' + obj.data[PRIMARY_KEY]
                });
            }
            form.on('switch(tag-status)', function(obj) {
                let id = obj.value;
                let status = obj.elem.checked ? 1 : 0;
                $.ajax({
                    url: MODULE_PATH + 'status',
                    data: {id: id, status: status},
                    dataType: 'json',
                    type: 'post',
                    success: function(result) {
                        if (result.code === 0) {
                            layer.msg(result.msg, {icon: 1, time: 1000});
                        } else {
                            layer.msg(result.msg, {icon: 2, time: 1000});
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>