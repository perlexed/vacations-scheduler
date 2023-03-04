<?php

namespace app\calendar\infrastructure;

use app\calendar\domain\models\CalendarEvent;
use DateTime;
use DateTimeInterface;
use Exception;
use Google\Service\Calendar\Event as GoogleCalendarEvent;
use Google\Service\Calendar\EventDateTime as GoogleCalendarEventDateTime;

class CalendarEventConverter
{
    public static function convertToGoogleEvent(CalendarEvent $event): GoogleCalendarEvent
    {
        $eventStart = new GoogleCalendarEventDateTime(['dateTime' => $event->startDate->format(DateTimeInterface::ATOM)]);
        $eventEnd = new GoogleCalendarEventDateTime(['dateTime' => $event->endDate->format(DateTimeInterface::ATOM)]);
        $googleCalendarEvent = new GoogleCalendarEvent([
            'summary' => $event->name,
        ]);
        $googleCalendarEvent->setStart($eventStart);
        $googleCalendarEvent->setEnd($eventEnd);

        return $googleCalendarEvent;
    }

    /**
     * @throws Exception
     */
    public static function convertFromGoogleEvent(GoogleCalendarEvent $googleEvent): CalendarEvent
    {
        return new CalendarEvent(
            name: $googleEvent->summary,
            startDate: new DateTime($googleEvent->getStart()->dateTime),
            endDate: new DateTime($googleEvent->getEnd()->dateTime),
            externalId: $googleEvent->id,
        );
    }
}
