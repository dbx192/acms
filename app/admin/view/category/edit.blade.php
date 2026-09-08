<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑分类</title>
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
                    <label class="layui-form-label">分类名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" value="{{$category->name}}" lay-verify="required" placeholder="请输入分类名称" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">上级分类</label>
                    <div class="layui-input-block">
                        <select name="parent_id">
                            <option value="0">顶级分类</option>
                            @foreach($categoryOptions as $option)
                            <option value="{{$option['id']}}" @if($category->parent_id == $option['id']) selected @endif>{{ str_repeat('　', $option['depth']) }}@if($option['depth'] > 0)├─ @endif{{$option['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">别名</label>
                    <div class="layui-input-block">
                        <input type="text" name="slug" value="{{$category->slug}}" placeholder="请输入分类别名，用于URL" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">缩略图</label>
                    <div class="layui-input-block">
                        <img src="{{ $category->thumb ?? '' }}" style="height:100px; max-width:200px; object-fit:cover; border:1px solid #e6e6e6; border-radius:4px; margin-bottom:8px;"/>
                        <input type="text" style="display:none" name="thumb" value="{{ $category->thumb ?? '' }}" />
                        <button type="button" class="pear-btn pear-btn-primary pear-btn-sm" id="thumb-upload">
                            <i class="layui-icon layui-icon-upload"></i>上传图片
                        </button>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description" placeholder="请输入分类描述" class="layui-textarea">{{$category->description}}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">SEO标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="seo_title" value="{{$category->seo_title}}" placeholder="请输入SEO标题" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">SEO关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="seo_keywords" value="{{$category->seo_keywords}}" placeholder="请输入SEO关键词，用英文逗号分隔" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">SEO描述</label>
                    <div class="layui-input-block">
                        <textarea name="seo_description" placeholder="请输入SEO描述" class="layui-textarea">{{$category->seo_description}}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">排序值</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" value="{{$category->sort}}" placeholder="数值越大越靠前" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="status" value="1" lay-skin="switch" lay-text="显示|隐藏" @if($category->status) checked @endif>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom">
            <div class="button-container">
                <button type="submit" class="pear-btn pear-btn-primary pear-btn-sm" lay-submit="" lay-filter="category-update">
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
        layui.use(['form', 'jquery', 'layer', 'upload'], function() {
            let form = layui.form;
            let $ = layui.jquery;
            let layer = layui.layer;
            let upload = layui.upload;

            // 缩略图上传
            upload.render({
                elem: '#thumb-upload',
                url: '/app/admin/acms/upload/image',
                acceptMime: 'image/gif,image/jpeg,image/jpg,image/png,image/webp',
                field: 'editormd-image-file',
                done: function(res) {
                    if (res.success === 1) {
                        this.item.prev().val(res.url).prev().attr('src', res.url);
                    } else {
                        layer.msg(res.message || '上传失败', {icon: 2});
                    }
                }
            });

            // 监听提交
            form.on('submit(category-update)', function(data) {
                data.field.status = data.field.status ? 1 : 0;
                
                $.ajax({
                    url: '/app/admin/acms/category/update/{{$category->id}}',
                    data: data.field,
                    dataType: 'json',
                    type: 'post',
                    success: function(result) {
                        if (result.code === 0) {
                            layer.msg(result.msg, {icon: 1, time: 1000}, function() {
                                parent.layer.close(parent.layer.getFrameIndex(window.name));
                                parent.refresh();
                            });
                        } else {
                            layer.msg(result.msg, {icon: 2, time: 1000});
                        }
                    }
                });
                return false;
            });
        });
    </script>
</body>
</html> 