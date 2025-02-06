<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Application\Query\GetReservationByIdHandler;
use App\Application\Query\GetReservationByIdQuery;
use App\Infrastructure\Provider\Bus\QueryBus;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final readonly class QueryBusProvider
{
    public const QUERY_BUS = 'query.bus';
    public const HANDLERS_MAP = 'query.handlers';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::QUERY_BUS => function (ContainerInterface $container): QueryBus {
                $handlersLocator = new HandlersLocator($container->get(self::HANDLERS_MAP));

                $messageBus = new MessageBus([
                    new HandleMessageMiddleware($handlersLocator),
                ]);

                return new QueryBus($messageBus);
            },
            self::HANDLERS_MAP => function (ContainerInterface $container) {
                return [
                    GetReservationByIdQuery::class => [new GetReservationByIdHandler($container->get(LoggerInterface::class))],
                ];
            },
        ]);
    }
}
