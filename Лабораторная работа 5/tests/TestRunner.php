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
            'failed' => $this->failed,
            'coverage' => $this->calculateCoverage()
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

    private function calculateCoverage(): array
    {
        // Простая оценка покрытия на основе количества пройденных тестов
        $totalTests = count($this->tests);
        $coverage = $totalTests > 0 ? ($this->passed / $totalTests) * 100 : 0;

        return [
            'percentage' => round($coverage, 2),
            'classes' => [
                'Arguments' => $this->getArgumentsCoverage(),
                'UUID' => $this->getUUIDCoverage(),
                'User' => $this->getUserCoverage(),
                'Post' => $this->getPostCoverage(),
                'Comment' => $this->getCommentCoverage()
            ]
        ];
    }

    private function getArgumentsCoverage(): float
    {
        // Считаем количество тестов для Arguments
        $argumentsTests = 0;
        $totalArgumentsTests = 3; // notEmpty, notNull, stringNotEmpty
        
        foreach ($this->results as $result) {
            if (stripos($result['name'], 'arguments') !== false && $result['status'] === 'passed') {
                $argumentsTests++;
            }
        }
        
        return min(100.0, ($argumentsTests / $totalArgumentsTests) * 100);
    }

    private function getUUIDCoverage(): float
    {
        $uuidTests = 0;
        $totalUUIDTests = 3; // generation, validation, equals
        
        foreach ($this->results as $result) {
            if (stripos($result['name'], 'uuid') !== false && $result['status'] === 'passed') {
                $uuidTests++;
            }
        }
        
        return min(100.0, ($uuidTests / $totalUUIDTests) * 100);
    }

    private function getUserCoverage(): float
    {
        $userTests = 0;
        $totalUserTests = 2; // creation, validation
        
        foreach ($this->results as $result) {
            if (stripos($result['name'], 'user') !== false && 
                stripos($result['name'], 'arguments') === false && 
                stripos($result['name'], 'uuid') === false &&
                $result['status'] === 'passed') {
                $userTests++;
            }
        }
        
        return min(100.0, ($userTests / $totalUserTests) * 100);
    }

    private function getPostCoverage(): float
    {
        $postTests = 0;
        $totalPostTests = 2; // creation, validation (repository tests counted separately)
        
        foreach ($this->results as $result) {
            if (stripos($result['name'], 'post') !== false && 
                stripos($result['name'], 'repository') === false &&
                stripos($result['name'], 'comments') === false &&
                $result['status'] === 'passed') {
                $postTests++;
            }
        }
        
        return min(100.0, ($postTests / $totalPostTests) * 100);
    }

    private function getCommentCoverage(): float
    {
        $commentTests = 0;
        $totalCommentTests = 2; // creation, validation (repository tests counted separately)
        
        foreach ($this->results as $result) {
            if (stripos($result['name'], 'comment') !== false && 
                stripos($result['name'], 'repository') === false &&
                $result['status'] === 'passed') {
                $commentTests++;
            }
        }
        
        return min(100.0, ($commentTests / $totalCommentTests) * 100);
    }
}

