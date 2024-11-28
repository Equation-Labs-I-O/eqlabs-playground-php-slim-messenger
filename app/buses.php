<?php

declare(strict_types=1);

use App\Infrastructure\Provider\BusTransports;
use App\Infrastructure\Provider\CommandBus;
use App\Infrastructure\Provider\QueryBus;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Symfony messenger bus configuration
    BusTransports::load($containerBuilder);
    CommandBus::load($containerBuilder);
    QueryBus::load($containerBuilder);
};