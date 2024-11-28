<?php

declare(strict_types=1);

namespace App\Application\Query;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetReservationByIdHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(GetReservationByIdQuery $query): string
    {
        $this->logger->info('Get Reservation by id was handled successfully');

        return $query->reservationId;
    }
}