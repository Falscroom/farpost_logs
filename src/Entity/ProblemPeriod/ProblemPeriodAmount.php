<?php

declare(strict_types=1);

namespace Log\Entity\ProblemPeriod;

use DateTime;
use Log\Entity\Log;

class ProblemPeriodAmount implements ProblemPeriodInterface
{
    protected const SUCCESS_LOGS_AFTER_ERROR = 100;

    private int $successLogsAfterError = 0;
    private int $successLogs = 0;
    private int $errorLogs = 0;
    private ?DateTime $dateTimeStart = null;
    private ?DateTime $dateTimeEnd = null;

    public function isEnded(): bool
    {
        return $this->successLogsAfterError >= static::SUCCESS_LOGS_AFTER_ERROR;
    }

    public function addLog(Log $log): ProblemPeriodInterface
    {
        $this->updateDateTimes($log);

        if ($log->isError()) {
            $this->errorLogs++;
            $this->successLogsAfterError = 0;
            return $this;
        }

        $this->successLogsAfterError++;
        $this->successLogs++;
        return $this;
    }

    public function getSLA(): float
    {
        if ($this->successLogs + $this->errorLogs === 0) {
            return 0.0;
        }

        return (100.0 * $this->successLogs) / ($this->successLogs + $this->errorLogs);
    }

    public function getStart(): DateTime
    {
        return $this->dateTimeStart;
    }

    public function getEnd(): DateTime
    {
        return $this->dateTimeEnd;
    }

    private function updateDateTimes(Log $log): void
    {
        if (!isset($this->dateTimeStart)) {
            $this->dateTimeStart = $log->getDateTime();
        }

        if ($this->dateTimeEnd < $log->getDateTime()) {
            $this->dateTimeEnd = $log->getDateTime();
        }
    }
}