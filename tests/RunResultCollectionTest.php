<?php

namespace Tests;

use Qase\PhpClientUtils\RunResult;
use Qase\PHPUnit\RunResultCollection;
use PHPUnit\Framework\TestCase;

class RunResultCollectionTest extends TestCase
{

    /**
     * @dataProvider autoCreateDefectDataProvider
     */
    public function testAutoCreateDefect(string $title, string $status, float $time, bool $expected)
    {
        $runResult = $this->getMockBuilder(RunResult::class)
            ->setConstructorArgs(['PRJ', null, true])
            ->getMock();

        $runResult->expects($this->once())
            ->method('addResult')
            ->with(
                $this->callback(function ($result) use ($expected) {
                    return isset($result['defect']) && $result['defect'] === $expected;
                })
            );

        $runResultCollection = new RunResultCollection($runResult, true);
        $runResultCollection->add($status, $title, $time);
    }

    public function autoCreateDefectDataProvider(): array
    {
        return [
            ['Test (Qase ID: 1)', 'failed', 1, true],
            ['Test (Qase ID: 2)', 'passed', 1, false],
            ['Test (Qase ID: 3)', 'skipped', 1, false],
            ['Test (Qase ID: 4)', 'disabled', 1, false],
            ['Test (Qase ID: 5)', 'pending', 1, false],
        ];
    }

    public function testGettingRunResultFromCollection()
    {
        $runResult = new RunResult('PRJ', 1, true, null);
        $runResultCollection = new RunResultCollection($runResult, true);
        $this->assertInstanceOf(RunResult::class, $runResultCollection->get());
    }

    public function testResultCollectionIsEmptyWhenReportingIsDisabled()
    {
        $runResult = new RunResult('PRJ', 1, true, null);
        $runResultCollection = new RunResultCollection($runResult, false);

        $runResultCollection->add('failed', 'Test 6', 1, 'Testing message');

        $runResultWithoutResults = $runResultCollection->get();
        $this->assertEmpty($runResultWithoutResults->getResults());
    }

    public function testAddingResults()
    {
        $runResult = new RunResult('PRJ', 1, true, null);
        $runResultCollection = new RunResultCollection($runResult, true);
        $runResultWithoutResults = $runResultCollection->get();
        $this->assertEmpty($runResultWithoutResults->getResults());

        $stackTraceMessage = 'Stack trace text';
        $runResultCollection->add('failed', 'Test 7', 1, $stackTraceMessage);
        $runResultCollection->add('passed', 'Test 8', 0.375);

        $runResultWithResults = $runResultCollection->get();

        $expectedResult = [
            [
                'status' => 'failed',
                'time' => 1.0,
                'full_test_name' => 'Test 7',
                'stacktrace' => $stackTraceMessage,
                'defect' => true,
            ],
            [
                'status' => 'passed',
                'time' => 0.375,
                'full_test_name' => 'Test 8',
                'stacktrace' => null,
                'defect' => false,
            ],
        ];

        $this->assertSame($runResultWithResults->getResults(), $expectedResult);
    }

}
