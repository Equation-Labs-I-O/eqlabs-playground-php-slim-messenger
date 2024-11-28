<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExampleCommand extends Command
{


    protected function configure(): void
    {
        $this
            ->setName('reservation:fulfill')
            ->setDescription('Fulfill a reservation')
            ->setHelp('This command is an example');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello, world!');

        return Command::SUCCESS;
    }
}