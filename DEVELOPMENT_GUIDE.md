# ACMS 插件开发规范

## 1. 项目概述

ACMS 是基于 WebMan Framework 开发的内容管理系统插件，提供文章、分类、标签、评论的完整管理功能。

### 1.1 核心功能模块

| 模块 | 功能说明 | 路由前缀 |
|------|----------|----------|
| 文章管理 | 文章的增删改查、置顶、推荐 | `/app/admin/acms/article` |
| 分类管理 | 分类树结构管理 | `/app/admin/acms/category` |
| 标签管理 | 标签的增删改查 | `/app/admin/acms/tag` |
| 评论管理 | 评论审核、回复管理 | `/app/admin/acms/comment` |
| 前台展示 | 文章列表、详情、评论 | `/app/acms` |
| 用户中心 | 收藏、历史、评论 | `/app/acms/user` |

### 1.2 技术栈

- **框架**: WebMan Framework (PHP 8.0+)
- **数据库**: MySQL 8.0+
- **前端**: LayUI 2.x + Editor.md
- **视图引擎**: Blade
- **ORM**: Illuminate Database

---

## 2. 目录结构规范

```
plugin/acms/
├── api/                    # 插件API接口（供系统调用）
│   ├── Category.php        # 分类相关API
│   └── Install.php         # 安装相关API
├── app/                    # 应用核心代码
│   ├── admin/              # 后台管理模块
│   │   ├── controller/     # 后台控制器
│   │   └── view/           # 后台视图
│   ├── controller/         # 前台控制器
│   ├── middleware/         # 中间件
│   ├── model/              # 数据模型
│   ├── service/            # 服务层
│   ├── view/               # 前台视图
│   │   ├── components/     # 可复用组件
│   │   ├── index/          # 首页相关
│   │   ├── layouts/        # 布局模板
│   │   └── user/           # 用户中心
│   └── functions.php       # 全局辅助函数
├── config/                 # 配置文件
│   ├── app.php             # 插件基本配置
│   ├── route.php           # 路由配置
│   ├── middleware.php      # 中间件配置
│   ├── menu.php            # 菜单配置
│   ├── view.php            # 视图配置
│   ├── event.php           # 事件配置
│   ├── container.php       # 容器配置
│   ├── autoload.php        # 自动加载配置
│   ├── exception.php       # 异常处理配置
│   ├── process.php         # 进程配置
│   └── static.php          # 静态资源配置
├── public/                 # 静态资源
│   ├── css/                # 样式文件
│   └── editor.md/          # Markdown编辑器
├── install.sql             # 数据库初始化脚本
└── .gitignore             # Git忽略规则
```

---

## 3. 命名规范

### 3.1 目录与文件命名

| 类型 | 规范 | 示例 |
|------|------|------|
| 目录名 | 小写蛇形命名 | `controller/`, `view/components/` |
| 类文件 | 大驼峰命名 | `ArticleController.php`, `UserLike.php` |
| 视图文件 | 小写蛇形命名 | `index.blade.php`, `edit.blade.php` |
| 配置文件 | 小写蛇形命名 | `route.php`, `middleware.php` |

### 3.2 类与方法命名

| 类型 | 规范 | 示例 |
|------|------|------|
| 控制器类 | 大驼峰 + Controller 后缀 | `ArticleController` |
| 模型类 | 大驼峰（单数形式） | `Article`, `Category` |
| 服务类 | 大驼峰 + Service 后缀 | `UserEvent` |
| 方法名 | 小驼峰命名 | `getList()`, `changeStatus()` |
| 动作方法 | RESTful 风格 | `index()`, `show()`, `store()`, `update()`, `destroy()` |

### 3.3 数据库命名

| 类型 | 规范 | 示例 |
|------|------|------|
| 表名 | `acms_` 前缀 + 小写蛇形（复数） | `acms_articles`, `acms_categories` |
| 字段名 | 小写蛇形命名 | `article_id`, `created_at` |
| 主键 | `id`（自增整型） | `id INT UNSIGNED AUTO_INCREMENT` |
| 外键 | `{表名}_id` | `category_id`, `user_id` |

---

## 4. 代码风格规范

### 4.1 PHP 代码规范

**基础规则：**
- 使用 PHP 8.0+ 特性（类型声明、箭头函数、空合并运算符）
- 文件编码：UTF-8（无 BOM）
- 行尾：LF
- 缩进：4 个空格

**类定义规范：**

```php
<?php

namespace plugin\acms\app\controller;

use plugin\acms\app\model\Article;
use support\Request;
use support\Response;

class ArticleController
{
    /**
     * 文章列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // 业务逻辑
    }
}
```

**方法参数规范：**

| 参数类型 | 说明 |
|----------|------|
| `$request` | 必须为 `support\Request` 类型 |
| `$id` | 资源ID，必须为 `int` 类型 |
| 返回值 | 必须声明返回类型 `Response` |

### 4.2 控制器规范

**RESTful 资源控制器方法：**

| 方法 | HTTP 方法 | 路由 | 功能说明 |
|------|----------|------|----------|
| `index()` | GET | `/article/index` | 列表页/数据接口 |
| `add()` | GET | `/article/add` | 添加页面 |
| `save()` | POST | `/article/save` | 保存数据 |
| `edit(int $id)` | GET | `/article/edit/{id}` | 编辑页面 |
| `update(int $id)` | POST | `/article/update/{id}` | 更新数据 |
| `delete()` | POST | `/article/delete` | 删除数据 |

**响应格式规范：**

```php
// 成功响应
return json(['code' => 0, 'msg' => '操作成功', 'data' => $data]);

// 失败响应
return json(['code' => 1, 'msg' => '操作失败']);
```

### 4.3 模型规范

```php
<?php

namespace plugin\acms\app\model;

use support\Model;

/**
 * @property integer $id 主键
 * @property string $title 标题
 * @property string $content 内容
 * @property string $created_at 创建时间
 */
class Article extends Model
{
    protected $connection = 'plugin.admin.mysql';
    protected $table = 'acms_articles';
    protected $primaryKey = 'id';
    
    // 关联关系
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
```

### 4.4 视图规范

**Blade 模板约定：**

```blade
{{-- 变量输出（自动转义） --}}
{{ $article->title }}

{{-- 原始HTML输出 --}}
{!! $article->content !!}

{{-- 条件判断 --}}
@if($article->status)
    <span>显示</span>
@else
    <span>隐藏</span>
@endif

{{-- 循环遍历 --}}
@foreach($articles as $article)
    <div>{{ $article->title }}</div>
@endforeach
```

---

## 5. 数据库设计规范

### 5.1 表结构设计原则

| 原则 | 说明 |
|------|------|
| 前缀统一 | 所有表使用 `acms_` 前缀 |
| 字段类型 | 使用合适的数据类型（`VARCHAR` 限长，`TEXT` 存大文本） |
| 索引优化 | 外键、查询字段必须建索引 |
| 时间字段 | 统一使用 `timestamp(0)` 类型 |
| 软删除 | 不使用软删除，直接物理删除 |

### 5.2 核心表结构

**文章表 (`acms_articles`)：**

| 字段 | 类型 | 约束 | 说明 |
|------|------|------|------|
| `id` | INT UNSIGNED | PRIMARY KEY AUTO_INCREMENT | 主键 |
| `title` | VARCHAR(255) | NOT NULL | 文章标题 |
| `content` | TEXT | NOT NULL | 文章内容 |
| `summary` | VARCHAR(500) | NULL | 文章摘要 |
| `category_id` | INT | NOT NULL DEFAULT 0 | 分类ID |
| `views` | INT | NOT NULL DEFAULT 0 | 浏览量 |
| `likes` | INT | NOT NULL DEFAULT 0 | 点赞数 |
| `is_top` | TINYINT(1) | NOT NULL DEFAULT 0 | 是否置顶 |
| `status` | TINYINT(1) | NOT NULL DEFAULT 1 | 状态 |
| `created_at` | TIMESTAMP(0) | NULL | 创建时间 |
| `updated_at` | TIMESTAMP(0) | NULL | 更新时间 |

**分类表 (`acms_categories`)：**

| 字段 | 类型 | 约束 | 说明 |
|------|------|------|------|
| `id` | INT UNSIGNED | PRIMARY KEY AUTO_INCREMENT | 主键 |
| `name` | VARCHAR(100) | NOT NULL UNIQUE | 分类名称 |
| `slug` | VARCHAR(100) | NULL UNIQUE | 分类别名 |
| `parent_id` | INT | NOT NULL DEFAULT 0 | 父级ID |
| `sort` | INT | NOT NULL DEFAULT 0 | 排序值 |
| `status` | TINYINT(1) | NOT NULL DEFAULT 1 | 状态 |

**评论表 (`acms_comments`)：**

| 字段 | 类型 | 约束 | 说明 |
|------|------|------|------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY AUTO_INCREMENT | 主键 |
| `content` | TEXT | NOT NULL | 评论内容 |
| `user_id` | BIGINT UNSIGNED | NOT NULL | 用户ID |
| `article_id` | BIGINT UNSIGNED | NOT NULL | 文章ID |
| `parent_id` | BIGINT UNSIGNED | NOT NULL DEFAULT 0 | 父评论ID |
| `status` | TINYINT | NOT NULL DEFAULT 0 | 状态（0待审核，1已审核） |

---

## 6. 路由规范

### 6.1 路由分组

```php
<?php

// 前台路由
Route::group('/app/acms', function () {
    Route::get('', [IndexController::class, 'list']);
    Route::get('/article/{id}', [IndexController::class, 'detail']);
});

// 后台路由
Route::group('/app/admin/acms', function () {
    Route::group('/article', function () {
        Route::get('/index', [ArticleController::class, 'index']);
        Route::post('/save', [ArticleController::class, 'save']);
    });
});
```

### 6.2 命名约定

| 场景 | 约定 |
|------|------|
| 列表页 | `GET /{module}/index` |
| 添加页 | `GET /{module}/add` |
| 保存 | `POST /{module}/save` |
| 编辑页 | `GET /{module}/edit/{id}` |
| 更新 | `POST /{module}/update/{id}` |
| 删除 | `POST /{module}/delete` |

---

## 7. 安全规范

### 7.1 输入验证

```php
public function save(Request $request): Response
{
    $data = $request->post();
    
    // 必填字段验证
    if (empty($data['title'])) {
        return json(['code' => 1, 'msg' => '标题不能为空']);
    }
    
    // 字段过滤（仅允许指定字段）
    $allowedFields = ['title', 'content', 'category_id', ...];
    foreach ($data as $key => $value) {
        if (!in_array($key, $allowedFields)) {
            unset($data[$key]);
        }
    }
}
```

### 7.2 SQL 注入防护

- 必须使用 ORM 提供的参数绑定
- 禁止拼接 SQL

```php
// 正确做法
$query->where('title', 'like', "%{$keyword}%");

// 错误做法（禁止）
$query->whereRaw("title LIKE '%$keyword%'");
```

### 7.3 XSS 防护

- 前端渲染使用 `{{ }}` 自动转义
- 富文本内容需使用 HTML 过滤

```blade
{{-- 自动转义（安全） --}}
{{ $article->title }}

{{-- 原始输出（需确保内容安全） --}}
{!! $article->content !!}
```

### 7.4 CSRF 防护

已通过中间件 `CsrfTokenCheck` 自动处理，无需手动添加。

---

## 8. 最佳实践

### 8.1 代码复用

**使用服务层处理复杂业务：**

```php
// app/service/UserEvent.php
class UserEvent
{
    public static function recordHistory($userId, $articleId)
    {
        UserHistory::updateOrCreate(
            ['user_id' => $userId, 'article_id' => $articleId],
            ['updated_at' => date('Y-m-d H:i:s')]
        );
    }
}
```

### 8.2 性能优化

- 使用 `select()` 指定查询字段，避免 `SELECT *`
- 使用 `with()` 预加载关联数据，避免 N+1 查询

```php
// 正确做法：预加载关联
$articles = Article::with(['category', 'tags'])
    ->select(['id', 'title', 'category_id', 'created_at'])
    ->paginate();

// 避免：循环中查询关联
foreach ($articles as $article) {
    $category = $article->category; // N+1 查询
}
```

### 8.3 错误处理

```php
try {
    $article->save();
    return json(['code' => 0, 'msg' => '操作成功']);
} catch (\Exception $e) {
    // 记录日志（生产环境）
    // Log::error($e->getMessage());
    return json(['code' => 1, 'msg' => '操作失败: ' . $e->getMessage()]);
}
```

### 8.4 日志规范

```php
use support\Log;

Log::info('文章创建成功', ['article_id' => $article->id]);
Log::error('文章保存失败', ['error' => $e->getMessage(), 'data' => $data]);
```

---

## 9. 开发流程

```
1. 需求分析 → 2. 数据库设计 → 3. 模型开发 → 4. 控制器开发 → 5. 视图开发 → 6. 测试验证
```

### 9.1 Git 提交规范

| 类型 | 说明 | 示例 |
|------|------|------|
| `feat` | 新增功能 | `feat: 添加文章点赞功能` |
| `fix` | 修复 bug | `fix: 修复评论审核状态不更新` |
| `docs` | 文档更新 | `docs: 更新 API 文档` |
| `style` | 代码格式调整 | `style: 格式化代码` |
| `refactor` | 代码重构 | `refactor: 重构文章列表查询` |
| `test` | 测试用例 | `test: 添加文章单元测试` |

---

## 10. 部署与维护

### 10.1 环境要求

| 环境 | 版本要求 |
|------|----------|
| PHP | 8.0+ |
| MySQL | 8.0+ |
| WebMan | 1.5+ |

### 10.2 安装步骤

1. 将插件目录放入 `plugin/`
2. 执行数据库初始化脚本 `install.sql`
3. 配置数据库连接信息
4. 重启 WebMan 服务

### 10.3 目录权限

```bash
chmod -R 755 plugin/acms/
chown -R www-data:www-data plugin/acms/public/
```

---

## 附录：核心配置示例

### A. 路由配置 (`config/route.php`)

```php
use plugin\acms\app\admin\controller\ArticleController;
use Webman\Route;

Route::group('/app/admin/acms', function () {
    Route::group('/article', function () {
        Route::get('/index', [ArticleController::class, 'index']);
        Route::post('/save', [ArticleController::class, 'save']);
        Route::post('/delete', [ArticleController::class, 'delete']);
    });
});
```

### B. 中间件配置 (`config/middleware.php`)

```php
use plugin\acms\app\middleware\CsrfTokenCheck;
use plugin\admin\api\Middleware as AdminMiddleware;

return [
    '' => [
        new CsrfTokenCheck(['admin']),
    ],
    'admin' => [
        AdminMiddleware::class
    ]
];
```

---

**版本**: v1.0.1  
**最后更新**: 2025-04-29  
**作者**: Ouyangyi