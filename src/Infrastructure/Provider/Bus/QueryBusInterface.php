<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Bus;

use App\Application\Query\QueryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;

    public function getMessageBus(): MessageBusInterface;
}