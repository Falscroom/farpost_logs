<?php

declare(strict_types=1);

namespace Log\Tests\Services;

use DateTime;
use Log\Entity\Log;
use Log\Entity\ProblemPeriod\ProblemPeriodAmount;
use Log\Entity\ProblemPeriod\ProblemPeriodInterface;
use Exception;
use Log\Factory\PeriodFactory;
use Log\Parser\LogParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Log\Services\ProblemPeriodService;

class ProblemsPeriodServiceTest extends TestCase
{
    private const TIME_LIMIT = 40.0;
    private ProblemPeriodService $service;
    /** @var MockObject|LogParser */
    private MockObject $parser;
    /** @var MockObject|PeriodFactory */
    private MockObject $factory;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->parser = $this->createMock(LogParser::class);
        $this->factory = $this->createMock(PeriodFactory::class);
        
        $this->service = new ProblemPeriodService($this->parser, $this->factory);
    }
    
    public function testRecoverAfterException()
    {
        $this->parser
            ->expects($this->once())
            ->method('parseLog')
            ->willThrowException(new Exception())
        ;
        
        $emptyData = $this->service->getPeriods($this->streamWithIterations(1), 0, 0);
        $this->assertEquals([], $emptyData);
    }
    
    /** @dataProvider periodsDataProvider */
    public function testPeriods(array $logs, array $periods, array $expected, float $sla)
    {
        $this->parser
            ->expects($this->any())
            ->method('parseLog')
            ->willReturnOnConsecutiveCalls(...$logs)
        ;
        $this->factory
            ->expects($this->exactly(count($periods)))
            ->method('newPeriod')
            ->willReturnOnConsecutiveCalls(...$periods)
        ;
        
        $periods = $this->service->getPeriods(
            $this->streamWithIterations(count($logs)),
            self::TIME_LIMIT,
            $sla
        );
         
        $this->assertEquals($expected, $periods);
    }
    
    public function periodsDataProvider(): array
    {
        $periods = $this->getPeriodsArrayWithReducedConstant(2);
        
        $log1 = $this->newLog(500, self::TIME_LIMIT - 1);
        $log2 = $this->newLog(200, self::TIME_LIMIT - 1);
        $log3 = $this->newLog(500, self::TIME_LIMIT - 1);
        $log4 = $this->newLog(500, self::TIME_LIMIT + 1);
        
        $expected = $this->getPeriodsArrayWithReducedConstant(1)[0]
            ->addLog($log3)
            ->addLog($log4)
        ;    
        
       $caseCutOnePeriodBySLA = [[$log1, $log2, $log3, $log4], $periods, [$expected], 40.0];

        $periods = $this->getPeriodsArrayWithReducedConstant(3);

        $log1 = $this->newLog(500, self::TIME_LIMIT - 1);
        $log2 = $this->newLog(200, self::TIME_LIMIT - 1);
        $log3 = $this->newLog(500, self::TIME_LIMIT - 1);
        $log4 = $this->newLog(200, self::TIME_LIMIT - 1);
        $log5 = $this->newLog(500, self::TIME_LIMIT - 1);
        $log6 = $this->newLog(500, self::TIME_LIMIT - 1);

        $expected = $this->getPeriodsArrayWithReducedConstant(3);
        $expected[0]
            ->addLog($log1)
            ->addLog($log2)
        ;
        
        $expected[1]
            ->addLog($log3)
            ->addLog($log4)
        ;
        
        $expected[2]
            ->addLog($log5)
            ->addLog($log6)
        ;

        $caseThreePeriods = [[$log1, $log2, $log3, $log4, $log5, $log6], $periods, $expected, 60.0];

        return [$caseCutOnePeriodBySLA, $caseThreePeriods];
    }
    
    /** @return ProblemPeriodInterface[] */
    private function getPeriodsArrayWithReducedConstant(int $amount): array
    {
        $periods = [];
        for ($i = 0; $i < $amount; $i++) {
            $periods[] = new class extends ProblemPeriodAmount {
                protected const SUCCESS_LOGS_AFTER_ERROR = 1;
            };
        }
        
        return $periods;
    }
    
    private function newLog(int $code, float $time): Log
    {
        return new Log(new DateTime(), $code, $time, self::TIME_LIMIT);
    }
    
    private function streamWithIterations(int $amount)
    {
        $data = '';
        //Because while 0 is false
        for ($i = 1; $i <= $amount; $i++) {
            $data .= "$i" . PHP_EOL;
        }
        
        $stream = fopen('php://memory','r+');
        fwrite($stream, $data);
        rewind($stream);
        
        return $stream;
    }
}