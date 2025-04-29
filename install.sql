/*
 Navicat Premium Data Transfer

 Source Server         : 159.75.104.212
 Source Server Type    : MySQL
 Source Server Version : 80036
 Source Host           : 159.75.104.212:3306
 Source Schema         : base_webman

 Target Server Type    : MySQL
 Target Server Version : 80036
 File Encoding         : 65001

 Date: 29/04/2025 16:40:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for acms_article_tag
-- ----------------------------
DROP TABLE IF EXISTS `acms_article_tag`;
CREATE TABLE `acms_article_tag`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `article_id` int(0) NOT NULL COMMENT '文章ID',
  `tag_id` int(0) NOT NULL COMMENT '标签ID',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `article_tag`(`article_id`, `tag_id`) USING BTREE,
  INDEX `tag_id`(`tag_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '文章标签关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of acms_article_tag
-- ----------------------------
INSERT INTO `acms_article_tag` VALUES (1, 1, 2);

-- ----------------------------
-- Table structure for acms_articles
-- ----------------------------
DROP TABLE IF EXISTS `acms_articles`;
CREATE TABLE `acms_articles`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '文章标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '文章内容',
  `summary` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '文章摘要',
  `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '缩略图',
  `category_id` int(0) NOT NULL DEFAULT 0 COMMENT '分类ID',
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '标签',
  `views` int(0) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `likes` int(0) NOT NULL DEFAULT 0 COMMENT '点赞数',
  `is_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否置顶',
  `is_recommend` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 0隐藏 1显示',
  `sort` int(0) NOT NULL DEFAULT 0 COMMENT '排序值',
  `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'SEO关键词',
  `seo_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'SEO描述',
  `created_at` timestamp(0) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp(0) NULL DEFAULT NULL COMMENT '更新时间',
  `user_id` int(0) NOT NULL DEFAULT 0 COMMENT '发布者ID',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '文章类型，1markdown,2html',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `category_id`(`category_id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `is_top`(`is_top`) USING BTREE,
  INDEX `is_recommend`(`is_recommend`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '文章表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of acms_articles
-- ----------------------------
INSERT INTO `acms_articles` VALUES (1, 'vscode快捷键', '以下是 VS Code 默认快捷键的系统差异对照表（一行一条）：  \r\n\r\n| 操作                     | Windows/Linux       | macOS               | 备注                     |\r\n|------------------------------|-------------------------|-------------------------|-----------------------------|\r\n| 打开用户设置                 | `Ctrl + ,`              | `⌘ + ,`                 |                             |\r\n| 打开快捷键设置               | `Ctrl + K Ctrl + S`      | `⌘ + K ⌘ + S`           | 需按组合顺序操作            |\r\n| 打开/关闭终端                | `` Ctrl + ` ``          | `` ⌘ + ` ``             | 反引号键                    |\r\n| 新建终端                     | `Ctrl + Shift + \\``     | `⌘ + Shift + \\``        |                             |\r\n| 选中所有相同单词             | `Ctrl + F2`             | `⌘ + F2`                | 类似 Sublime 的 `Alt + F3` |\r\n| 重命名变量/方法              | `F2`                    | `F2`                    | 部分 Mac 需 `fn + F2`       |\r\n| 复制当前行                   | `Alt + Shift + ↓/↑`     | `⌥ + Shift + ↓/↑`       |                             |\r\n| 跳转到某行                   | `Ctrl + G`              | `⌘ + G`                 |                             |\r\n| 文件头/文件尾                | `Ctrl + Home/End`       | `⌘ + ↑/↓`               | Mac 无直接 `Home/End` 键    |\r\n| 返回/前进历史位置            | `Alt + ←/→`             | `⌥ + ←/→`               |                             |\r\n| 查找当前文件方法             | `Ctrl + Shift + O`      | `⌘ + Shift + O`         |                             |\r\n| 全局符号搜索                 | `Ctrl + T`              | `⌘ + T`                 |                             |\r\n| 查看定义                     | `F12`                   | `F12`                   | 部分 Mac 需 `fn + F12`      |\r\n| 提取方法                     | `Ctrl + .`              | `⌘ + .`                 | 需选中代码后触发           |\r\n| 快速打开文件                 | `Ctrl + P`              | `⌘ + P`                 |                             |\r\n| 关闭当前文件                 | `Ctrl + W`              | `⌘ + W`                 |                             |\r\n| 切换标签页                   | `Ctrl + PageUp/PageDown`| `⌘ + Option + ←/→`       | Mac 无 `PageUp/PageDown`    |\r\n| 跳转到下一个错误             | `F8`                    | `F8`                    | 部分 Mac 需 `fn + F8`       |\r\n\r\n**注意事项**：\r\n1. Mac 功能键：`F1-F12` 默认绑定系统功能（如亮度调节），需配合 `fn` 键（或修改系统设置）。  \r\n2. Linux 差异：部分键盘可能需要 `Fn + Home/End` 或自定义映射。  \r\n3. 符号替代：  \r\n   • `⌘` = Command (`Cmd`)  \r\n\r\n   • `⌥` = Option (`Alt`)  \r\n\r\n   • `⌃` = Control (`Ctrl`)  \r\n\r\n\r\n表格严格遵循 官方默认键位，无自定义内容。', 'MySQL索引优化核心要点', '', 1, '测试,技术', 32, 41, 1, 1, 1, 1, '数据库优化指南', 'MySQL,索引,性能', '数据库优化技术文章', '2025-04-28 17:41:33', '2025-04-29 14:11:19', 1, 1);
INSERT INTO `acms_articles` VALUES (2, '2025科技趋势速览', '人工智能与量子计算将引领未来十年。', '前沿科技发展趋势分析', '/images/news-thumb.jpg', 2, '新闻,科技', 66, 75, 1, 1, 1, 5, '年度科技趋势', 'AI,量子计算', '2025年重点科技领域预测', '2025-04-28 17:41:33', '2025-04-29 14:10:00', 1, 1);
INSERT INTO `acms_articles` VALUES (3, '高效编程实践', '注重代码可读性与模块化设计。', '编程规范的重要性', NULL, 1, '编程', 127, 11, 0, 0, 1, 20, NULL, NULL, NULL, '2025-04-28 17:41:33', '2025-04-28 19:21:49', 1, 1);
INSERT INTO `acms_articles` VALUES (5, '健康生活小贴士', '每日适量运动与均衡饮食是关键。', '生活方式健康建议', '/images/health-thumb.png', 3, '健康,生活', 30, 14, 0, 0, 1, 15, '健康指南', '运动,营养', NULL, '2025-04-28 17:41:33', '2025-04-29 11:04:27', 1, 1);
INSERT INTO `acms_articles` VALUES (12, '前端框架对比分析', 'React与Vue的核心差异比较。', '主流框架技术特点', '/images/tech.png', 1, '前端,框架', 157, 40, 1, 1, 1, 8, '框架技术选型', 'React,Vue', '前端开发框架对比', '2025-04-28 17:41:33', '2025-04-29 16:27:06', 1, 1);
INSERT INTO `acms_articles` VALUES (13, '企业数字化转型', '传统行业如何实现数据驱动决策。', '数字化升级路径', NULL, 2, '商业,技术', 57, 4, 0, 1, 1, 12, '企业转型策略', '数字化,大数据', '企业创新方法论', '2025-04-28 17:41:33', '2025-04-29 16:32:48', 1, 1);
INSERT INTO `acms_articles` VALUES (14, '摄影构图基础教学', '三分法与黄金分割的运用技巧。', '摄影入门教程', '/images/photo.jpg', 3, '艺术,摄影', 11, 7, 0, 0, 1, 18, NULL, '构图,技巧', '摄影基础课程', '2025-04-28 17:41:33', '2025-04-29 11:04:30', 1, 1);
INSERT INTO `acms_articles` VALUES (15, '区块链技术原理', '分布式账本与智能合约解析。', '区块链核心机制说明', '/images/blockchain.png', 1, '区块链,技术', 19, 36, 1, 1, 1, 6, '区块链入门', '去中心化,智能合约', '区块链技术详解', '2025-04-28 17:41:33', '2025-04-29 15:52:21', 1, 1);
INSERT INTO `acms_articles` VALUES (16, '环保材料新突破', '可降解塑料研发取得重大进展。', '新材料技术新闻', '/images/environment.jpg', 2, '环保,科技', 47, 48, 0, 0, 1, 22, '环保科技动态', '可降解材料', '新材料研发报道', '2025-04-28 17:41:33', '2025-04-29 15:57:51', 1, 1);
INSERT INTO `acms_articles` VALUES (17, '金融风险管理策略', '系统性风险与对冲工具应用。\n## werfew \nceshi', '金融风控方法论', '', 3, '', 22, 14, 0, 0, 1, 30, '', '', '金融风险控制专题', '2025-04-28 17:41:33', '2025-04-29 14:08:35', 1, 1);
INSERT INTO `acms_articles` VALUES (18, '测试图片', '![](/app/acms/upload/img/20250429/68108056b365.png)\n![](/app/acms/upload/img/20250429/681081967f0b.png)', '士大夫但是', '', 7, '', 13, 0, 0, 0, 1, 0, '', '', '', '2025-04-29 15:37:12', '2025-04-29 16:32:56', 1, 1);

-- ----------------------------
-- Table structure for acms_categories
-- ----------------------------
DROP TABLE IF EXISTS `acms_categories`;
CREATE TABLE `acms_categories`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '分类名称',
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '分类别名',
  `parent_id` int(0) NOT NULL DEFAULT 0 COMMENT '父级ID',
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '分类描述',
  `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '缩略图',
  `sort` int(0) NOT NULL DEFAULT 0 COMMENT '排序值',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 0隐藏 1显示',
  `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'SEO关键词',
  `seo_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'SEO描述',
  `created_at` timestamp(0) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `slug`(`slug`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of acms_categories
-- ----------------------------
INSERT INTO `acms_categories` VALUES (1, '默认分类', 'default', 0, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-04-16 18:38:31', '2025-04-16 18:38:31');
INSERT INTO `acms_categories` VALUES (2, '技术分享', 'tech', 0, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-04-16 18:38:31', '2025-04-16 18:38:31');
INSERT INTO `acms_categories` VALUES (3, '行业资讯', 'news', 0, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-04-16 18:38:31', '2025-04-16 18:38:31');
INSERT INTO `acms_categories` VALUES (4, '编程语言', 'programming', 0, '主流编程语言技术专栏', '/images/code-icon.png', 10, 1, '编程语言学习指南', 'Java,Python,PHP,Go', '涵盖各类编程语言教程与最佳实践', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (5, '前端开发', 'frontend', 0, 'Web前端技术分享', '/images/frontend-icon.png', 20, 1, '前端开发资源大全', 'HTML,CSS,JavaScript,Vue,React', '最新前端框架与开发技巧', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (6, '后端架构', 'backend', 0, '服务器与分布式系统', '/images/server-icon.png', 30, 1, '后端架构设计', '微服务,MySQL,Redis', '高并发系统架构解决方案', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (7, '人工智能', 'ai', 0, '机器学习与深度学习', '/images/ai-icon.png', 40, 1, 'AI技术前沿', 'TensorFlow,PyTorch,神经网络', '人工智能算法与应用案例', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (8, '区块链', 'blockchain', 0, '去中心化技术研究', '/images/blockchain-icon.png', 50, 1, '区块链技术解析', '比特币,以太坊,智能合约', '区块链原理与开发实践', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (9, 'Java开发', 'java', 1, 'Java生态技术栈', '/images/java-icon.png', 11, 1, 'Java工程师成长路径', 'SpringBoot,MyBatis,JVM', '企业级Java开发全攻略', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (10, 'Python实战', 'python', 1, 'Python应用开发', '/images/python-icon.png', 12, 1, 'Python全能开发', 'Django,Flask,爬虫', '从入门到精通的Python教程', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (11, 'PHP编程', 'php', 1, 'Web开发首选语言', '/images/php-icon.png', 13, 1, 'PHP最佳实践', 'Laravel,ThinkPHP', '高性能PHP开发指南', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (12, 'Go语言', 'golang', 1, '云原生开发语言', '/images/go-icon.png', 14, 1, 'Go语言并发编程', 'Gin,微服务,协程', 'Go语言高并发实战', '2025-04-28 17:41:33', '2025-04-28 17:41:33');
INSERT INTO `acms_categories` VALUES (13, 'Rust入门', 'rust', 1, '系统级编程语言', '/images/rust-icon.png', 15, 1, 'Rust安全编程', '所有权,生命周期', 'Rust内存安全机制解析', '2025-04-28 17:41:33', '2025-04-28 17:41:33');

-- ----------------------------
-- Table structure for acms_comments
-- ----------------------------
DROP TABLE IF EXISTS `acms_comments`;
CREATE TABLE `acms_comments`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '评论内容',
  `user_id` bigint(0) UNSIGNED NOT NULL COMMENT '用户ID',
  `article_id` bigint(0) UNSIGNED NOT NULL COMMENT '文章ID',
  `parent_id` bigint(0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父评论ID',
  `status` tinyint(0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态 0-待审核 1-已审核',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `likes` int(0) UNSIGNED NULL DEFAULT 0 COMMENT '点赞数',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `article_id`(`article_id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of acms_comments
-- ----------------------------
INSERT INTO `acms_comments` VALUES (1, '这篇文章讲解得非常清晰，特别是数据库优化部分对我帮助很大！', 101, 1, 0, 1, '2024-03-15 09:12:34', '2024-03-15 09:12:34', 0);
INSERT INTO `acms_comments` VALUES (2, '同感！作者把B+树索引原理讲得很易懂', 102, 1, 1, 1, '2024-03-15 10:05:21', '2024-03-15 10:05:21', 0);
INSERT INTO `acms_comments` VALUES (3, '期待作者更新量子计算的实际应用案例', 103, 2, 0, 0, '2024-03-16 14:30:45', '2024-03-16 14:30:45', 0);
INSERT INTO `acms_comments` VALUES (4, '个人觉得PHP在2024年已经不是最佳选择了', 104, 3, 0, 1, '2024-03-17 11:22:33', '2024-03-17 11:22:33', 0);
INSERT INTO `acms_comments` VALUES (5, 'PHP仍然在CMS领域占据主导地位，请看最新统计数据', 105, 3, 4, 1, '2024-03-17 13:15:40', '2024-03-17 13:15:40', 0);
INSERT INTO `acms_comments` VALUES (6, '请问示例代码中Redis连接池应该如何配置？', 106, 5, 0, 1, '2024-03-18 08:40:12', '2024-03-18 08:40:12', 0);
INSERT INTO `acms_comments` VALUES (7, '建议使用HikariCP配置，文档第5章有详细说明', 1, 5, 6, 1, '2024-03-18 09:05:30', '2024-03-18 09:05:30', 0);
INSERT INTO `acms_comments` VALUES (10, '我们团队50人采用中等粒度（10-15服务）运行良好', 108, 1, 2, 1, '2024-03-21 16:44:50', '2024-03-21 16:44:50', 0);
INSERT INTO `acms_comments` VALUES (11, '这篇文章讲解得非常清晰，特别是数据库优化部分对我帮助很大！', 1, 1, 0, 1, '2024-03-15 09:12:34', '2024-03-15 09:12:34', 0);
INSERT INTO `acms_comments` VALUES (12, '同感！作者把B+树索引原理讲得很易懂', 1, 1, 1, 1, '2024-03-15 10:05:21', '2024-03-15 10:05:21', 0);
INSERT INTO `acms_comments` VALUES (13, '期待作者更新量子计算的实际应用案例', 1, 2, 0, 0, '2024-03-16 14:30:45', '2024-03-16 14:30:45', 0);
INSERT INTO `acms_comments` VALUES (14, '个人觉得PHP在2024年已经不是最佳选择了', 1, 3, 0, 1, '2024-03-17 11:22:33', '2024-03-17 11:22:33', 0);
INSERT INTO `acms_comments` VALUES (15, 'PHP仍然在CMS领域占据主导地位，请看最新统计数据', 1, 3, 4, 1, '2024-03-17 13:15:40', '2024-03-17 13:15:40', 0);
INSERT INTO `acms_comments` VALUES (16, '请问示例代码中Redis连接池应该如何配置？', 1, 5, 0, 1, '2024-03-18 08:40:12', '2024-03-18 08:40:12', 0);
INSERT INTO `acms_comments` VALUES (17, '建议使用HikariCP配置，文档第5章有详细说明', 1, 5, 6, 1, '2024-03-18 09:05:30', '2024-03-18 09:05:30', 0);
INSERT INTO `acms_comments` VALUES (19, '关于微服务拆分粒度，我认为应该根据团队规模决定', 107, 6, 0, 1, '2024-03-20 15:33:28', '2024-03-20 15:33:28', 0);
INSERT INTO `acms_comments` VALUES (20, '我们团队50人采用中等粒度（10-15服务）运行良好', 1, 1, 2, 1, '2024-03-21 16:44:50', '2024-03-21 16:44:50', 0);
INSERT INTO `acms_comments` VALUES (21, '这篇文章讲解得非常清晰，特别是数据库优化部分对我帮助很大！', 1, 13, 0, 1, '2024-03-15 09:12:34', '2024-03-15 09:12:34', 0);
INSERT INTO `acms_comments` VALUES (22, '同感！作者把B+树索引原理讲得很易懂', 1, 14, 1, 15, '2024-03-15 10:05:21', '2024-03-15 10:05:21', 0);
INSERT INTO `acms_comments` VALUES (23, '期待作者更新量子计算的实际应用案例', 1, 2, 0, 0, '2024-03-16 14:30:45', '2024-03-16 14:30:45', 0);
INSERT INTO `acms_comments` VALUES (24, '个人觉得PHP在2024年已经不是最佳选择了', 1, 3, 0, 1, '2024-03-17 11:22:33', '2024-03-17 11:22:33', 0);
INSERT INTO `acms_comments` VALUES (25, 'PHP仍然在CMS领域占据主导地位，请看最新统计数据', 1, 3, 4, 1, '2024-03-17 13:15:40', '2024-03-17 13:15:40', 0);
INSERT INTO `acms_comments` VALUES (26, '请问示例代码中Redis连接池应该如何配置？', 1, 5, 0, 1, '2024-03-18 08:40:12', '2024-03-18 08:40:12', 0);
INSERT INTO `acms_comments` VALUES (27, '建议使用HikariCP配置，文档第5章有详细说明', 1, 5, 6, 1, '2024-03-18 09:05:30', '2024-03-18 09:05:30', 0);
INSERT INTO `acms_comments` VALUES (29, '关于微服务拆分粒度，我认为应该根据团队规模决定', 107, 6, 0, 1, '2024-03-20 15:33:28', '2024-03-20 15:33:28', 0);
INSERT INTO `acms_comments` VALUES (30, '我们团队50人采用中等粒度（10-15服务）运行良好', 1, 16, 2, 1, '2024-03-21 16:44:50', '2024-03-21 16:44:50', 0);

-- ----------------------------
-- Table structure for acms_tags
-- ----------------------------
DROP TABLE IF EXISTS `acms_tags`;
CREATE TABLE `acms_tags`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '标签名称',
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '标签别名',
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '标签描述',
  `count` int(0) NOT NULL DEFAULT 0 COMMENT '文章数量',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 0隐藏 1显示',
  `sort` int(0) NOT NULL DEFAULT 0 COMMENT '排序值',
  `created_at` timestamp(0) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `slug`(`slug`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '标签表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of acms_tags
-- ----------------------------
INSERT INTO `acms_tags` VALUES (1, 'PHP', 'php', NULL, 0, 1, 0, '2025-04-16 18:38:31', '2025-04-16 18:38:31');
INSERT INTO `acms_tags` VALUES (2, 'WebMan', 'webman', NULL, 0, 1, 0, '2025-04-16 18:38:31', '2025-04-16 18:38:31');
INSERT INTO `acms_tags` VALUES (3, 'Laravel', 'laravel', NULL, 0, 1, 0, '2025-04-16 18:38:31', '2025-04-16 18:38:31');

SET FOREIGN_KEY_CHECKS = 1;
