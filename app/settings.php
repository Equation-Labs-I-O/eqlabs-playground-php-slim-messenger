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
                        'async' => [
                            'dsn' => getenv('RABBITMQ_TRANSPORT_DSN'),
                            'options' => [
                                'exchange' => [
                                    'name' => 'commands.exchange',
                                    'default_publish_routing_key' => 'async.commands',
                                    'type' => 'direct',
                                ],
                                'queues' => [
                                    'commands.queue' => [
                                        'binding_keys' => [ 'async.commands' ],
                                    ],
                                ],
                                'delay' => [
                                    'exchange_name' => 'commands.retry.exchange',
                                    // you will have as many retry queues as the max_retries value dynamically created/deleted by the workers
                                    'queue_name_pattern' => 'commands.retry.queue.%delay%',
                                ]
                            ],
                            'retry_strategy' => [
                                'max_retries' => 3,
                                'delay' => 1000,
                                'multiplier' => 5,
                                'max_delay' => 0,
                            ]
                        ],
                        'async_failure' => [
                            'dsn' => 'doctrine://default',
                            'options' => [
                                'table_name' => 'commands_failed_messages',
                            ],
                        ]
                    ],
                ],
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => true,
                'logErrorDetails'     => true
            ]);
        }
    ]);
};
