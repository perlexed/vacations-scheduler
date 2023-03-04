<?php

use DI\ContainerBuilder;

require __DIR__ . '/vendor/autoload.php';

if (php_sapi_name() !== 'cli') {
    throw new Exception('This application must be run in the CLI mode');
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_ENV['SENTRY_DSN']) {
    Sentry\init(['dsn' => $_ENV['SENTRY_DSN']]);
}

$_ENV['APP_ROOT_DIR'] = __DIR__;
