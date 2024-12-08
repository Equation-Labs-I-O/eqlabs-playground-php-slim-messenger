<?php

declare(strict_types=1);

namespace App\Application\Command;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RetryAndFailHandler
{
    /**
     * @throws Exception
     */
    public function __invoke(RetryAndFailCommand $command): void
    {
        sleep(1);
        throw new Exception('This command failed ');
    }
}