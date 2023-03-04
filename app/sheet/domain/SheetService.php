<?php


namespace app\sheet\domain;


use app\sheet\domain\exceptions\SheetDataException;
use app\sheet\domain\interfaces\IGoogleSheetService;
use app\sheet\domain\structs\PersonVacations;
use app\sheet\domain\structs\Vacation;
use DateTime;
use DateTimeZone;

class SheetService
{
    public function __construct(
        private readonly IGoogleSheetService $googleSheetService
    ) {}

    /**
     * @return PersonVacations[]
     */
    public function getPersonsVacations(): array
    {
        $rowsData = $this->googleSheetService->getDataColumns();

        // Groups rows by users by splitting data blocks with empty rows
        $rowsGroups = [];
        $currentGroup = [];
        foreach ($rowsData as $row)
        {
            if (count($row) === 0) {
                if (count($currentGroup) > 0) {
                    $rowsGroups[] = $currentGroup;
                    $currentGroup = [];
                }
            } else {
                $currentGroup[] = $row;
            }
        }
        if (count($currentGroup) > 0) {
            $rowsGroups[] = $currentGroup;
        }

        return array_map([$this, 'getPersonVacations'], $rowsGroups);
    }

    /**
     * @param array $dataRows
     * @return PersonVacations
     * @throws SheetDataException
     */
    private function getPersonVacations(array $dataRows): PersonVacations
    {
        if (empty($dataRows)) {
            throw new SheetDataException('User data group is empty');
        }

        $rowWithName = $dataRows[0];

        if (count($rowWithName) !== 1) {
            throw new SheetDataException('Row with person\'s name must contain 1 cell: ' . print_r($rowWithName, true));
        }

        // Filter out spanned columns and columns which doesn't contain date-like strings (e.g. '8.6.2020')
        $vacationRangesRows = array_filter($dataRows, function($dataRow) {
            return count($dataRow) == 2
                && count(explode('.', $dataRow[0])) === 3
                && count(explode('.', $dataRow[1])) === 3;
        });

        // Reset indices after 'array_filter'
        $vacationRangesRows = array_values($vacationRangesRows);

        return new PersonVacations(
            personName: trim($rowWithName[0]),
            vacations: array_map([$this, 'getVacationFromDataRow'], $vacationRangesRows)
        );
    }

    /**
     * @param array $vacationRow e.g. ['03.08.2020', '7.8.2020']
     * @return Vacation
     * @throws SheetDataException
     */
    private function getVacationFromDataRow(array $vacationRow): Vacation
    {
        $defaultTimezone = new DateTimeZone('Asia/Krasnoyarsk');

        /**
         * @param string $sheetDateTime
         * @return DateTime|null
         */
        $getDateTimeForSheetDate = function(string $sheetDateTime) use ($defaultTimezone) {
            list($day, $month, $year) = explode('.', $sheetDateTime);

            if (strlen($year) !== 4) {
                return null;
            }

            $dateTime = new DateTime();
            $dateTime
                ->setTimezone($defaultTimezone)
                ->setDate((int) $year, (int) $month, (int) $day);

            return $dateTime;
        };

        $start = $getDateTimeForSheetDate($vacationRow[0]);
        $end = $getDateTimeForSheetDate($vacationRow[1]);

        if (!$start || !$end || $start > $end) {
            throw new SheetDataException("Wrong date format: " . print_r($vacationRow, true));
        }

        $start->setTime(0, 0);
        $end->setTime(23, 59, 59);

        return new Vacation($start, $end);
    }
}
