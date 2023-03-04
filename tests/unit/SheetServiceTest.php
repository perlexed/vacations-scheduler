<?php

namespace unit;

use app\sheet\domain\interfaces\IGoogleSheetService;
use app\sheet\domain\SheetService;
use app\sheet\domain\structs\PersonVacations;
use app\sheet\domain\structs\Vacation;
use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class SheetServiceTest extends TestCase
{
    private array $testColumns;

    /** @var PersonVacations[] */
    private array $testVacations;

    public function setUp(): void
    {
        $this->testColumns = [
            [],
            ['Иван Иванов'],
            ['07.06.2018', '09.06.2018'],
            [],
        ];

        $defaultTimezone = new DateTimeZone('Asia/Krasnoyarsk');

        $startDateTime = new DateTime();
        $startDateTime
            ->setTimezone($defaultTimezone)
            ->setDate(2018, 6, 7)
            ->setTime(0, 0, 0);

        $endDateTime = new DateTime();
        $endDateTime
            ->setTimezone($defaultTimezone)
            ->setDate(2018, 6, 9)
            ->setTime(23, 59, 59);

        $personVacations1 = new PersonVacations(
            personName: 'Иван Иванов',
            vacations: [new Vacation($startDateTime, $endDateTime)],
        );

        $this->testVacations = [
            $personVacations1,
        ];
    }

    public function testVacationsGetter()
    {
        $googleSheetService = $this->createMock(IGoogleSheetService::class);
        $googleSheetService
            ->expects($this->once())
            ->method('getDataColumns')
            ->willReturn($this->testColumns);

        $sheetService = new SheetService($googleSheetService);

        $this->assertEquals(
            $this->testVacations,
            $sheetService->getPersonsVacations(),
        );
    }
}
