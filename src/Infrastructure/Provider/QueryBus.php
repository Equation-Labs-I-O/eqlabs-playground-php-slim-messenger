<?php


namespace App\Infrastructure\Provider;

use App\Application\Query\GetReservationByIdHandler;
use App\Application\Query\GetReservationByIdQuery;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final readonly class QueryBus
{
    public const SERVICE_NAME = 'query.bus';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            'query.bus' => function (ContainerInterface $container) {
                $handlers = $container->get('query.handlers');
                $handlersLocator = new HandlersLocator($handlers);

                return new MessageBus([
                    new HandleMessageMiddleware($handlersLocator),
                ]);
            },
            'query.handlers' => function (ContainerInterface $container) {
                return [
                    GetReservationByIdQuery::class => [$container->get(GetReservationByIdHandler::class)],
                ];
            },
        ]);
    }
}