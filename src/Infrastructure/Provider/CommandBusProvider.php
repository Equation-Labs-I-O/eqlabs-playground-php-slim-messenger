<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Application\Command\ConfirmReservationCommand;
use App\Application\Command\ConfirmReservationHandler;
use App\Application\Command\CreatePendingReservationCommand;
use App\Application\Command\CreatePendingReservationHandler;
use App\Application\Command\RetryAndFailCommand;
use App\Application\Command\RetryAndFailHandler;
use App\Infrastructure\Provider\Bus\CommandBus;
use App\Infrastructure\Provider\Messenger\TransportsProviders;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;

final readonly class CommandBusProvider
{
    public const COMMAND_BUS = 'command.bus';
    public const HANDLERS_MAP = 'command.handlers';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::COMMAND_BUS => function (ContainerInterface $container): CommandBus {
                $handlersLocator = new HandlersLocator($container->get(self::HANDLERS_MAP));
                $sendersLocator = $container->get(TransportsProviders::MESSENGER_SENDERS_LOCATOR);

                $messageBus = new MessageBus([
                    new FailedMessageProcessingMiddleware(),
                    new RejectRedeliveredMessageMiddleware(),
                    new SendMessageMiddleware($sendersLocator),
                    new HandleMessageMiddleware($handlersLocator),
                ]);

                return new CommandBus($messageBus);
            },
            self::HANDLERS_MAP => function (ContainerInterface $container) {
                return [
                    CreatePendingReservationCommand::class => [new CreatePendingReservationHandler($container->get(LoggerInterface::class))],
                    ConfirmReservationCommand::class => [new ConfirmReservationHandler($container->get(LoggerInterface::class))],
                ];
            },
        ]);
    }
}
