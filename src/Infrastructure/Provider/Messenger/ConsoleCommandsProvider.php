<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Adapters\FailureTransportsServicesProvider;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\Command\FailedMessagesRemoveCommand;
use Symfony\Component\Messenger\Command\FailedMessagesRetryCommand;
use Symfony\Component\Messenger\Command\FailedMessagesShowCommand;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class ConsoleCommandsProvider
{
    public const FAILURE_TRANSPORTS_SERVICE_PROVIDER = 'messages.failure.transport.services.locator';
    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            ConsumeMessagesCommand::class => function (ContainerInterface $container): ConsumeMessagesCommand {
                return new ConsumeMessagesCommand(
                    new RoutableMessageBus($container),
                    $container,
                    $container->get(EventDispatcherInterface::class),
                    $container->get(LoggerInterface::class),
                    [
                        TransportsProviders::ASYNC_RECEIVER_TRANSPORT,
                    ]
                );
            },
            FailedMessagesRetryCommand::class => function (ContainerInterface $container): FailedMessagesRetryCommand {
                return new FailedMessagesRetryCommand(
                    TransportsProviders::ASYNC_FAILURE_TRANSPORT,
                    $container->get(self::FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                    $container->get(EventDispatcherInterface::class),
                    new EventDispatcher(),
                    $container->get(LoggerInterface::class),
                );
            },
            FailedMessagesShowCommand::class => function (ContainerInterface $container): FailedMessagesShowCommand {
                return new FailedMessagesShowCommand(
                    TransportsProviders::ASYNC_FAILURE_TRANSPORT,
                    $container->get(self::FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                );
            },
            FailedMessagesRemoveCommand::class => function (ContainerInterface $container): FailedMessagesRemoveCommand {
                return new FailedMessagesRemoveCommand(
                    TransportsProviders::ASYNC_FAILURE_TRANSPORT,
                    $container->get(self::FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                );
            },
            self::FAILURE_TRANSPORTS_SERVICE_PROVIDER => function (ContainerInterface $container): ServiceProviderInterface {
                return new FailureTransportsServicesProvider(
                    $container,
                    [
                        TransportsProviders::ASYNC_FAILURE_TRANSPORT => ReceiverInterface::class,
                    ]
                );
            },
            EventDispatcherInterface::class => function (): EventDispatcherInterface {
                return new EventDispatcher();
            },
        ]);
    }
}