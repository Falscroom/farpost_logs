<?php

declare(strict_types=1);

namespace Log\Parser;

use Log\Entity\Log;
use Log\Factory\LogFactory;

class LogParser
{
    public const DATETIME_FORMAT = 'd/m/Y:H:i:s';
    private const SEPARATOR = ' ';
    private const DATETIME_POSITION = 3;
    private const TIMEZONE_POSITION = 4;
    private const CODE_POSITION = 8;
    private const RESPONSE_TIME_POSITION = 10;

    private LogFactory $factory;

    public function __construct(LogFactory $factory)
    {
        $this->factory = $factory;
    }

    public function parseLog(string $line, float $timeLimit): Log
    {
        $preparedLine = preg_replace('/[\[\]]+/', '', $line);
        $explodedLine = explode(self::SEPARATOR, $preparedLine);

        $datetime = $explodedLine[self::DATETIME_POSITION];
        $timezone = $explodedLine[self::TIMEZONE_POSITION];
        $code = $explodedLine[self::CODE_POSITION];
        $responseTime = $explodedLine[self::RESPONSE_TIME_POSITION];

        return $this->factory->newLog(
            $datetime,
            $timezone,
            $code,
            $responseTime,
            self::DATETIME_FORMAT,
            $timeLimit
        );
    }
}