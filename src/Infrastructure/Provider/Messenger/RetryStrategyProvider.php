<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Retry\MultiplierRetryStrategy;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;

final readonly class RetryStrategyProvider
{
    public const FOR_ASYNC_TRANSPORT = 'messenger.commands.retry.strategy';
    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::FOR_ASYNC_TRANSPORT => function (ContainerInterface $container): RetryStrategyInterface {
                $settings = $container->get(SettingsInterface::class);
                $messengerRetrySettings = $settings->get('messenger')['transports']['async']['retry_strategy'];

                return new MultiplierRetryStrategy(
                    $messengerRetrySettings['max_retries'],
                    $messengerRetrySettings['delay'],
                    $messengerRetrySettings['multiplier'],
                    $messengerRetrySettings['max_delay']
                );

            }
        ]);
    }
}