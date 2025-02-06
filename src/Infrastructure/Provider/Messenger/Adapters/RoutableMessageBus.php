<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\Messenger\Adapters;

use App\Infrastructure\Provider\Bus\CommandBusInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus as SymfonyRoutableMessageBus;

final class RoutableMessageBus extends SymfonyRoutableMessageBus
{
    public function __construct(
        protected ContainerInterface $busLocator,
    ) {
        parent::__construct($busLocator);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getMessageBus(string $busName): MessageBusInterface
    {
        if (!$this->busLocator->has($busName)) {
            throw new InvalidArgumentException(\sprintf('Bus named "%s" does not exist.', $busName));
        }

        /** @var CommandBusInterface $bus */
        $bus = $this->busLocator->get($busName);

        return $bus->getMessageBus();
    }
}