<?php

namespace unit\sheet;

use app\sheet\domain\exceptions\ServerCurrentlyUnavailableException;
use PHPUnit\Framework\TestCase;

class ServerCurrentlyUnavailableExceptionTest extends TestCase
{
    public function testExceptionCheck()
    {
        $this->assertTrue(
            ServerCurrentlyUnavailableException::areSymptomsMatch(
                503,
                'The service is currently unavailable.',
            )
        );

        $this->assertFalse(
            ServerCurrentlyUnavailableException::areSymptomsMatch(
                501,
                'The service is currently unavailable.',
            )
        );

        $this->assertFalse(
            ServerCurrentlyUnavailableException::areSymptomsMatch(
                503,
                'qweqwe',
            )
        );
    }
}
