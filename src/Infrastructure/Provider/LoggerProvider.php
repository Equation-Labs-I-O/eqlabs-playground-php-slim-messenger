<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final readonly class LoggerProvider
{
    public static function load(ContainerBuilder $containerBuilder) : void
    {
        $containerBuilder->addDefinitions([
            LoggerInterface::class => function (ContainerInterface $c) {
                $settings = $c->get(SettingsInterface::class);

                $loggerSettings = $settings->get('logger');
                $logger = new Logger($loggerSettings['name']);

                $processor = new UidProcessor();
                $logger->pushProcessor($processor);

                $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
                $logger->pushHandler($handler);

                return $logger;
            },
        ]);
    }
}