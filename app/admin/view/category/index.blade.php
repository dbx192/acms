<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>分类管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
    <link rel="stylesheet" href="/app/admin/admin/css/reset.css" />
</head>
<body class="pear-container">
        <!-- 顶部查询表单 -->
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form top-search-from">
                    <div class="layui-form-item">
                        <label class="layui-form-label">分类名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="keyword" placeholder="请输入分类名称" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label"></label>
                        <button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="category-query">
                            <i class="layui-icon layui-icon-search"></i>查询
                        </button>
                        <button type="reset" class="pear-btn pear-btn-md" lay-submit lay-filter="category-reset">
                            <i class="layui-icon layui-icon-refresh"></i>重置
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- 数据表格 -->
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="category-table" lay-filter="category-table"></table>
        </div>
    </div>
    <script type="text/html" id="category-toolbar">
        <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add">
            <i class="layui-icon layui-icon-add-1"></i>新增
        </button>
        <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove">
            <i class="layui-icon layui-icon-delete"></i>删除
        </button>
    </script>
    <script type="text/html" id="category-bar">
        <button class="pear-btn pear-btn-primary pear-btn-sm" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
        <button class="pear-btn pear-btn-danger pear-btn-sm" lay-event="remove"><i class="layui-icon layui-icon-delete"></i></button>
    </script>
    <script type="text/html" id="category-status">
        <input type="checkbox" name="status" value="@{{d.id}}" lay-skin="switch" lay-text="显示|隐藏" lay-filter="category-status" @{{ d.status == 1 ? 'checked' : '' }}>
    </script>
    <script src="/app/admin/component/layui/layui.js?v=2.8.12"></script>
    <script src="/app/admin/component/pear/pear.js"></script>
    <script src="/app/admin/admin/js/permission.js"></script>
    <script src="/app/admin/admin/js/common.js"></script>
    <script>
    layui.use(["table", "treetable", "form", "common", "popup", "util"], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.$;
        let common = layui.common;
        let treeTable = layui.treetable;
        let util = layui.util;
        const PRIMARY_KEY = "id";
        const MODULE_PATH = "/app/admin/acms/category/";
        let cols = [
            {type: 'checkbox'},
            {title: '分类名称', field: 'name', align: 'left'},
            {title: '别名', field: 'slug', align: 'left'},
            {title: '排序', field: 'sort', align: 'center'},
            {title: '状态', field: 'status', align: 'center', templet: '#category-status'},
            {title: '创建时间', field: 'created_at', align: 'center'},
            {title: '操作', toolbar: '#category-bar', width: 130, align: 'center'}
        ];
        let currentWhere = {};
        function render(where = {}) {
            currentWhere = where;
            treeTable.render({
                elem: "#category-table",
                url: MODULE_PATH + 'index',
                where: where,
                treeColIndex: 1,
                treeIdName: 'id',
                treePidName: 'parent_id',
                treeDefaultClose: true,
                cols: [cols],
                skin: 'line',
                size: 'lg',
                toolbar: '#category-toolbar',
                defaultToolbar: [
                    {
                        title: '刷新',
                        layEvent: 'refresh',
                        icon: 'layui-icon-refresh',
                    }, 'filter', 'print', 'exports'
                ]
            });
        }
        render();
        // 工具栏事件
        table.on('toolbar(category-table)', function(obj) {
            if (obj.event === 'add') {
                add();
            } else if (obj.event === 'refresh') {
                render(currentWhere);
            } else if (obj.event === 'batchRemove') {
                batchRemove(obj);
            }
        });
        // 行事件
        table.on('tool(category-table)', function(obj) {
            if (obj.event === 'edit') {
                edit(obj);
            } else if (obj.event === 'remove') {
                remove(obj);
            }
        });
        // 查询事件
        form.on('submit(category-query)', function(data) {
            render(data.field);
            return false;
        });
        function add() {
            layer.open({
                type: 2,
                title: '新增分类',
                shade: 0.1,
                area: [common.isModile()?"100%":"520px", common.isModile()?"100%":"520px"],
                content: MODULE_PATH + 'add',
                end: function() {
                    render(currentWhere);
                }
            });
        }
        function edit(obj) {
            let value = obj.data[PRIMARY_KEY];
            layer.open({
                type: 2,
                title: '修改分类',
                shade: 0.1,
                area: [common.isModile()?"100%":"520px", common.isModile()?"100%":"520px"],
                content: MODULE_PATH + 'edit/' + value,
                end: function() {
                    render(currentWhere);
                }
            });
        }
        function remove(obj) {
            let id = obj.data[PRIMARY_KEY];
            doRemove(id, obj);
        }
        function batchRemove(obj) {
            let checkIds = common.checkField(obj, PRIMARY_KEY);
            if (checkIds === "") {
                layui.popup.warning("未选中数据");
                return false;
            }
            doRemove(checkIds.split(","));
        }
        function doRemove(ids, obj) {
            let data = {};
            data[PRIMARY_KEY] = ids;
            layer.confirm("确定删除?", {
                icon: 3,
                title: "提示"
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.ajax({
                    url: MODULE_PATH + 'delete',
                    data: data,
                    dataType: "json",
                    type: "post",
                    success: function(res) {
                        layer.close(loading);
                        if (res.code) {
                            return layui.popup.failure(res.msg);
                        }
                        return layui.popup.success("操作成功", function () {
                            return obj ? obj.del() : refreshTable();
                        });
                    }
                })
            });
        }
        window.refreshTable = function() {
            render(currentWhere);
        }
        form.on('switch(category-status)', function(obj) {
            let id = obj.value;
            let status = obj.elem.checked ? 1 : 0;
            $.ajax({
                url: MODULE_PATH + 'change-status',
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
        // 重置事件
        form.on('submit(category-reset)', function(data) {
            $(".top-search-from")[0].reset();
            render({});
            return false;
        });
    });
    </script>
</body>
</html>