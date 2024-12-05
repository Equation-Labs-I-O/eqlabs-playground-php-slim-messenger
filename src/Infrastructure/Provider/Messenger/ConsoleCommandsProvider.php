<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Adapters\MessengerServicesProvider;
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
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

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
            FailedMessagesShowCommand::class => function (ContainerInterface $container): FailedMessagesShowCommand {
                return new FailedMessagesShowCommand(
                    TransportsProviders::ASYNC_FAILURE_TRANSPORT,
                    $container->get(ServicesProvider::FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                );
            },
            FailedMessagesRemoveCommand::class => function (ContainerInterface $container): FailedMessagesRemoveCommand {
                return new FailedMessagesRemoveCommand(
                    TransportsProviders::ASYNC_FAILURE_TRANSPORT,
                    $container->get(ServicesProvider::FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                );
            },
        ]);
    }
}