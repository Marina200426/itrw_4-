<?php

require_once __DIR__ . '/LoggerInterface.php';

class TestLogger implements LoggerInterface
{
    private array $logs = [];

    public function info(string $message): void
    {
        $this->logs[] = ['level' => 'INFO', 'message' => $message, 'timestamp' => time()];
    }

    public function warning(string $message): void
    {
        $this->logs[] = ['level' => 'WARNING', 'message' => $message, 'timestamp' => time()];
    }

    public function error(string $message): void
    {
        $this->logs[] = ['level' => 'ERROR', 'message' => $message, 'timestamp' => time()];
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function clearLogs(): void
    {
        $this->logs = [];
    }

    public function getLogsByLevel(string $level): array
    {
        return array_filter($this->logs, function($log) use ($level) {
            return $log['level'] === $level;
        });
    }
}

