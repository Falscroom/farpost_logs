<?php

declare(strict_types=1);

namespace Log\Command;

use Log\Services\ProblemPeriodService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnalyzeCommand extends Command
{
    private ProblemPeriodService $problemPeriodService;

    public function __construct(ProblemPeriodService $problemPeriodService)
    {
        $this->problemPeriodService = $problemPeriodService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('logs:analyze')
            ->setDescription('analyze all logs')
            ->addArgument('timeLimit', InputArgument::REQUIRED, 'Response time limit')
            ->addArgument('slaLimit', InputArgument::REQUIRED, 'SLA limit')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (float) $input->getArgument('timeLimit');
        $limitSLA = (float) $input->getArgument('slaLimit');

        $style = new SymfonyStyle($input, $output);
        $periods = $this->problemPeriodService->getPeriods(STDIN, $limit, $limitSLA);

        foreach ($periods as $period) {
            $timePeriod = "{$period->getStart()->format('H:i:s')} {$period->getEnd()->format('H:i:s')}";
            $sla = number_format($period->getSLA(), 2);
            $style->writeln("$timePeriod $sla");
        }

        return Command::SUCCESS;
    }
}