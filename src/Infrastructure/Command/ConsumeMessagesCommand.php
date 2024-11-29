<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsumeMessagesCommand extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('messenger:consume')
            ->setDescription('Consume messages from the queue')
            ->setHelp('This command is an example');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello, world!');


        return Command::SUCCESS;
    }
}