<?php

namespace ZEngine\Core;

use Throwable;
use ErrorException;

class ErrorHandler
{
    private ZEngine $app;
    private bool $debug;

    public function __construct(ZEngine $app)
    {
        $this->app = $app;
        $this->debug = env('APP_DEBUG', false);
    }

    public function register(): void
    {
        error_reporting(E_ALL);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
        ini_set('display_errors', '0');
    }

    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
        return false;
    }

    public function handleException(Throwable $e): void
    {
        $this->renderException($e);
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $this->handleException(
                new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
            );
        }
    }

    private function renderException(Throwable $e): void
    {
        $statusCode = $this->getStatusCode($e);
        http_response_code($statusCode);

        $this->logError($e);

        if ($this->debug) {
            $this->renderDebugPage($e);
        } else {
            $this->renderProductionPage($e, $statusCode);
        }
    }

    private function getStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        return 500;
    }

    private function renderDebugPage(Throwable $e): void
    {
        $file = $e->getFile();
        $line = $e->getLine();
        $message = $e->getMessage();
        $trace = $e->getTraceAsString();
        $code = $this->getCodeSnippet($file, $line);

        include dirname(__DIR__) . '/app/Views/errors/debug.php';
    }

    private function renderProductionPage(Throwable $e, int $statusCode): void
    {
        $errorView = dirname(__DIR__) . "/app/Views/errors/{$statusCode}.php";

        if (!file_exists($errorView)) {
            $errorView = dirname(__DIR__) . '/app/Views/errors/500.php';
        }

        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo "Error {$statusCode}";
        }
    }

    private function getCodeSnippet(string $file, int $line, int $context = 10): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $lines = file($file);
        $start = max(0, $line - $context - 1);
        $end = min(count($lines), $line + $context);

        $snippet = [];
        for ($i = $start; $i < $end; $i++) {
            $snippet[$i + 1] = $lines[$i];
        }

        return $snippet;
    }

    private function logError(Throwable $e): void
    {
        $logDir = dirname(__DIR__) . '/storage/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();

        $logEntry = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n%s\n",
            $timestamp,
            $type,
            $message,
            $file,
            $line,
            $trace,
            str_repeat('-', 80)
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
