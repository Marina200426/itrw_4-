<?php

class TestRunner
{
    private array $tests = [];
    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;

    public function addTest(string $name, callable $test): void
    {
        $this->tests[] = [
            'name' => $name,
            'test' => $test
        ];
    }

    public function run(): array
    {
        $this->results = [];
        $this->passed = 0;
        $this->failed = 0;

        foreach ($this->tests as $test) {
            $result = $this->runTest($test['name'], $test['test']);
            $this->results[] = $result;

            if ($result['status'] === 'passed') {
                $this->passed++;
            } else {
                $this->failed++;
            }
        }

        return [
            'results' => $this->results,
            'total' => count($this->tests),
            'passed' => $this->passed,
            'failed' => $this->failed
        ];
    }

    private function runTest(string $name, callable $test): array
    {
        $startTime = microtime(true);
        $error = null;
        $status = 'passed';

        try {
            $test();
        } catch (Throwable $e) {
            $status = 'failed';
            $error = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        }

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        return [
            'name' => $name,
            'status' => $status,
            'duration' => $duration,
            'error' => $error
        ];
    }
}

