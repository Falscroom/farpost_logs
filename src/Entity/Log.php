<?php

declare(strict_types=1);

namespace Log\Entity;

use DateTime;

class Log
{
    private DateTime $dateTime;
    private bool $isError;

    public function __construct(
        DateTime $dateTime,
        int $code,
        float $responseTime,
        float $timeLimit
    ) {
        $this->dateTime = $dateTime;
        $this->isError = ($code >= 500 && $code <= 599) || $responseTime > $timeLimit;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function isError(): bool
    {
        return $this->isError;
    }
}