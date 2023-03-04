<?php


namespace app\scheduler\domain;


use app\calendar\domain\CalendarService;
use app\calendar\domain\interfaces\IGoogleCalendarService;
use app\calendar\domain\models\CalendarEvent;
use app\logging\domain\interfaces\ILogger;
use app\sheet\domain\SheetService;
use app\sheet\domain\structs\PersonVacations;
use DateTime;
use DateTimeInterface;
use Exception;

class VacationsScheduler
{
    public function __construct(
        private readonly CalendarService $calendarService,
        private readonly SheetService $sheetService,
        private readonly ILogger $logger,
        private readonly IGoogleCalendarService $googleCalendarService,
    ) {}

    private const VACATION_STRING_TOKEN = 'Отпуск';

    /**
     * @throws Exception
     */
    public function updateVacations(bool $isDryRun = false): void
    {
        $this->logger->log("Started at " . (new DateTime())->format('Y-m-d H:i:s') . "\n");
        $this->logger->log("[1/4] Getting vacations from the Google Sheet...");
        $vacationsByPerson = $this->sheetService->getPersonsVacations();
        $this->logger->log(" Found " . count($vacationsByPerson) . " users with vacations\n");

        $this->logger->log("[2/4] Getting calendar events...");
        $calendarEvents = $this->googleCalendarService->getEvents();
        $this->logger->log(" Found " . count($calendarEvents) . " calendar events\n");

        $this->logger->log("Working with events changes\n");

        $eventsToDelete = $this->getEventsToDelete($vacationsByPerson, $calendarEvents);
        $eventsToDeleteCount = count($eventsToDelete);

        $this->logger->log("[3/4] Found $eventsToDeleteCount events to delete. ");

        if (!$eventsToDelete) {
            $this->logger->log("Nothing to delete.\n");
        } elseif ($isDryRun) {
            $this->logger->log(" Running dry run, no event deletion is made\n");
        } else {
            $this->logger->log("Deleting... ");
            $this->calendarService->deleteEvents($eventsToDelete);
            $this->logger->log("done.\n");
        }

        $eventsToCreate = $this->getEventsToCreate($vacationsByPerson, $calendarEvents);
        $eventsToCreateCount = count($eventsToCreate);
        $this->logger->log("[4/4] Found $eventsToCreateCount events to create. ");

        if (!$eventsToCreateCount) {
            $this->logger->log("Nothing to create.\n");
        } elseif ($isDryRun) {
            $this->logger->log(" Running dry run, no event creation is made\n");
        } else {
            $this->logger->log("Creating... ");
            $this->calendarService->createEvents($eventsToCreate);
            $this->logger->log("done.\n");
        }
    }

    /**
     * @param PersonVacations[] $vacationsByPerson
     * @param CalendarEvent[] $calendarEvents
     * @return CalendarEvent[]
     * @throws Exception
     */
    private function getEventsToDelete(array $vacationsByPerson, array $calendarEvents): array
    {
        $shouldEventBeDeleted = function(CalendarEvent $event) use ($vacationsByPerson) {
            // E.g.: 'Отпуск Богатиков Александр'
            $eventName = $event->name;

            // Deleting the 'Отпуск ' substring at the beginning of the string
            $regexp = "^" . self::VACATION_STRING_TOKEN . ' ';
            $personName = mb_eregi_replace($regexp, '', $eventName);

            foreach ($vacationsByPerson as $personVacations) {
                if (
                    $personName === $personVacations->personName
                    && $personVacations->hasVacationInDates($event->startDate, $event->endDate)
                ) {
                    return false;
                }
            }

            return true;
        };

        return array_filter($calendarEvents, $shouldEventBeDeleted);
    }

    /**
     * @param PersonVacations[] $vacationsByPerson
     * @param CalendarEvent[] $existingCalendarEvents
     * @return array
     */
    private function getEventsToCreate(array $vacationsByPerson, array $existingCalendarEvents): array
    {
        /** @var CalendarEvent[] $eventsToCreate */
        $eventsToCreate = [];

        foreach ($vacationsByPerson as $personVacations) {
            $eventName = self::VACATION_STRING_TOKEN . " " . $personVacations->personName;

            foreach ($personVacations->vacations as $vacation) {
                $startDate = $vacation->start;
                $endDate = $vacation->end;

                if (!static::isEventExists($existingCalendarEvents, $eventName, $startDate, $endDate)) {
                    $eventsToCreate[] = new CalendarEvent(
                        name: $eventName,
                        startDate: $startDate,
                        endDate: $endDate,
                    );
                }
            }
        }

        return $eventsToCreate;
    }

    /**
     * @param CalendarEvent[] $calendarEvents
     * @param string $eventName
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return bool
     */
    private static function isEventExists(
        array $calendarEvents,
        string $eventName,
        DateTime $startDate,
        DateTime $endDate,
    ): bool
    {
        foreach ($calendarEvents as $event) {
            if (
                $event->name === $eventName
                && $event->startDate->format(DateTimeInterface::ATOM) === $startDate->format(DateTimeInterface::ATOM)
                && $event->endDate->format(DateTimeInterface::ATOM) === $endDate->format(DateTimeInterface::ATOM)
            ) {
                return true;
            }
        }

        return false;
    }
}
