<?php

namespace app\config\infrastructure;

use Exception;

class ConfigService
{
    public string $googleCalendarId;

    public string $googleSheetId;

    public string $googleAuthFilePath;

    /**
     * @throws Exception
     */
    public function __construct() {
        $this->googleAuthFilePath = $_ENV['APP_ROOT_DIR'] . DIRECTORY_SEPARATOR . 'vacations-scheduler-credentials.json';
        $this->googleSheetId = $_ENV['VACATIONS_SHEET_ID'];
        $this->googleCalendarId = $_ENV['VACATIONS_CALENDAR_ID'];

        $this->validate();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function validate(): void {
        if (!$this->googleCalendarId || !$this->googleSheetId) {
            throw new Exception('Calendar ID and vacations sheet ID must be defined');
        }

        if (!$this->googleAuthFilePath || !file_exists($this->googleAuthFilePath)) {
            throw new Exception("Google credentials file must be created at $this->googleAuthFilePath");
        }
    }
}
