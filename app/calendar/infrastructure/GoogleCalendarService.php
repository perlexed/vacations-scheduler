<?php

namespace app\calendar\infrastructure;

use app\calendar\domain\interfaces\IGoogleCalendarService;
use app\calendar\domain\models\CalendarEvent;
use app\logging\domain\interfaces\ILogger;
use Exception;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleCalendarEvent;

class GoogleCalendarService implements IGoogleCalendarService
{
    public function __construct(
        private readonly GoogleCalendar $service,
        private readonly string $calendarId,
        private readonly ILogger $logger
    ) {}

    public function createEvent(CalendarEvent $event): void
    {
        @$this->service->events->insert(
            $this->calendarId,
            CalendarEventConverter::convertToGoogleEvent($event),
        );
    }

    public function deleteEvent($eventId): void
    {
        @$this->service->events->delete(
            $this->calendarId,
            $eventId,
        );
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getEvents(): array
    {
        /** @var GoogleCalendarEvent[] $events */
        $events = [];
        $nextPageToken = null;

        do {
            if ($nextPageToken !== null) {
                $this->logger->log(".");
            }

            $optParams = [
                'showDeleted' => false,
            ];

            if ($nextPageToken !== null) {
                $optParams['pageToken'] = $nextPageToken;
            }

            $results = @$this->service->events->listEvents(
                $this->calendarId,
                $optParams,
            );

            $events = array_merge(
                $events,
                @$results->getItems(),
            );

            $nextPageToken = $results->nextPageToken;
        } while($nextPageToken !== null);

        return array_map(
            [CalendarEventConverter::class, 'convertFromGoogleEvent'],
            $events,
        );
    }
}
