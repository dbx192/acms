ACMS Content Management System Plugin

ACMS (Advanced Content Management System) is a content management system plugin developed based on the Webman framework. It supports common CMS features such as articles, categories, tags, and comments, making it suitable for content-based websites like technical blogs and news portals.

Key Features

• Article Management: Publish, edit, delete, pin, recommend, and toggle status

• Category Management: Multi-level categories with SEO field support

• Tag Management: Add, edit, delete, and search tags, with article-tag associations

• Comment Management: Comment review, reply, delete, and like functionality

• Frontend Display: Article lists, details, categories, tags, search, and comments (login required)

• Admin Panel: Based on Webman Admin, with Webman User support for permission and menu integration


Installation

1. Extract the Plugin  
   Unzip `plugin/acms.zip` into the `plugin/acms/` directory, or place the source code directly in `plugin/acms/`.

2. Import Database  
   Run `php webman app-plugin:install acms` to create the required database tables and initial data. Alternatively, manually execute `plugin/acms/install.sql`.

3. Register Routes  
   The plugin includes a route file `plugin/acms/config/route.php`, which Webman will load automatically.

4. Register Menus (Optional)  
   The plugin includes menu configuration `plugin/acms/config/menu.php`, which will be automatically imported into the admin panel during installation.

5. Access Admin Panel  
   Admin URL: `/app/admin/acms/article/index`  
   Frontend URL: `/app/acms`

Directory Structure

```
plugin/
  acms/
    install.sql                // Database schema and initial data
    readme.md                  // Plugin documentation
    api/                       // API-related code
    app/                       // Controllers, models, services, etc.
    config/                    // Route, menu, and other configurations
    public/                    // Static assets
```

Route Configuration

• Frontend Route Prefix: `/app/acms`

  • Article List: `/app/acms`

  • Article Details: `/app/acms/article/{id}`

  • Category Page: `/app/acms/category/{id}`

  • Tag Page: `/app/acms/tag/{id}`

  • Search Page: `/app/acms/search`

  • Comment Submission: `/app/acms/comment/add`


• Admin Route Prefix: `/app/admin/acms`

  • Article Management: `/app/admin/acms/article/index`

  • Category Management: `/app/admin/acms/category/index`

  • Tag Management: `/app/admin/acms/tag/index`

  • Comment Management: `/app/admin/acms/comment/index`


FAQ

1. Admin Menu/Page Returns 404

• Ensure paths in `plugin/acms/config/menu.php` and `plugin/acms/config/route.php` follow `/app/admin/acms/xxx`.

• Clear Webman cache (delete cached files in `runtime/`).

• Verify database tables and initial data are correctly imported.


2. Route Conflicts or Invalid Routes

• Check `config/route.php` for duplicate route names.

• Ensure the plugin directory is named `acms` and matches route and menu configurations.


3. Database Connection Failed

• Verify database settings in `config/database.php` match your environment.


Custom Development Suggestions

• Controllers, models, and views follow Webman conventions and can be extended directly.

• Modify `config/menu.php` for custom menus and permissions.

• Customize frontend pages in the `view/` directory.

• Supports batch query optimization for improved performance with large datasets.

• Added article ID association for content aggregation.

• Supports multi-level nesting for categories and menus, with dynamic menu rendering.


Contributions & Feedback

For suggestions or issues, please submit an Issue or PR.

---

Author: ouyangyi  
License: MIT