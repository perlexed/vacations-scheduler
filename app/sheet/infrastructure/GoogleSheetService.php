<?php

namespace app\sheet\infrastructure;

use app\sheet\domain\exceptions\ServerCurrentlyUnavailableException;
use app\sheet\domain\interfaces\IGoogleSheetService;
use Google\Service\Sheets;
use Google\Service\Exception as GoogleException;

class GoogleSheetService implements IGoogleSheetService
{
    private static string $columnsRange = 'A:B';

    public function __construct(
        private readonly Sheets $service,
        private readonly string $spreadsheetId
    ) {}

    /**
     * @throws ServerCurrentlyUnavailableException
     * @throws GoogleException
     */
    public function getDataColumns(): array
    {
        try {
            $response = @$this->service->spreadsheets_values->get($this->spreadsheetId, static::$columnsRange);
        } catch (GoogleException $exception) {
            $isTemporaryError = ServerCurrentlyUnavailableException::areSymptomsMatch(
                $exception->getCode(),
                $exception->getMessage(),
            );

            $actualException = $isTemporaryError
                ? new ServerCurrentlyUnavailableException()
                : $exception;

            throw $actualException;
        }

        return $response->getValues();
    }
}
