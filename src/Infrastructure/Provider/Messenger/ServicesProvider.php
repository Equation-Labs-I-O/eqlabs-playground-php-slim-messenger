<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Adapters\MessengerServicesProvider;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class ServicesProvider
{
    public const ASYNC_TRANSPORTS_SERVICE_PROVIDER = 'messenger.async.transport.service.provider';
    public const FAILURE_TRANSPORTS_SERVICE_PROVIDER = 'messenger.failure.transport.service.provider';
    public const RETRY_STRATEGY_SERVICE_PROVIDER = 'messenger.retry.strategy.service.provider';

    public static function load(ContainerBuilder $containerBuilder): void
    {
       $containerBuilder->addDefinitions([
          self::ASYNC_TRANSPORTS_SERVICE_PROVIDER => function (ContainerInterface $container): ServiceProviderInterface {
            return new MessengerServicesProvider(
                 $container,
                 [
                      TransportsProviders::ASYNC_TRANSPORT,
                 ]
            );
          },
           self::FAILURE_TRANSPORTS_SERVICE_PROVIDER => function (ContainerInterface $container): ServiceProviderInterface {
               return new MessengerServicesProvider(
                   $container,
                   [
                       TransportsProviders::ASYNC_FAILURE_TRANSPORT,
                   ]
               );
           },
          self::RETRY_STRATEGY_SERVICE_PROVIDER => function (ContainerInterface $container): ServiceProviderInterface {
              return new MessengerServicesProvider(
                  $container,
                  [
                      RetryStrategyProvider::FOR_ASYNC_TRANSPORT,
                  ]
              );
          },
        ]);
    }

}