<?php

namespace App\Infrastructure\Provider\Messenger\Adapters;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceLocatorTrait;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class MessengerServiceLocator implements ContainerInterface, ServiceProviderInterface
{
    use ServiceLocatorTrait;

    /**
     * @param array<string, callable> $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    public function getProvidedServices(): array
    {
        return $this->factories;
    }
}