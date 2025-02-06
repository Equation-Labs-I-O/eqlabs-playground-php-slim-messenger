<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Provider\Messenger\Adapters\MessengerServiceLocator;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class ServicesProvider
{
    public const MESSENGER_FAILURE_TRANSPORTS_SERVICE_PROVIDER = 'messenger.failure.transports.service.provider';
    public const MESSENGER_TRANSPORTS_SERVICE_PROVIDER = 'messenger.transports.service.provider';
    public const MESSENGER_RETRY_SERVICE_PROVIDER = 'messenger.service.provider';

    public static function load(ContainerBuilder $containerBuilder): void
    {
       $containerBuilder->addDefinitions([
           self::MESSENGER_TRANSPORTS_SERVICE_PROVIDER => function (ContainerInterface $container): ServiceProviderInterface {
               return new MessengerServiceLocator(
                   [
                       TransportsProviders::MESSENGER_AMQP_TRANSPORT => fn() => $container->get(TransportsProviders::MESSENGER_AMQP_TRANSPORT)
                   ]
               );
           },
          self::MESSENGER_FAILURE_TRANSPORTS_SERVICE_PROVIDER => function (ContainerInterface $container): ServiceProviderInterface {
            return new MessengerServiceLocator(
                 [
                     TransportsProviders::MESSENGER_AMQP_TRANSPORT => fn() => $container->get(TransportsProviders::MESSENGER_FAILURE_TRANSPORT),
                 ]
            );
          },
          self::MESSENGER_RETRY_SERVICE_PROVIDER => function (ContainerInterface $container): ServiceProviderInterface {
              return new MessengerServiceLocator(
                  [
                      TransportsProviders::MESSENGER_AMQP_TRANSPORT => fn() => $container->get(RetryStrategyProvider::FOR_ASYNC_TRANSPORT)
                  ]
              );
          },
        ]);
    }
}