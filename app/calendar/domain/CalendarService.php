<?php


namespace app\calendar\domain;


use app\calendar\domain\interfaces\IGoogleCalendarService;
use app\calendar\domain\models\CalendarEvent;
use app\logging\domain\interfaces\ILogger;

class CalendarService
{
    public function __construct(
        private readonly IGoogleCalendarService $googleService,
        private readonly ILogger $logger
    ) {}

    /**
     * @param CalendarEvent[] $events
     */
    public function deleteEvents(array $events): void
    {
        if (empty($events)) {
            $this->logger->log("No events to delete\n");
            return;
        }

        $this->logger->progressStart(count($events));

        foreach ($events as $event) {
            $this->googleService->deleteEvent($event->externalId);
            $this->logger->progressAdvance();
        }
    }

    /**
     * @param CalendarEvent[] $events
     */
    public function createEvents(array $events): void
    {
        if (empty($events)) {
            $this->logger->log("No events to create\n");
            return;
        }

        $this->logger->progressStart(count($events));

        foreach ($events as $event) {
            $this->googleService->createEvent($event);
            $this->logger->progressAdvance();
        }
    }
}
