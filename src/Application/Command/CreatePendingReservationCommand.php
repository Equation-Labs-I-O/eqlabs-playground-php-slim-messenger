<?php

declare(strict_types=1);

namespace App\Application\Command;

final readonly class CreatePendingReservationCommand implements CommandInterface
{
    public function __construct(public string $reservationId)
    {
    }
}