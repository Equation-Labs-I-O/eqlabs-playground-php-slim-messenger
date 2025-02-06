<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Bus;

use App\Application\Query\QueryInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function ask(QueryInterface $query): mixed
    {
        return $this->handle($query);
    }

    public function getMessageBus(): MessageBusInterface
    {
        return $this->messageBus;
    }
}