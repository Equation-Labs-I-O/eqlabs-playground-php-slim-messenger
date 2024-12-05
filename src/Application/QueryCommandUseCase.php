<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Command\ConfirmReservationCommand;
use App\Application\Command\CreatePendingReservationCommand;
use App\Application\Command\RetryAndFailCommand;
use App\Application\Query\GetReservationByIdQuery;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class QueryCommandUseCase
{
    public function __construct(
        private LoggerInterface $logger,
        private MessageBusInterface $asyncCommandBus,
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function execute(string $id): void
    {
        // sync query bus
        $this->queryBus->dispatch(new GetReservationByIdQuery($id));
        // async command bus
        $this->asyncCommandBus->dispatch(new CreatePendingReservationCommand($id));
        $this->asyncCommandBus->dispatch(new RetryAndFailCommand($id));
        // sync command bus
        $this->commandBus->dispatch(new ConfirmReservationCommand($id));

        $this->logger->info('Use case has been executed successfully!');
    }
}