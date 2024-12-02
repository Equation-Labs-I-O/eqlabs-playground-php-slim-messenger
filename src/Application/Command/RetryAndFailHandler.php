<?php

declare(strict_types=1);

namespace App\Application\Command;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RetryAndFailHandler
{
    private int $timesCalled = 0;

    /**
     * @throws Exception
     */
    public function __invoke(RetryAndFailCommand $command): void
    {
        ++$this->timesCalled;

        throw new Exception('This command failed ' . $this->timesCalled . ' times');
    }
}