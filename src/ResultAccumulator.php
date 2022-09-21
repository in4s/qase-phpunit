<?php

namespace Qase\PHPUnit;

use Qase\PhpClientUtils\Config;
use Qase\PhpClientUtils\RunResult;

class ResultAccumulator
{
    private Config $config;
    private RunResult $runResult;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->runResult = new RunResult(
            $this->config->getProjectCode(),
            $this->config->getRunId(),
            $this->config->getCompleteRunAfterSubmit(),
            $this->config->getEnvironmentId(),
        );
    }

    public function accumulate(string $status, string $test, float $time, string $message = null): void
    {
        if (!$this->config->isReportingEnabled()) {
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

    public function getRunResult(): RunResult
    {
        return $this->runResult;
    }
}
