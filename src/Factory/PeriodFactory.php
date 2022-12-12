<?php

namespace Log\Factory;

use Log\Entity\ProblemPeriod\ProblemPeriodAmount;
use Log\Entity\ProblemPeriod\ProblemPeriodInterface;

class PeriodFactory
{
    public function newPeriod(): ProblemPeriodInterface
    {
        return new ProblemPeriodAmount();
    }
}