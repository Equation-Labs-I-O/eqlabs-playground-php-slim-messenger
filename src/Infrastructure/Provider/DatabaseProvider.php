<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Infrastructure\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Psr\Container\ContainerInterface;

final readonly class DatabaseProvider
{
    public const MAIN_CONNECTION = 'database';
    public static function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            'database' => function (ContainerInterface $container): Connection {
                $settings = $container->get(SettingsInterface::class);
                $databaseSettings = $settings->get('database');

                $dsnParser = new DsnParser();

                return DriverManager::getConnection($dsnParser->parse($databaseSettings['dsn']));
            },
        ]);
    }
}