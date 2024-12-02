<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger;

use App\Infrastructure\Provider\DatabaseProvider;
use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceiver;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpSender;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection as DoctrineConnection;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

final readonly class TransportsProviders
{
    public const ASYNC_SENDER_TRANSPORT = 'messenger.async.sender.transport';
    public const ASYNC_RECEIVER_TRANSPORT = 'messenger.async.receiver.transport';
    public const ASYNC_SENDER_LOCATOR_CONFIGURATION = 'messenger.sender.locator.configuration';
    public const ASYNC_FAILURE_TRANSPORT = 'messenger.async.failure.transport';

    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            self::ASYNC_SENDER_LOCATOR_CONFIGURATION => function (ContainerInterface $container): array {
                return [
                   [ '*' =>  [ self::ASYNC_SENDER_TRANSPORT ]],
                    $container
                ];
            },
            self::ASYNC_SENDER_TRANSPORT => function (ContainerInterface $container): SenderInterface {
                $settings = $container->get(SettingsInterface::class);
                $amqpSettings = $settings->get('messenger')['transports']['async_transport'];

                return new AmqpSender(Connection::fromDsn($amqpSettings['dsn'], $amqpSettings['options']), new PhpSerializer());
            },
            self::ASYNC_RECEIVER_TRANSPORT => function (ContainerInterface $container): ReceiverInterface {
                $settings = $container->get(SettingsInterface::class);
                $amqpSettings = $settings->get('messenger')['transports']['async_transport'];

                return new AmqpReceiver(Connection::fromDsn($amqpSettings['dsn'], $amqpSettings['options']), new PhpSerializer());
            },
            self::ASYNC_FAILURE_TRANSPORT => function (ContainerInterface $container) {
                return new DoctrineTransport(
                    new DoctrineConnection([], $container->get(DatabaseProvider::MAIN_CONNECTION)),
                    new PhpSerializer()
                );
            }
        ]);
    }
}