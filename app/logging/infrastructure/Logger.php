<?php

namespace app\logging\infrastructure;

use app\logging\domain\interfaces\ILogger;
use ProgressBar\Manager as ProgressBar;

class Logger implements ILogger
{
    private ProgressBar $progressBar;

    public function log(string $logText): void
    {
        echo $logText;
    }

    public function progressStart(int $totalElementsCount): void
    {
        $this->progressBar = new ProgressBar(0, $totalElementsCount);
    }

    public function progressAdvance(): void
    {
        $this->progressBar->advance();
    }
}