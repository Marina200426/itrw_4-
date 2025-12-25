<?php

require_once __DIR__ . '/LoggerInterface.php';

class FileLogger implements LoggerInterface
{
    private string $logFile;
    private $fileHandle = null;

    public function __construct(string $logFile)
    {
        $this->logFile = $logFile;
        // Создаем директорию для логов, если её нет
        $logDir = dirname($logFile);
        if ($logDir && !is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
    }

    private function writeLog(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        if ($this->fileHandle === null) {
            $this->fileHandle = fopen($this->logFile, 'a');
            if ($this->fileHandle === false) {
                throw new RuntimeException("Cannot open log file: {$this->logFile}");
            }
        }
        
        fwrite($this->fileHandle, $logEntry);
        fflush($this->fileHandle);
    }

    public function info(string $message): void
    {
        $this->writeLog('INFO', $message);
    }

    public function warning(string $message): void
    {
        $this->writeLog('WARNING', $message);
    }

    public function error(string $message): void
    {
        $this->writeLog('ERROR', $message);
    }

    public function __destruct()
    {
        if ($this->fileHandle !== null) {
            fclose($this->fileHandle);
        }
    }
}

