<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Bus;

use App\Application\Command\CommandInterface;
use App\Infrastructure\Provider\CommandBusProvider;
use App\Infrastructure\Provider\Messenger\TransportsProviders;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

final class CommandBus implements CommandBusInterface
{
    use HandleTrait {
        handle as busHandle;
    }

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function handle(CommandInterface $command): mixed
    {
        return $this->busHandle($command);
    }

    /**
     * @throws ExceptionInterface
     */
    public function handleAsync(CommandInterface $command): void
    {
        $stamps = [
            new BusNameStamp(CommandBusProvider::COMMAND_BUS),
            new TransportNamesStamp([TransportsProviders::MESSENGER_AMQP_TRANSPORT]),
        ];

        $this->messageBus->dispatch($command, $stamps);
    }

    public function getMessageBus(): MessageBusInterface
    {
        return $this->messageBus;
    }

}