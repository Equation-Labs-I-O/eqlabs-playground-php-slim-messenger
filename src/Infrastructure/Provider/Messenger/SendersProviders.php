<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Messenger\MessengerSenders;
use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

final readonly class SendersProviders
{
    public const ASYNC_MESSAGES_SENDERS = 'messenger.senders';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        // define sender locator middlewares for AMQP and SYNC transport to be injected in the message buses
        $containerBuilder->addDefinitions([
            self::ASYNC_MESSAGES_SENDERS => function (ContainerInterface $container): SendersLocatorInterface {
                return new MessengerSenders($container->get(SettingsInterface::class));
            },
        ]);
    }
}