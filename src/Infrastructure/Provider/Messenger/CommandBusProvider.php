<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Application\Command\ConfirmReservationCommand;
use App\Application\Command\ConfirmReservationHandler;
use App\Application\Command\CreatePendingReservationCommand;
use App\Application\Command\CreatePendingReservationHandler;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;

final readonly class CommandBusProvider
{
    public const ASYNC = 'async.command.bus';
    public const SYNC = 'command.bus';
    public const HANDLERS_MAP = 'command.handlers';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::ASYNC => function (ContainerInterface $container) {
                $handlersLocator = new HandlersLocator($container->get(self::HANDLERS_MAP));
                $sendersLocator = new SendersLocator(...$container->get(TransportsProviders::ASYNC_SENDER_LOCATOR_CONFIGURATION));

                return new MessageBus([
                    new SendMessageMiddleware($sendersLocator),
                    new HandleMessageMiddleware($handlersLocator),
                ]);
            },
            self::SYNC => function (ContainerInterface $container) {
                $handlersLocator = new HandlersLocator($container->get(self::HANDLERS_MAP));

                return new MessageBus([
                    new HandleMessageMiddleware($handlersLocator),
                ]);
            },
            self::HANDLERS_MAP => function (ContainerInterface $container) {
                return [
                    CreatePendingReservationCommand::class => [$container->get(CreatePendingReservationHandler::class)],
                    ConfirmReservationCommand::class => [$container->get(ConfirmReservationHandler::class)],
                ];
            },
        ]);
    }
}