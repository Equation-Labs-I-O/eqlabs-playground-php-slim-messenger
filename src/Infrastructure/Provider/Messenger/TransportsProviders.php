<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Provider\DatabaseProvider;
use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceiver;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpSender;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpTransport;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection as DoctrineConnection;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Sync\SyncTransportFactory;
use Symfony\Component\Messenger\Transport\TransportInterface;

final readonly class TransportsProviders
{
    public const ASYNC_FAILURE_TRANSPORT = 'messenger.transport.failure';
    public const ASYNC_TRANSPORT = 'messenger.transport.async';
    public const ASYNC_SENDERS_MAP = 'messenger.senders.map.configuration';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::ASYNC_SENDERS_MAP => function (ContainerInterface $container): array {
                return [
                   [ '*' =>  [ self::ASYNC_TRANSPORT ]],
                   $container
                ];
            },
            self::ASYNC_TRANSPORT => function (ContainerInterface $container): TransportInterface {
                $settings = $container->get(SettingsInterface::class);
                $amqpSettings = $settings->get('messenger')['transports']['async'];

                return new AmqpTransport(
                    Connection::fromDsn($amqpSettings['dsn'], $amqpSettings['options']),
                    new PhpSerializer()
                );
            },
            self::ASYNC_FAILURE_TRANSPORT => function (ContainerInterface $container): TransportInterface {
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