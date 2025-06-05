<?php

use plugin\acms\app\middleware\CsrfTokenCheck;
use plugin\admin\api\Middleware as AdminMiddleware;
use plugin\user\api\Middleware as UserMiddleware;
return [
    '' => [
        new UserMiddleware(['admin']),
        CsrfTokenCheck::class,
    ],
    'admin' => [
        AdminMiddleware::class
    ]
];
