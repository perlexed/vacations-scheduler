<?php

use app\calendar\domain\interfaces\IGoogleCalendarService;
use app\calendar\infrastructure\GoogleCalendarService;
use app\config\infrastructure\ConfigService;
use app\google\infrastructure\GoogleClientFetcher;
use app\logging\domain\interfaces\ILogger;
use app\logging\infrastructure\Logger;
use app\sheet\domain\interfaces\IGoogleSheetService;
use app\sheet\infrastructure\GoogleSheetService;
use Psr\Container\ContainerInterface;

return [
    ILogger::class => DI\autowire(Logger::class),
    IGoogleCalendarService::class => function (ContainerInterface $container) {
        /** @var ConfigService $configService */
        $configService = $container->get(ConfigService::class);
        $client = GoogleClientFetcher::getClient($configService->googleAuthFilePath);
        $logger = $container->get(ILogger::class);

        return new GoogleCalendarService(
            new Google\Service\Calendar($client),
            $configService->googleCalendarId,
            $logger
        );
    },
    IGoogleSheetService::class => function (ContainerInterface $container) {
        /** @var ConfigService $configService */
        $configService = $container->get(ConfigService::class);
        $client = GoogleClientFetcher::getClient($configService->googleAuthFilePath);

        return new GoogleSheetService(
            new Google\Service\Sheets($client),
            $configService->googleSheetId,
        );
    },
];
