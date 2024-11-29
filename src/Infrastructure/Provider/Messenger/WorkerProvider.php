<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Worker;

final readonly class WorkerProvider
{
    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            'messenger.worker' => function (ContainerInterface $container) {
                return new Worker(
                    receivers: [],
                    bus: $container->get(CommandBusProvider::ASYNC),
                    logger: $container->get(LoggerInterface::class)
                );
            }
        ]);
    }
}