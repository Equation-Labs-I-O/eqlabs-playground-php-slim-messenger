<?php

declare(strict_types=1);

use App\Infrastructure\Provider\ControllersProvider;
use App\Infrastructure\Provider\LoggerProvider;
use App\Infrastructure\Provider\Messenger\CommandBusProvider;
use App\Infrastructure\Provider\Messenger\QueryBusProvider;
use App\Infrastructure\Provider\Messenger\SendersProviders;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    LoggerProvider::load($containerBuilder);
    ControllersProvider::load($containerBuilder);

    // Messenger bus configuration
    SendersProviders::load($containerBuilder);
    CommandBusProvider::load($containerBuilder);
    QueryBusProvider::load($containerBuilder);
};
