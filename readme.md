# ACMS 内容管理系统插件

ACMS（Advanced Content Management System）是基于 Webman 框架开发的内容管理系统插件，支持文章、分类、标签、评论等功能，适用于技术博客、资讯站点等内容型网站。

## 概述

ACMS 提供了一套完整的内容管理解决方案，集成 Webman Admin 和 Webman User，支持权限管理、菜单配置及前后台功能，适用于快速搭建内容型网站。

## 主要功能

- **文章管理**：支持发布、编辑、删除、置顶、推荐及状态切换。
- **分类管理**：支持多级分类，包含 SEO 字段配置。
- **标签管理**：支持标签增删改查及文章标签关联。
- **评论管理**：支持评论审核、回复、删除及点赞功能。
- **前台展示**：提供文章列表、详情、分类、标签、搜索及评论功能（需登录）。
- **后台管理**：基于 Webman Admin，集成权限与菜单管理。

## 安装步骤

### 环境要求
- 已安装 Webman Admin 和 Webman User 插件。
- 安装以下扩展（用于 Blade 模板和分页）：
  ```bash
  composer require psr/container webman/blade jasongrimes/paginator
  ```

### 安装流程
**直接在admin插件中心安装即可。这是最简单和方便的！（推荐）**

下面是手动安装的方法：
1. **解压插件**
   将 `plugin/acms.zip` 解压到 `plugin/acms/` 目录，或直接将源码放入 `plugin/acms/`。

2. **导入数据库**
   执行以下命令创建数据表和初始数据：
   ```bash
   php webman app-plugin:install acms
   ```
   或手动执行 `plugin/acms/install.sql`。

3. **注册路由**
   插件自带路由文件 `plugin/acms/config/route.php`，Webman 会自动加载。

4. **注册菜单（可选）**
   插件自带菜单配置 `plugin/acms/config/menu.php`，安装时自动导入后台菜单。

5. **访问系统**
   - 后台地址：`/app/admin/acms/article/index`
   - 前台地址：`/app/acms`

## 目录结构

```
plugin/acms/
├── install.sql           // 数据库结构及初始数据
├── readme.md            // 插件说明文档
├── api/                // API 相关代码
├── app/                // 控制器、模型、服务等
├── config/             // 路由、菜单等配置
├── public/             // 静态资源
├── view/               // Blade 模板视图
```

## 路由说明

### 前台路由（前缀：`/app/acms`）
- 文章列表：`/app/acms`
- 文章详情：`/app/acms/article/{id}`
- 分类页：`/app/acms/category/{id}`
- 标签页：`/app/acms/tag/{id}`
- 搜索页：`/app/acms/search`
- 评论提交：`/app/acms/comment/add`

### 后台路由（前缀：`/app/admin/acms`）
- 文章管理：`/app/admin/acms/article/index`
- 分类管理：`/app/admin/acms/category/index`
- 标签管理：`/app/admin/acms/tag/index`
- 评论管理：`/app/admin/acms/comment/index`

## 常见问题
1. **后台菜单或页面 404**
   - 确保 `plugin/acms/config/menu.php` 和 `plugin/acms/config/route.php` 路径正确，均为 `/app/admin/acms/xxx`。
   - 清理 Webman 缓存：删除 `runtime/` 目录下的缓存文件。
   - 确认数据库表和初始数据已正确导入。

2. **路由冲突或无效**
   - 检查 `config/route.php` 是否存在同名路由冲突。
   - 确保插件目录名为 `acms`，并与路由、菜单配置一致。

3. **数据库连接失败**
   - 检查 `config/database.php` 数据库配置，确保与实际环境一致。

4. **MySQL 8.0 以下版本兼容性**
   - 在 `install.sql` 中，将 `utf8mb4_0900_ai_ci` 替换为 `utf8mb4_general_ci`。

5. **CSRF 保护**
   - 使用 `csrf_token` 和 `csrf_field`。
   - 已新增 `autoload.php` 和 `functions.php`，提供简化的自定义函数替代。

6. **分页支持**
   - 确保已安装分页扩展：
     ```bash
     composer require jasongrimes/paginator
     ```

## 二次开发建议

- **扩展性**：控制器、模型、视图遵循 Webman 规范，可直接扩展。
- **菜单与权限**：可通过修改 `config/menu.php` 自定义菜单和权限。
- **前端页面**：在 `view/` 目录下自定义 Blade 模板。
- **性能优化**：支持批量查询，优化多数据查询性能。
- **功能增强**：
  - 新增文章 ID 关联功能，便于内容聚合。
  - 支持多级分类和菜单，动态渲染层级。

## 贡献与反馈

欢迎提交 Issue 或 PR，提供建议或报告问题。
github: https://github.com/dbx192/acms

## 作者与协议

- **作者**：ouyangyi
- **开源协议**：MIT