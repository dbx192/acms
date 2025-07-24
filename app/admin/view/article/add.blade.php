<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>新增文章</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
    <link href="https://cdn.jsdelivr.net/npm/editor.md@1.5.0/css/editormd.min.css" rel="stylesheet">
    <style>
        .editormd-fullscreen {
            z-index: 19891017;
        }
    </style>
</head>

<body>
    <form class="layui-form" action="">
        <div class="mainBox">
            <div class="main-container">
                <div class="layui-form-item">
                    <label class="layui-form-label">文章标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" lay-verify="required" placeholder="请输入文章标题"
                            autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属分类</label>
                    <div class="layui-input-block">
                        <select name="category_id" lay-verify="required">
                            <option value="">请选择分类</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">文章摘要</label>
                    <div class="layui-input-block">
                        <textarea name="summary" placeholder="请输入文章摘要" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">缩略图</label>
                    <div class="layui-input-block">
                        <input type="text" name="thumb" placeholder="请输入缩略图URL" autocomplete="off"
                            class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">文章内容</label>
                    <div class="layui-input-block">
                        <div id="editor">
                            <textarea style="display:none;" name="content"></textarea>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">标签选择</label>
                    <div class="layui-input-block">
                        <select name="tags" xm-select="tags" xm-select-search="">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">SEO标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="seo_title" placeholder="请输入SEO标题" autocomplete="off"
                            class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">SEO关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="seo_keywords" placeholder="请输入SEO关键词，用英文逗号分隔" autocomplete="off"
                            class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">SEO描述</label>
                    <div class="layui-input-block">
                        <textarea name="seo_description" placeholder="请输入SEO描述" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">排序值</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" value="0" placeholder="数值越大越靠前" autocomplete="off"
                            class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">文章类型</label>
                    <div class="layui-input-block">
                        <input type="radio" name="type" value="1" title="Markdown" checked>
                        <input type="radio" name="type" value="2" title="HTML">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">置顶</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="is_top" value="1" lay-skin="switch" lay-text="是|否">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">推荐</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="is_recommend" value="1" lay-skin="switch" lay-text="是|否">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="status" value="1" lay-skin="switch" lay-text="显示|隐藏"
                            checked>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom">
            <div class="button-container">
                <button type="submit" class="pear-btn pear-btn-primary pear-btn-sm" lay-submit=""
                    lay-filter="article-save">
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
    <script src="https://cdn.jsdelivr.net/npm/editor.md@1.5.0/editormd.min.js"></script>

    <script>
        layui.use(['form', 'element', 'select', 'layer'], function() {
            let form = layui.form;
            let element = layui.element;
            let select = layui.select;
            let layer = layui.layer;

            // 初始化多选标签
            select.render({
                el: 'select[name="tags"]'
            });

            // 初始化Markdown编辑器
            let editor = editormd("editor", {
                width: "100%",
                height: 640,
                path: "https://cdn.jsdelivr.net/npm/editor.md@1.5.0/lib/",
                toolbarIcons: function() {
                    return [
                        "undo", "redo", "|",
                        "bold", "italic", "quote", "uppercase", "lowercase", "|",
                        "h1", "h2", "h3", "h4", "h5", "h6", "|",
                        "list-ul", "list-ol", "hr", "|",
                        "link", "reference-link", "image", "code", "preformatted-text",
                        "code-block", "table", "datetime", "|",
                        "watch", "preview", "fullscreen", "|",
                        "help"
                    ]
                },
                saveHTMLToTextarea: true, // 保存HTML到Textarea
                emoji: true,
                taskList: true,
                tocm: true, // Using [TOCM]
                tex: true, // 开启科学公式TeX语言支持
                flowChart: true, // 开启流程图支持
                sequenceDiagram: true, // 开启时序/序列图支持
                imageUpload: true,
                imageFormats: ["jpg", "jpeg", "gif", "png", "webp"],
                imageUploadURL: "/app/admin/acms/upload/image" // 图片上传接口
            });

            // 监听提交
            form.on('submit(article-save)', function(data) {
                // 将编辑器的HTML内容赋值给表单
                data.field.content = editor.getMarkdown();

                // 处理标签数据
                if (data.field.tags) {
                    data.field.tags = data.field.tags;
                }

                // 处理复选框
                data.field.is_top = data.field.is_top ? 1 : 0;
                data.field.is_recommend = data.field.is_recommend ? 1 : 0;
                data.field.status = data.field.status ? 1 : 0;

                // 发送请求
                $.ajax({
                    url: '/app/admin/acms/article/save',
                    data: data.field,
                    dataType: 'json',
                    type: 'post',
                    success: function(result) {
                        if (result.code === 0) {
                            layer.msg(result.msg, {
                                icon: 1,
                                time: 1000
                            }, function() {
                                parent.layer.close(parent.layer.getFrameIndex(window
                                    .name));
                                parent.refreshTable();
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
