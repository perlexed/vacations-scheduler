<?php

namespace app\sheet\domain\exceptions;

use Exception;

class ServerCurrentlyUnavailableException extends Exception
{
    private static int $exceptionCode = 503;
    private static string $exceptionMessage = 'The service is currently unavailable.';

    public static function areSymptomsMatch(int $exceptionCode, string $exceptionMessage): bool
    {
        return $exceptionCode === static::$exceptionCode
            && $exceptionMessage === static::$exceptionMessage;
    }
}
