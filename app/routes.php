<?php

declare(strict_types=1);

use App\Infrastructure\Controller\CommandQueryController;
use Slim\App;

return function (App $app) {
    $app->get('/cqrs', CommandQueryController::class);
};
