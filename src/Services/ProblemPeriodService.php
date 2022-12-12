<?php

declare(strict_types=1);

namespace Log\Services;

use Log\Entity\ProblemPeriod\ProblemPeriodInterface;
use Log\Factory\PeriodFactory;
use Log\Parser\LogParser;
use Throwable;

class ProblemPeriodService
{
    private LogParser $parser;
    private PeriodFactory $factory;

    public function __construct(LogParser $logParserService, PeriodFactory $factory)
    {
        $this->parser = $logParserService;
        $this->factory = $factory;
    }
    
    /** @return ProblemPeriodInterface[] */
    public function getPeriods($inputSteam, float $timeLimit, float $limitSLA): array
    {
        $periods = [];
        while ($line = stream_get_line($inputSteam, 0, PHP_EOL)) {
            try {
                $log = $this->parser->parseLog($line, $timeLimit);
            } catch (Throwable $t) {
                continue;
            }

            if (isset($period) && $period->isEnded()) {
                if ($period->getSLA() < $limitSLA) {
                    $periods[] = $period;
                }
                $period = null;
            }

            if ($log->isError() && !isset($period)) {
                $period = $this->factory->newPeriod();
            }

            if (isset($period)) {
                $period->addLog($log);
            }
        }

        if (isset($period) && !$period->isEnded() && $period->getSLA() < $limitSLA) {
            $periods[] = $period;
        }

        $this->sortPeriods($periods);
        return $periods;
    }

    private function sortPeriods(array $periods)
    {
        usort($periods, function (ProblemPeriodInterface $a, ProblemPeriodInterface $b) {
            return $a->getStart() <=> $b->getStart();
        });
    }
}