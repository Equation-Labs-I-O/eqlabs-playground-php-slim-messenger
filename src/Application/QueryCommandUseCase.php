<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Command\ConfirmReservationCommand;
use App\Application\Command\CreatePendingReservationCommand;
use App\Application\Query\GetReservationByIdQuery;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class QueryCommandUseCase
{
    public function __construct(
        private LoggerInterface $logger,
        private MessageBusInterface $asyncCommandBus,
        private MessageBusInterface $syncCommandBus,
        private MessageBusInterface $queryBus
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function execute(string $id): void
    {
        // sync bus (query bus is always sync)
        $this->queryBus->dispatch(new GetReservationByIdQuery($id));
        // async bus
        $this->asyncCommandBus->dispatch(new CreatePendingReservationCommand($id));
        // sync bus
        $this->syncCommandBus->dispatch(new ConfirmReservationCommand($id));

        $this->logger->info('Use case has been executed successfully!');
    }
}