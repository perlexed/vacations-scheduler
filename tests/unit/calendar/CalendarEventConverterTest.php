<?php

namespace unit\calendar;

use app\calendar\domain\models\CalendarEvent;
use app\calendar\infrastructure\CalendarEventConverter;
use DateTime;
use Exception;
use Google\Service\Calendar\Event as GoogleCalendarEvent;
use Google\Service\Calendar\EventDateTime as GoogleCalendarEventDateTime;
use PHPUnit\Framework\TestCase;

class CalendarEventConverterTest extends TestCase
{
    private CalendarEvent $event;
    private GoogleCalendarEvent $googleEvent;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $name = 'test event';
        $id = 'test id';
        $startDate = '2022-01-01T23:59:59+09:00';
        $endDate = '2022-01-02T23:59:59+09:00';

        $this->event = new CalendarEvent(
            name: $name,
            startDate: new DateTime($startDate),
            endDate: new DateTime($endDate),
            externalId: $id,
        );

        $this->googleEvent = new GoogleCalendarEvent();
        $this->googleEvent->id = $id;
        $this->googleEvent->summary = $name;
        $this->googleEvent->setStart(new GoogleCalendarEventDateTime(['dateTime' => $startDate]));
        $this->googleEvent->setEnd(new GoogleCalendarEventDateTime(['dateTime' => $endDate]));
    }

    public function testConvertingToGoogleEvent()
    {
        $googleEvent = CalendarEventConverter::convertToGoogleEvent($this->event);

        $this->assertEquals($this->event->name, $googleEvent->summary);
        $this->assertEquals(
            $this->event->startDate->format(\DateTimeInterface::ATOM),
            $googleEvent->getStart()->getDateTime()
        );
        $this->assertEquals(
            $this->event->endDate->format(\DateTimeInterface::ATOM),
            $googleEvent->getEnd()->getDateTime()
        );
    }

    /**
     * @throws Exception
     */
    public function testConvertingFromGoogleEvent()
    {
        $event = CalendarEventConverter::convertFromGoogleEvent($this->googleEvent);

        $this->assertEquals($this->googleEvent->id, $event->externalId);
        $this->assertEquals($this->googleEvent->summary, $event->name);
        $this->assertEquals(
            new DateTime($this->googleEvent->getStart()->getDateTime()),
            $event->startDate,
        );
        $this->assertEquals(
            new DateTime($this->googleEvent->getEnd()->getDateTime()),
            $event->endDate,
        );
    }
}
