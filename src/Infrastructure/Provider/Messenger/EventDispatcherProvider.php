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
    public const FOR_ASYNC_TRANSPORT = 'messenger.event.dispatcher';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::FOR_ASYNC_TRANSPORT => function (ContainerInterface $container) {
                $dispatcher = new EventDispatcher();

                foreach ($container->get('messenger.event.listeners') as $subscriber) {
                    $dispatcher->addSubscriber($subscriber);
                }

                return $dispatcher;
            },
            'messenger.event.listeners' => function (ContainerInterface $container) {
                return [
                    new AddErrorDetailsStampListener(),
//                    new SendFailedMessageForRetryListener(
//                        $container->get(ServicesProvider::ASYNC_TRANSPORTS_SERVICE_PROVIDER),
//                        $container->get(ServicesProvider::RETRY_STRATEGY_SERVICE_PROVIDER),
//                        $container->get(LoggerInterface::class)
//                    ),
                    new SendFailedMessageToFailureTransportListener(
                        $container->get(ServicesProvider::FAILURE_TRANSPORTS_SERVICE_PROVIDER),
                        $container->get(LoggerInterface::class)
                    )
                ];
            },
        ]);
    }
}