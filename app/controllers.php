<?php

declare(strict_types=1);

use App\Application\QueryCommandUseCase;
use App\Infrastructure\Controller\CommandQueryController;
use App\Infrastructure\Provider\Messenger\CommandBusProvider;
use App\Infrastructure\Provider\Messenger\QueryBusProvider;
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
                    $container->get(CommandBusProvider::ASYNC),
                    $container->get(CommandBusProvider::SYNC),
                    $container->get(QueryBusProvider::SYNC),
                );
            },
        ]
    );
};