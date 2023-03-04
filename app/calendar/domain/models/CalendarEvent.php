<?php

namespace app\calendar\domain\models;

use DateTime;

class CalendarEvent
{
    public function __construct(
        public readonly string $name,
        public readonly DateTime $startDate,
        public readonly DateTime $endDate,
        public readonly ?string $externalId = '',
    ) {}
}
