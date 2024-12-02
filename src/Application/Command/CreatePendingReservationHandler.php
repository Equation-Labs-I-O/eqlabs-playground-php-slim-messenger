<?php

declare(strict_types=1);

namespace App\Application\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreatePendingReservationHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(CreatePendingReservationCommand $command): void
    {
        sleep(1);

        $this->logger->info(
            sprintf(
                'This command was handled with an ASYNC bus at %s',
                (new \DateTime())->format('Y-m-d H:i:s')
            )
        );
    }
}