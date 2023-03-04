<?php

namespace app\sheet\domain\interfaces;

interface IGoogleSheetService
{
    public function getDataColumns(): array;
}