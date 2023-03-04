<?php


namespace app\sheet\domain\structs;


use DateTime;
use DateTimeInterface;

class PersonVacations
{
    /**
     * @var Vacation[]
     */
    public readonly array $vacations;

    public function __construct(
        public readonly string $personName,
        array $vacations,
    ) {
        $this->vacations = $vacations;
    }

    public function hasVacationInDates(DateTime $start, DateTime $end): bool
    {
        foreach ($this->vacations as $vacation) {
            if (
                static::areDateTimeEquals($vacation->start, $start)
                && static::areDateTimeEquals($vacation->end, $end)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Internal DateTime comparison by date and time (up to seconds)
     *
     * @param DateTime $date1
     * @param DateTime $date2
     * @return bool
     */
    private static function areDateTimeEquals(DateTime $date1, DateTime $date2): bool
    {
        return $date1->format(DateTimeInterface::ATOM) === $date2->format(DateTimeInterface::ATOM);
    }
}
