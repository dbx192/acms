# ACMS 内容管理系统插件

ACMS（Advanced Content Management System）是基于 Webman 框架开发的内容管理系统插件，支持文章、分类、标签、评论等常用 CMS 功能，适用于技术博客、资讯站点等内容型网站。

## 主要功能

- 文章管理：发布、编辑、删除、置顶、推荐、状态切换
- 分类管理：多级分类，支持 SEO 字段
- 标签管理：标签增删改查，文章标签关联
- 评论管理：评论审核、回复、删除、点赞功能
- 前台展示：文章列表、详情、分类、标签、搜索、评论（需登录）
- 后台管理：基于 Webman Admin，Webman User支持权限与菜单集成

## 安装方法

1. **解压插件**
   将 `plugin/acms.zip` 解压到 `plugin/acms/` 目录下，或直接将源码放入 `plugin/acms/`。

2. **导入数据库**
   执行 `php webman app-plugin:install acms`，创建所需数据表和初始数据。或手动执行 `plugin/acms/install.sql`。

3. **注册路由**
   插件自带路由文件 `plugin/acms/config/route.php`，Webman 会自动加载。

4. **注册菜单（可选）**
   插件自带菜单配置 `plugin/acms/config/menu.php`，安装时会自动导入到后台菜单。

5. **访问后台**
   后台地址：`/app/admin/acms/article/index`  
   前台地址：`/app/acms`

## 目录结构

```
plugin/
  acms/
    install.sql                // 数据库结构及初始数据
    readme.md                  // 插件说明文档
    api/                       // API 相关代码
    app/                       // 控制器、模型、服务等
    config/                    // 路由、菜单等配置
    public/                    // 静态资源
```

## 路由说明

- 前台路由前缀：`/app/acms`
  - 文章列表：`/app/acms`
  - 文章详情：`/app/acms/article/{id}`
  - 分类页：`/app/acms/category/{id}`
  - 标签页：`/app/acms/tag/{id}`
  - 搜索页：`/app/acms/search`
  - 评论提交：`/app/acms/comment/add`

- 后台路由前缀：`/app/admin/acms`
  - 文章管理：`/app/admin/acms/article/index`
  - 分类管理：`/app/admin/acms/category/index`
  - 标签管理：`/app/admin/acms/tag/index`
  - 评论管理：`/app/admin/acms/comment/index`

## 常见问题

### 1. 后台菜单/页面404

- 请确认 `plugin/acms/config/menu.php` 和 `plugin/acms/config/route.php` 路径均为 `/app/admin/acms/xxx`。
- 清理 Webman 缓存（删除 `runtime/` 下缓存文件）。
- 数据库表和初始数据已正确导入。

### 2. 路由冲突或无效

- 检查 `config/route.php` 是否有同名路由冲突。
- 确认插件目录名为 `acms`，并与路由、菜单配置一致。

### 3. 数据库连接失败

- 检查 `config/database.php` 数据库配置，确保与实际环境一致。

### 4. mysql8.0以下版本
install.sql中utf8mb4_0900_ai_ci改为utf8mb4_general_ci

### 5. webman安装laravel组件环境
blade模板引擎

### 6. 分页
composer require jasongrimes/paginator

### 7. csrf
- csrf_token、csrf_field
- 新增autoload.php + functions.php(自定义了简单的函数替代）


## 二次开发建议

- 控制器、模型、视图均遵循 Webman 规范，可直接扩展。
- 如需自定义菜单、权限，可修改 `config/menu.php`。
- 前端页面可在 `view/` 目录下自定义。
- 支持批量查询优化，提升多数据查询性能
- 新增文章ID关联功能，便于内容聚合
- 分类、菜单等支持多级嵌套，菜单渲染支持动态层级。

## 贡献与反馈

如有建议或问题，欢迎提交 Issue 或 PR。

---

**作者**：ouyangyi  
**开源协议**：MIT
