<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Bus;

use App\Application\Command\CommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

interface CommandBusInterface
{
    public function handle(CommandInterface $command): mixed;

    public function handleAsync(CommandInterface $command): void;

    public function getMessageBus(): MessageBusInterface;
}