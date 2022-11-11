<?php

declare(strict_types=1);

namespace Qase\PHPUnit;

use Qase\PhpClientUtils\RunResult;

class RunResultCollection
{
    private RunResult $runResult;
    private bool $isReportingEnabled;

    public function __construct(RunResult $runResult, bool $isReportingEnabled)
    {
        $this->isReportingEnabled = $isReportingEnabled;
        $this->runResult = $runResult;
    }

    public function get(): RunResult
    {
        return $this->runResult;
    }

    public function add(string $status, string $test, float $time, string $message = null): void
    {
        if (!$this->isReportingEnabled) {
            return;
        }

        $this->runResult->addResult([
            'status' => $status,
            'time' => $time,
            'full_test_name' => $test,
            'stacktrace' => $status === Reporter::FAILED ? $message : null,
            'defect' => $status === Reporter::FAILED,
        ]);
    }
}
