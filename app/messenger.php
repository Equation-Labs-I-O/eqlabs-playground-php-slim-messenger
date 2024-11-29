<?php

declare(strict_types=1);

use App\Infrastructure\Provider\Messenger\SendersProviders;
use App\Infrastructure\Provider\Messenger\CommandBusProvider;
use App\Infrastructure\Provider\Messenger\QueryBusProvider;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Symfony messenger bus configuration
    SendersProviders::load($containerBuilder);
    CommandBusProvider::load($containerBuilder);
    QueryBusProvider::load($containerBuilder);
};