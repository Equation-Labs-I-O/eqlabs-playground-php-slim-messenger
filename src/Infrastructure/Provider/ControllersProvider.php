<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Application\QueryCommandUseCase;
use App\Infrastructure\Controller\CommandQueryController;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final readonly class ControllersProvider
{
    public static function load(ContainerBuilder $containerBuilder): void
    {
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
    }
}