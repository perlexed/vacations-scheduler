<?php


namespace app\google\infrastructure;


use Google\Exception;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Sheets as GoogleSheets;

class GoogleClientFetcher
{
    private static string $applicationName = 'Vacations Scheduler';
    private static array $applicationScopes = [
        GoogleCalendar::CALENDAR,
        GoogleSheets::SPREADSHEETS_READONLY,
    ];

    /**
     * Returns an authorized API client.
     * @return GoogleClient the authorized client object
     * @throws Exception
     */
    public static function getClient(string $googleAuthFilePath): GoogleClient
    {
        $client = new GoogleClient();
        $client->setApplicationName(static::$applicationName);
        $client->setScopes(static::$applicationScopes);
        $client->setAuthConfig($googleAuthFilePath);

        return $client;
    }
}
