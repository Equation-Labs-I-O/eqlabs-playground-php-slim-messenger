<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Infrastructure\Settings\SettingsInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpSender;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

final readonly class MessengerSenders implements SendersLocatorInterface
{
    public function __construct(private SettingsInterface $settings)
    {
    }

    public function getSenders(Envelope $envelope): iterable
    {
        $messengerSettings = $this->settings->get('messenger');

        return [
            'async_commands' => $this->createAsyncSender($messengerSettings['transports']['async_commands']),
        ];
    }

    private function createAsyncSender(array $settings): AmqpSender
    {
        $options = $settings['options'];
        $dsn = $settings['dsn'];

        $connection = Connection::fromDsn($dsn, $options);

        return new AmqpSender($connection);
    }
}