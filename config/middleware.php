<?php

use plugin\admin\api\Middleware as AdminMiddleware;
use plugin\user\api\Middleware as UserMiddleware;
return [
    '' => [
        new UserMiddleware(['admin'])
    ],
    'admin' => [
        AdminMiddleware::class
    ]
];
