<?php

namespace app\logging\domain\interfaces;

interface ILogger
{
    public function log(string $logText): void;

    /**
     * Mark the start of the progress with the total of $totalElementsCount elements
     *
     * @param int $totalElementsCount
     * @return void
     */
    public function progressStart(int $totalElementsCount): void;

    /**
     * Mark one step in the current progress
     *
     * @return void
     */
    public function progressAdvance(): void;
}
