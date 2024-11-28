<?php

declare(strict_types=1);

use App\Application\QueryCommandUseCase;
use App\Infrastructure\Controller\CommandQueryController;
use App\Infrastructure\Provider\CommandBus;
use App\Infrastructure\Provider\QueryBus;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            CommandQueryController::class => function (ContainerInterface $container) {
                return new CommandQueryController(
                    $container->get(QueryCommandUseCase::class),
                );
            },
            QueryCommandUseCase::class => function (ContainerInterface $container) {
                return new QueryCommandUseCase(
                    $container->get(LoggerInterface::class),
                    $container->get(CommandBus::ASYNC),
                    $container->get(CommandBus::SYNC),
                    $container->get(QueryBus::SERVICE_NAME),
                );
            },
        ]
    );
};