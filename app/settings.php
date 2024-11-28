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
                        'async' => [
                            'dsn' => getenv('RABBITMQ_COMMANDS_DSN'),
                            'options' => [
                                'exchange' => [
                                    'name' => 'async.messages.exchange',
                                ],
                                'queues' => [
                                    'async.messages.exchange' => 'async.messages.queue',
                                ],
                                'retry' => [
                                    'delay' => 1000,
                                    'multiplier' => 2,
                                    'max' => 10000,
                                ],
                            ],
                        ],
                        'sync' => [
                            'dsn' => 'sync://',
                        ],
                    ],
                ],
            ]);
        }
    ]);
};
