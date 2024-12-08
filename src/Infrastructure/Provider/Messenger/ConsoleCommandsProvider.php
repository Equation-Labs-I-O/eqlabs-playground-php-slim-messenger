<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Provider\CommandBusProvider;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\Command\FailedMessagesRemoveCommand;
use Symfony\Component\Messenger\Command\FailedMessagesRetryCommand;
use Symfony\Component\Messenger\Command\FailedMessagesShowCommand;
use Symfony\Component\Messenger\RoutableMessageBus;

final readonly class ConsoleCommandsProvider
{
    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            ConsumeMessagesCommand::class => function (ContainerInterface $container): ConsumeMessagesCommand {
                return new ConsumeMessagesCommand(
                    new RoutableMessageBus($container),
                    $container,
                    $container->get(EventDispatcherProvider::FOR_ASYNC_TRANSPORT),
                    $container->get(LoggerInterface::class),
                    [
                        TransportsProviders::ASYNC_TRANSPORT,
                    ]
                );
            },
            FailedMessagesRetryCommand::class => function (ContainerInterface $container): FailedMessagesRetryCommand {
                return new FailedMessagesRetryCommand(
                    null,
                    $container->get(ServicesProvider::MESSENGER_FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                    $container->get(CommandBusProvider::ASYNC),
                    $container->get(EventDispatcherProvider::FOR_ASYNC_TRANSPORT),
                    $container->get(LoggerInterface::class),
                );
            },
            FailedMessagesShowCommand::class => function (ContainerInterface $container): FailedMessagesShowCommand {
                return new FailedMessagesShowCommand(
                    null,
                    $container->get(ServicesProvider::MESSENGER_FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                );
            },
            FailedMessagesRemoveCommand::class => function (ContainerInterface $container): FailedMessagesRemoveCommand {
                return new FailedMessagesRemoveCommand(
                    null,
                    $container->get(ServicesProvider::MESSENGER_FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                );
            },
        ]);
    }
}