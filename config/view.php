<?php

use support\view\Blade;

return [
    'handler' => Blade::class,
    'options' => [
        // 模板文件扩展名
        'view_suffix' => 'blade.php',
        // 模板缓存目录
        'cache_path' => runtime_path() . '/views/',
    ]
]; 