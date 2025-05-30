<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\EventListener\AddErrorDetailsStampListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageForRetryListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener;

final readonly class EventDispatcherProvider
{
    public const MESSENGER_EVENT_DISPATCHER = 'messenger.event.dispatcher';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::MESSENGER_EVENT_DISPATCHER => function (ContainerInterface $container) {
                $dispatcher = new EventDispatcher();

                foreach ($container->get('messenger.event.listeners') as $subscriber) {
                    $dispatcher->addSubscriber($subscriber);
                }

                return $dispatcher;
            },
            'messenger.event.listeners' => function (ContainerInterface $container) {
                return [
                    new AddErrorDetailsStampListener(),
                    new SendFailedMessageForRetryListener(
                        $container->get(ServicesProvider::MESSENGER_TRANSPORTS_SERVICE_PROVIDER),
                        $container->get(ServicesProvider::MESSENGER_RETRY_SERVICE_PROVIDER),
                        $container->get(LoggerInterface::class)
                    ),
                    new SendFailedMessageToFailureTransportListener(
                        $container->get(ServicesProvider::MESSENGER_FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                        $container->get(LoggerInterface::class)
                    )
                ];
            },
        ]);
    }
}