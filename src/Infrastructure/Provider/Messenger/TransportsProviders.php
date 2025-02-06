<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Provider\DatabaseProvider;
use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpTransport;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection as DoctrineConnection;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\TransportInterface;

final readonly class TransportsProviders
{
    public const MESSENGER_FAILURE_TRANSPORT = 'messenger.transport.failure';
    public const MESSENGER_AMQP_TRANSPORT = 'messenger.transport.amqp';
    public const MESSENGER_SENDERS_LOCATOR = 'messenger.senders.map.configuration';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::MESSENGER_SENDERS_LOCATOR => function (ContainerInterface $container): SendersLocator {
               return new SendersLocator(
                   [
                        self::MESSENGER_AMQP_TRANSPORT => $container->get(self::MESSENGER_AMQP_TRANSPORT),
                        self::MESSENGER_FAILURE_TRANSPORT => $container->get(self::MESSENGER_FAILURE_TRANSPORT),
                   ],
                   $container
               );
            },
            self::MESSENGER_AMQP_TRANSPORT => function (ContainerInterface $container): TransportInterface {
                $settings = $container->get(SettingsInterface::class);
                $amqpSettings = $settings->get('messenger')['transports']['async'];

                return new AmqpTransport(
                    Connection::fromDsn($amqpSettings['dsn'], $amqpSettings['options']),
                    new PhpSerializer()
                );
            },
            self::MESSENGER_FAILURE_TRANSPORT => function (ContainerInterface $container): TransportInterface {
                $settings = $container->get(SettingsInterface::class);
                $doctrineSettings = $settings->get('messenger')['transports']['async_failure'];

                return new DoctrineTransport(
                    new DoctrineConnection($doctrineSettings['options'], $container->get(DatabaseProvider::MAIN_CONNECTION)),
                    new PhpSerializer()
                );
            },
        ]);
    }
}