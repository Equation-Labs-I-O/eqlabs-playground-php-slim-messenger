<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Retry\MultiplierRetryStrategy;

final readonly class RetryStrategyProvider
{
    public const MULTIPLIER_RETRY = 'messenger.retry_strategy';
    public static function load(ContainerBuilder $containerBuilder) : void
    {
        $containerBuilder->addDefinitions([
            'messenger.retry_strategy' => function (ContainerInterface $container) {
                $settings = $container->get(SettingsInterface::class);
                $messengerRetryStrategy = $settings->get('messenger')['retry_strategy'];

                return new MultiplierRetryStrategy(
                    $messengerRetryStrategy['max_retries'],
                    $messengerRetryStrategy['delay'],
                    $messengerRetryStrategy['multiplier'],
                    $messengerRetryStrategy['max_delay']
                );
            },
        ]);
    }
}