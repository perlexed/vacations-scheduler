<?php

use app\scheduler\domain\VacationsScheduler;
use DI\ContainerBuilder;

$container = require __DIR__ . '/bootstrap.php';

// Dependency Injection setup
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->addDefinitions(require __DIR__ . DIRECTORY_SEPARATOR . 'di-definitions.php');
$container = $containerBuilder->build();

// App start
/** @var VacationsScheduler $app */
$app = $container->get(VacationsScheduler::class);
$isDryRun = isset($argv[1]) && $argv[1] === 'dry';
$app->updateVacations($isDryRun);
