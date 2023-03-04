<?php

namespace unit\sheet;

use app\sheet\domain\structs\PersonVacations;
use app\sheet\domain\structs\Vacation;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

class PersonVacationsTest extends TestCase
{
    /** @var Vacation[] */
    private array $vacations;

    public function setUp(): void
    {
        $vacationsDates = [
            ['2022-06-07', '2022-07-22'],
            ['2022-06-07', '2022-07-25'],
            ['2021-06-07', '2022-07-25'],
            ['2021-06-07', '2021-05-07'],
        ];

        $this->vacations = array_map(
            /**
             * @throws Exception
             */
            fn($vacationDates) => (new Vacation(new DateTime($vacationDates[0]), new DateTime($vacationDates[1]))),
            $vacationsDates,
        );
    }

    public function testHasVacationInDates()
    {
        $personVacations = new PersonVacations(
            personName: 'Ivan Petrov',
            vacations: $this->vacations,
        );

        $this->assertEquals(
            true,
            $personVacations->hasVacationInDates($this->vacations[0]->start, $this->vacations[0]->end),
        );
        $this->assertEquals(
            false,
            $personVacations->hasVacationInDates(new DateTime('2022-06-07'), new DateTime('2022-07-23')),
        );
    }
}
