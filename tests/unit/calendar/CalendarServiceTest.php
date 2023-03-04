<?php

namespace unit\calendar;

use app\calendar\domain\CalendarService;
use app\calendar\domain\interfaces\IGoogleCalendarService;
use app\calendar\domain\models\CalendarEvent;
use app\logging\domain\interfaces\ILogger;
use DateTime;
use PHPUnit\Framework\TestCase;

class CalendarServiceTest extends TestCase
{
    private ILogger $loggerStub;

    /** @var CalendarEvent[] */
    private array $testCalendarEvents;

    public function setUp(): void
    {
        $this->loggerStub = $this->createStub(ILogger::class);

        $this->testCalendarEvents = [
            new CalendarEvent(name: 'test event 1', startDate: new DateTime(), endDate: new DateTime(), externalId: '1'),
            new CalendarEvent(name: 'test event 2', startDate: new DateTime(), endDate: new DateTime(), externalId: '2'),
            new CalendarEvent(name: 'test event 3', startDate: new DateTime(), endDate: new DateTime(), externalId: '3'),
        ];
    }

    public function testNoEventsDeletion()
    {
        $googleCalendarServiceMock = $this->createMock(IGoogleCalendarService::class);
        $googleCalendarServiceMock
            ->expects($this->never())
            ->method('deleteEvent');

        $calendarService = new CalendarService($googleCalendarServiceMock, $this->loggerStub);

        $calendarService->deleteEvents([]);
    }

    public function testEventsDeletion()
    {
        $googleCalendarServiceMock = $this->createMock(IGoogleCalendarService::class);
        $googleCalendarServiceMock
            ->expects($this->exactly(count($this->testCalendarEvents)))
            ->method('deleteEvent')
            ->withConsecutive(
                [$this->testCalendarEvents[0]->externalId],
                [$this->testCalendarEvents[1]->externalId],
                [$this->testCalendarEvents[2]->externalId],
            );

        $calendarService = new CalendarService($googleCalendarServiceMock, $this->loggerStub);

        $calendarService->deleteEvents($this->testCalendarEvents);
    }

    public function testNoEventsCreation()
    {
        $googleCalendarServiceMock = $this->createMock(IGoogleCalendarService::class);
        $googleCalendarServiceMock
            ->expects($this->never())
            ->method('createEvent');

        $calendarService = new CalendarService($googleCalendarServiceMock, $this->loggerStub);

        $calendarService->deleteEvents([]);
    }

    public function testEventsCreation()
    {
        $googleCalendarServiceMock = $this->createMock(IGoogleCalendarService::class);
        $googleCalendarServiceMock
            ->expects($this->exactly(count($this->testCalendarEvents)))
            ->method('createEvent')
            ->withConsecutive(
                [$this->testCalendarEvents[0]],
                [$this->testCalendarEvents[1]],
                [$this->testCalendarEvents[2]],
            );

        $calendarService = new CalendarService($googleCalendarServiceMock, $this->loggerStub);

        $calendarService->createEvents($this->testCalendarEvents);
    }
}
