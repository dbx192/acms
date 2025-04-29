<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CMS系统 - 内容管理系统')</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">
    <!-- Markdown解析库 -->
    <link href="/app/acms/css/github-markdown.min.css" rel="stylesheet">
    <link href="/app/acms/css/github.min.css" rel="stylesheet">
    <link href="/app/acms/css/pagination.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-title {
            font-weight: 600;
        }
        .sidebar-item {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
            position: relative;
            border: 1px solid #f0f0f0;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .sidebar-item:hover {
            background-color: #f5f7fa;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px) scale(1.02);
        }
        .sidebar-item .fa {
            margin-right: 8px;
            color: #6c63ff;
        }
        .sidebar-item:not(:last-child)::after {
            content: '';
            display: block;
            height: 1px;
            background: #f0f0f0;
            margin: 10px 0 0 0;
        }
        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .tag {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 0 4px 6px 0;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
            color: #fff !important;
            box-shadow: 0 2px 6px rgba(240,147,251,0.08);
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            border: none;
        }
        .tag:nth-child(5n+1) { background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%); }
        .tag:nth-child(5n+2) { background: linear-gradient(90deg, #fa709a 0%, #fee140 100%); }
        .tag:nth-child(5n+3) { background: linear-gradient(90deg, #30cfd0 0%, #330867 100%); }
        .tag:nth-child(5n+4) { background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); }
        .tag:nth-child(5n)   { background: linear-gradient(90deg, #f7971e 0%, #ffd200 100%); }
        .tag:hover {
            filter: brightness(1.1) saturate(1.2);
            transform: scale(1.08) rotate(-2deg);
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        }
        .tag .fa {
            margin-right: 4px;
        }
        .card {
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .card-header {
            border-radius: 14px 14px 0 0 !important;
            background: linear-gradient(90deg, #e0eafc 0%, #cfdef3 100%);
        }
        .card-body {
            border-radius: 0 0 14px 14px;
        }
        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #5a5a5a;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .article-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .pagination {
            margin-top: 30px;
        }
        .highlight {
            background-color: #ffc107;
            font-weight: bold;
            padding: 0 2px;
        }
        /* Markdown样式 */
        .markdown-body {
            padding: 15px;
            line-height: 1.8;
            font-size: 16px;
        }
        .markdown-body pre {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .markdown-body img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }
        .markdown-body blockquote {
            border-left: 4px solid #dfe2e5;
            padding-left: 15px;
            color: #6a737d;
            margin: 15px 0;
        }
        .markdown-body table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .markdown-body table th,
        .markdown-body table td {
            border: 1px solid #dfe2e5;
            padding: 8px;
        }
        .markdown-body table th {
            background-color: #f6f8fa;
        }
        @yield('additional-styles')
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/app/acms">CMS系统</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ strpos(request()->uri(), '/app/acms') === 0 && request()->uri() === '/app/acms' ? 'active' : '' }}" href="/app/acms">首页</a>
                </li>
                @isset($categoryTree)
                    @foreach($categoryTree as $navCategory)
                        @if(isset($navCategory['children']) && count($navCategory['children']) > 0)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ (isset($category) && $category->id == $navCategory['id']) || (isset($article) && $article->category_id == $navCategory['id']) ? 'active' : '' }}" href="/app/acms/category/{{$navCategory['id']}}" id="navbarDropdown{{$navCategory['id']}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{$navCategory['name']}}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown{{$navCategory['id']}}">
                                    @foreach($navCategory['children'] as $child)
                                        <li><a class="dropdown-item" href="/app/acms/category/{{$child['id']}}">{{$child['name']}}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ (isset($category) && $category->id == $navCategory['id']) || (isset($article) && $article->category_id == $navCategory['id']) ? 'active' : '' }}" href="/app/acms/category/{{$navCategory['id']}}">{{$navCategory['name']}}</a>
                            </li>
                        @endif
                    @endforeach
                @endisset
            </ul>
            <form class="d-flex" action="/app/acms/search" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="keyword" placeholder="搜索文章..." aria-label="搜索" value="{{ $keyword ?? '' }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>

    <!-- 内容区域 -->
    <div class="container my-4">
        <div class="row">
            <!-- 主内容区 -->
            <div class="col-lg-8">
                @yield('content')
            </div>
            
            <!-- 侧边栏 -->
            <div class="col-lg-4">
                @section('sidebar')
                    <!-- 分类列表 -->
                    @isset($categories)
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">分类列表</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                @foreach($categories as $sidebarCategory)
                                <li class="sidebar-item">
                                    <a href="/app/acms/category/{{$sidebarCategory->id}}" class="text-decoration-none text-dark {{ (isset($category) && $category->id == $sidebarCategory->id) || (isset($article) && $article->category_id == $sidebarCategory->id) ? 'fw-bold text-primary' : '' }}">
                                        <i class="fa fa-folder-open"></i>
                                        {{$sidebarCategory->name}} 
                                        <span class="badge bg-light text-dark ms-2"><i class="fa fa-file-alt me-1"></i>{{$sidebarCategory->articles_count ?? 0}}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endisset
                    
                    <!-- 标签云 -->
                    @isset($tags)
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">标签云</h5>
                        </div>
                        <div class="card-body">
                            <div class="tag-cloud">
                                @foreach($tags as $sidebarTag)
                                <a href="/app/acms/tag/{{$sidebarTag->id}}" class="tag text-decoration-none">
                                    <i class="fa fa-tag"></i> {{$sidebarTag->name}}
                                    @if(isset($sidebarTag->articles_count))
                                    <span style="font-size:0.8em;opacity:0.85;">({{$sidebarTag->articles_count}})</span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endisset
                @show
            </div>
        </div>
    </div>

    <script src="/app/acms/js/jquery.min.js"></script>
    <script src="/app/acms/js/bootstrap.bundle.min.js"></script>
    <!-- Markdown解析库 -->
    <script src="/app/acms/js/marked.min.js"></script>
    <script src="/app/acms/js/highlight.min.js"></script>
    <script>
        // 配置marked使用highlight.js进行代码高亮
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, {language: lang}).value;
                } else {
                    return hljs.highlightAuto(code).value;
                }
            },
            breaks: true,
            gfm: true
        });
        
        // 对具有markdown-content类的元素进行解析
        document.addEventListener('DOMContentLoaded', function() {
            const markdownElements = document.querySelectorAll('.markdown-content');
            markdownElements.forEach(function(element) {
                const markdown = element.getAttribute('data-content');
                if (markdown) {
                    element.innerHTML = marked.parse(markdown);
                    // 代码高亮
                    element.querySelectorAll('pre code').forEach((block) => {
                        hljs.highlightElement(block);
                    });
                }
            });
        });

        // 让导航栏下拉菜单点击展开/收起，兼容PC和移动端
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownToggles = document.querySelectorAll('.nav-item.dropdown > .dropdown-toggle');
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    var parent = this.parentElement;
                    var menu = parent.querySelector('.dropdown-menu');
                    // 关闭其它已展开的
                    document.querySelectorAll('.nav-item.dropdown .dropdown-menu.show').forEach(function(m) {
                        if(m!==menu) m.classList.remove('show');
                    });
                    document.querySelectorAll('.nav-item.dropdown .dropdown-toggle.show').forEach(function(t) {
                        if(t!==this) t.classList.remove('show');
                    });
                    // 切换当前
                    this.classList.toggle('show');
                    menu.classList.toggle('show');
                });
            });
            // 点击空白处关闭
            document.addEventListener('click', function(e) {
                if(!e.target.closest('.nav-item.dropdown')) {
                    document.querySelectorAll('.nav-item.dropdown .dropdown-menu.show').forEach(function(m) {
                        m.classList.remove('show');
                    });
                    document.querySelectorAll('.nav-item.dropdown .dropdown-toggle.show').forEach(function(t) {
                        t.classList.remove('show');
                    });
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>