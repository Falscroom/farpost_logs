<?php

declare(strict_types=1);

namespace Log\Entity\ProblemPeriod;

use DateTime;
use Log\Entity\Log;

interface ProblemPeriodInterface
{
    public function isEnded(): bool;
    public function addLog(Log $log): self;
    public function getSLA(): float;
    public function getStart(): DateTime;
    public function getEnd(): DateTime;
}