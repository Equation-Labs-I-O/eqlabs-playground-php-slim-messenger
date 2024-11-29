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
                'messenger' => [
                    'transports' => [
                        'async_commands' => [
                            'dsn' => getenv('RABBITMQ_COMMANDS_DSN'),
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
                        ],
                        'sync' => [
                            'dsn' => 'sync://',
                        ],
                    ],
                    'retry_strategy' => [
                        'max_retries' => 3,
                        'delay' => 1000,
                        'multiplier' => 2,
                        'max_delay' => 0,
                    ],
                ],
            ]);
        }
    ]);
};
