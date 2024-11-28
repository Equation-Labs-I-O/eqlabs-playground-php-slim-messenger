<?php

declare(strict_types=1);

use Slim\App;
use App\Infrastructure\Middleware\SessionMiddleware;

return function (App $app) {
    $app->add(SessionMiddleware::class);
};
