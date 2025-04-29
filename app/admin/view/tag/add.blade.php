<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加标签</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
</head>
<body>
<form class="layui-form" action="">
    <div class="mainBox">
        <div class="main-container">
            <div class="layui-form-item">
                <label class="layui-form-label">标签名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required" placeholder="请输入标签名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">标签别名</label>
                <div class="layui-input-block">
                    <input type="text" name="slug" placeholder="请输入别名，用于URL优化" autocomplete="off" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="number" name="sort" value="0" placeholder="请输入排序值，值越小越靠前" autocomplete="off" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="status" value="1" lay-skin="switch" lay-text="显示|隐藏" checked>
                </div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <textarea name="description" placeholder="请输入标签描述" class="layui-textarea"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom">
        <div class="button-container">
            <button type="submit" class="pear-btn pear-btn-primary pear-btn-sm" lay-submit lay-filter="tag-save">
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
<script>
    layui.use(['form', 'jquery'], function() {
        let form = layui.form;
        let $ = layui.jquery;
        
        let MODULE_PATH = "/app/admin/acms/tag/";

        form.on('submit(tag-save)', function(data) {
            let loading = layer.load();
            
            // 处理开关状态
            if (data.field.status === undefined) {
                data.field.status = 0;
            }
            
            $.ajax({
                url: MODULE_PATH + 'save',
                data: data.field,
                dataType: 'json',
                type: 'post',
                success: function(result) {
                    layer.close(loading);
                    if (result.code === 0) {
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            parent.layer.close(parent.layer.getFrameIndex(window.name));
                            parent.window.refresh();
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