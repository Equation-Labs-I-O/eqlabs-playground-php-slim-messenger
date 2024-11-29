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

final readonly class CommandBusProvider
{
    public const ASYNC = 'async.command.bus';
    public const SYNC = 'sync.command.bus';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            'async.command.bus' => function (ContainerInterface $container) {
                $handlersLocator = new HandlersLocator($container->get('command.handlers'));
                $senders = $container->get(SendersProviders::ASYNC_MESSAGES_SENDERS);

                return new MessageBus([
                    new SendMessageMiddleware($senders),
                    new HandleMessageMiddleware($handlersLocator),
                ]);
            },
            'sync.command.bus' => function (ContainerInterface $container) {
                $handlersLocator = new HandlersLocator($container->get('command.handlers'));

                return new MessageBus([
                    new HandleMessageMiddleware($handlersLocator),
                ]);
            },
            'command.handlers' => function (ContainerInterface $container) {
                return [
                    CreatePendingReservationCommand::class => [$container->get(CreatePendingReservationHandler::class)],
                    ConfirmReservationCommand::class => [$container->get(ConfirmReservationHandler::class)],
                ];
            },
        ]);
    }
}