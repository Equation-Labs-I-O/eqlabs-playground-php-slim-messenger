<?php

declare(strict_types=1);

use App\Infrastructure\Provider\ControllersProvider;
use App\Infrastructure\Provider\DatabaseProvider;
use App\Infrastructure\Provider\LoggerProvider;
use App\Infrastructure\Provider\Messenger\CommandBusProvider;
use App\Infrastructure\Provider\Messenger\ConsoleCommandsProvider;
use App\Infrastructure\Provider\Messenger\QueryBusProvider;
use App\Infrastructure\Provider\Messenger\TransportsProviders;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    LoggerProvider::load($containerBuilder);
    DatabaseProvider::load($containerBuilder);
    ControllersProvider::load($containerBuilder);

    // Messenger bus configuration
    TransportsProviders::load($containerBuilder);
    CommandBusProvider::load($containerBuilder);
    QueryBusProvider::load($containerBuilder);
    ConsoleCommandsProvider::load($containerBuilder);
};
