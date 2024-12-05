<?php

declare(strict_types=1);

namespace App\Application\Command;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RetryAndFailHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(RetryAndFailCommand $command): void
    {
        $this->logger->info('This command will fail and be retried, until it reaches the max retries, then it will be moved to the failure transport (database)');
        sleep(1);
        throw new Exception('This command failed ');
    }
}