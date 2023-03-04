<?php


namespace app\sheet\domain\structs;


use DateTime;

class Vacation
{
    public function __construct(
        public readonly DateTime $start,
        public readonly DateTime $end
    ) {}
}
