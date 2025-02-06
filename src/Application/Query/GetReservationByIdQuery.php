<?php

declare(strict_types=1);

namespace App\Application\Query;

final readonly class GetReservationByIdQuery implements QueryInterface
{
    public function __construct(public string $reservationId)
    {
    }
}