<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use App\Infrastructure\Settings\Settings;
use App\Infrastructure\Settings\SettingsInterface;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => true,
                'logErrorDetails'     => true,
                'logger' => [
                    'name' => getenv('PROJECT_NAME'),
                    'path' => 'php://stdout',
                    'level' => Logger::DEBUG,
                ],
                'database' => [
                    'dsn' => getenv('MYSQL_DSN'),
                ],
                'messenger' => [
                    'transports' => [
                        'async_transport' => [
                            'dsn' => getenv('RABBITMQ_TRANSPORT_DSN'),
                            'options' => [
                                'exchange' => [
                                    'name' => 'commands.exchange',
                                ],
                                'queues' => [
                                    'commands.queue' => [
                                        'binding_keys' => [ 'async.commands' ],
                                    ],
                                ],
                            ],
                            'retry_strategy' => [
                                'max_retries' => 3,
                                'delay' => 1000,
                                'multiplier' => 2,
                                'max_delay' => 0,
                            ]
                        ],
                        'async_failure_transport' => [
                            'dsn' => 'doctrine://default',
                            'options' => [
                                'table_name' => 'async_commands_failed_messages',
                            ],
                        ]
                    ],
                ],
            ]);
        }
    ]);
};
