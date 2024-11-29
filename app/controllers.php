<?php

declare(strict_types=1);

use App\Infrastructure\Provider\ControllersProvider;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    ControllersProvider::load($containerBuilder);
};