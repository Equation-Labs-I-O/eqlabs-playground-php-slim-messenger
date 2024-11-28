<?php

declare(strict_types=1);

namespace App\Application\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ConfirmReservationHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ConfirmReservationCommand $command): void
    {
        $this->logger->info('This command was handled with a SYNC bus immediately!');
    }
}