<?php

namespace app\calendar\domain\interfaces;

use app\calendar\domain\models\CalendarEvent;

interface IGoogleCalendarService
{
    /**
     * @param CalendarEvent $event
     * @return void
     */
    public function createEvent(CalendarEvent $event): void;

    /**
     * @param $eventId
     * ID of GoogleCalendarEvent
     * @return void
     */
    public function deleteEvent($eventId): void;

    /**
     * @return CalendarEvent[]
     */
    public function getEvents(): array;
}
