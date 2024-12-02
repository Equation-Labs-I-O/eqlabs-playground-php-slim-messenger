<?php


namespace App\Infrastructure\Provider\Messenger;

use App\Application\Query\GetReservationByIdHandler;
use App\Application\Query\GetReservationByIdQuery;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final readonly class QueryBusProvider
{
    public const SYNC = 'query.bus';
    public const HANDLERS_MAP = 'query.handlers';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::SYNC => function (ContainerInterface $container) {
                $handlers = $container->get(self::HANDLERS_MAP);
                $handlersLocator = new HandlersLocator($handlers);

                return new MessageBus([
                    new HandleMessageMiddleware($handlersLocator),
                ]);
            },
            self::HANDLERS_MAP => function (ContainerInterface $container) {
                return [
                    GetReservationByIdQuery::class => [$container->get(GetReservationByIdHandler::class)],
                ];
            },
        ]);
    }
}