<?php

use plugin\acms\app\admin\controller\ArticleController;
use plugin\acms\app\admin\controller\CategoryController;
use plugin\acms\app\admin\controller\CommentController;
use plugin\acms\app\admin\controller\TagController;
use plugin\acms\app\admin\controller\UploadController;
use plugin\acms\app\admin\controller\DashboardController;
use plugin\acms\app\controller\CommentController as IndexCommentController;
use plugin\acms\app\controller\IndexController;
use plugin\acms\app\controller\UserController;
use Webman\Route;
use Webman\Http\Request;

// 前台路由
Route::group('/app/acms', function () {
    // 首页
    Route::get('', [IndexController::class, 'list']);

    // 文章详情
    Route::get('/article/{id}', [IndexController::class, 'detail']);

    // 统一文章列表入口，所有参数通过url传递
    Route::get('/list', [IndexController::class, 'list']);

    // 添加评论
    Route::post('/comment/add', [IndexCommentController::class, 'add']);
    
    // 用户相关路由
    Route::group('/user', function () {
        // 用户点赞的文章
        Route::get('/likes', [UserController::class, 'likes']);
        // 评论过的文章
        Route::get('/commented', [UserController::class, 'commented']);
        // 评论点赞
        Route::post('/comment/like', [UserController::class, 'likeComment']);
        // 用户收藏
        Route::post('/favorite/toggle', [UserController::class, 'favorite']);
        // 用户浏览历史
        Route::get('/history', [UserController::class, 'history']);
    });
});

// 后台路由
Route::group('/app/admin/acms', function () {
    // 文章管理
    Route::group('/article', function () {
        Route::get('/index', [ArticleController::class, 'index']);
        Route::get('/add', [ArticleController::class, 'add']);
        Route::post('/save', [ArticleController::class, 'save']);
        Route::get('/edit/{id}', [ArticleController::class, 'edit']);
        Route::post('/update/{id}', [ArticleController::class, 'update']);
        Route::post('/delete', [ArticleController::class, 'delete']);
        Route::post('/change-status', [ArticleController::class, 'changeStatus']);
    });

    // 分类管理
    Route::group('/category', function () {
        Route::get('/index', [CategoryController::class, 'index']);
        Route::get('/add', [CategoryController::class, 'add']);
        Route::post('/save', [CategoryController::class, 'save']);
        Route::get('/edit/{id}', [CategoryController::class, 'edit']);
        Route::post('/update/{id}', [CategoryController::class, 'update']);
        Route::post('/delete', [CategoryController::class, 'delete']);
        Route::post('/change-status', [CategoryController::class, 'changeStatus']);
    });

    // 标签管理
    Route::group('/tag', function () {
        Route::get('/index', [TagController::class, 'index']);
        Route::get('/add', [TagController::class, 'add']);
        Route::post('/save', [TagController::class, 'save']);
        Route::get('/edit/{id}', [TagController::class, 'edit']);
        Route::post('/update/{id}', [TagController::class, 'update']);
        Route::post('/delete', [TagController::class, 'delete']);
        Route::post('/status', [TagController::class, 'status']);
    });

    // 评论管理
    Route::group('/comment', function () {
        Route::get('/index', [CommentController::class, 'index']);
        Route::get('/edit/{id}', [CommentController::class, 'edit']);
        Route::post('/update/{id}', [CommentController::class, 'update']);
        Route::post('/delete', [CommentController::class, 'delete']);
        Route::post('/audit', [CommentController::class, 'audit']);
    });

    // 上传管理
    Route::post('/upload/image', [UploadController::class, 'image']);

    // 仪表盘
    Route::group('/dashboard', function () {
        Route::get('/index', [DashboardController::class, 'index']);
    });
});
