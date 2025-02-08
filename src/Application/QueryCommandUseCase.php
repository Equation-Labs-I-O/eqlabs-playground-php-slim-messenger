<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Command\ConfirmReservationCommand;
use App\Application\Command\CreatePendingReservationCommand;
use App\Application\Query\GetReservationByIdQuery;
use App\Infrastructure\Provider\Bus\CommandBusInterface;
use App\Infrastructure\Provider\Bus\QueryBusInterface;
use Psr\Log\LoggerInterface;

final readonly class QueryCommandUseCase
{
    public function __construct(
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus
    ) {
    }

    public function execute(string $id): void
    {
        // sync query handling
        $newId = $this->queryBus->ask(new GetReservationByIdQuery($id));
        // async command handling
        $this->commandBus->handleAsync(new CreatePendingReservationCommand($newId));
        // sync command handling
        $this->commandBus->handle(new ConfirmReservationCommand($newId));

        $this->logger->info('Use case has been executed successfully!');
    }
}