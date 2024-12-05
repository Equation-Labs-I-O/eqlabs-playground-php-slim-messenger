<?php

namespace App\Infrastructure\Adapters;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class MessengerServicesProvider implements ServiceProviderInterface
{
    public function __construct(private ContainerInterface $container, private array $transportServicesIds)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    public function getProvidedServices(): array
    {
        return $this->transportServicesIds;
    }
}