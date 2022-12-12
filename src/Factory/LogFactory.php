<?php

declare(strict_types=1);

namespace Log\Factory;

use DateTime;
use DateTimeZone;
use Log\Entity\Log;

class LogFactory
{
    public function newLog(
        string $datetime,
        string $timezone,
        string $code,
        string $responseTime,
        string $format,
        float $timeLimit
    ): Log {
        return new Log(
            DateTime::createFromFormat($format, $datetime, new DateTimeZone($timezone)),
            (int) $code,
            (float) $responseTime,
            $timeLimit
        );
    }
}