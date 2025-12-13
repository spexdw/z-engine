<?php

namespace ZEngine\Core\Services;

class LoggerService
{
    private string $logDir;
    private string $logFile;

    public function __construct(array $config = [])
    {
        $this->logDir = $config['path'] ?? dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }

        $this->logFile = $this->logDir . '/' . date('Y-m-d') . '.log';
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';

        $logMessage = "[{$timestamp}] {$level}: {$message}";

        if ($contextStr) {
            $logMessage .= " | Context: {$contextStr}";
        }

        $logMessage .= PHP_EOL;

        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function getLogs(int $lines = 100): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $file = file($this->logFile);
        return array_slice($file, -$lines);
    }

    public function clear(): bool
    {
        if (file_exists($this->logFile)) {
            return unlink($this->logFile);
        }

        return false;
    }
}
