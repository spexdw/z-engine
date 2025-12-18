<?php

namespace ZEngine\Core\Services;

class RateLimitService
{
    private static ?RateLimitService $instance = null;
    private string $storagePath;
    private array $timeWindows = [
        '1second' => 1,
        '1minute' => 60,
        '5minutes' => 300,
        '10minutes' => 600,
        '15minutes' => 900,
        '30minutes' => 1800,
        '1hour' => 3600,
        '6hours' => 21600,
        '12hours' => 43200,
        '1day' => 86400,
        '1week' => 604800,
        '1month' => 2592000,
    ];

    private function __construct()
    {
        $this->storagePath = rtrim(env('RATELIMIT_STORAGE_PATH') ?? __DIR__ . '/../../storage/ratelimit', '/');
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
        $this->cleanup();
    }

    public static function getInstance(): RateLimitService
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function check(string $key, array $limits, ?string $identifier = null): bool|string
    {
        $identifier = $identifier ?? $this->getDefaultIdentifier();
        $rateLimitKey = $this->generateKey($key, $identifier);

        $attempts = $this->getAttempts($rateLimitKey);
        $now = time();

        foreach ($limits as $window => $maxAttempts) {
            $seconds = $this->parseTimeWindow($window);
            if ($seconds === null) {
                continue;
            }

            $recentAttempts = array_filter($attempts, function ($timestamp) use ($now, $seconds) {
                return ($now - $timestamp) <= $seconds;
            });

            if (count($recentAttempts) >= $maxAttempts) {
                $oldestAttempt = min($recentAttempts);
                $retryAfter = $seconds - ($now - $oldestAttempt);
                return $this->formatErrorMessage($window, $maxAttempts, $retryAfter);
            }
        }

        $this->recordAttempt($rateLimitKey, $now);
        return false;
    }

    public function hit(string $key, ?string $identifier = null): void
    {
        $identifier = $identifier ?? $this->getDefaultIdentifier();
        $rateLimitKey = $this->generateKey($key, $identifier);
        $this->recordAttempt($rateLimitKey, time());
    }

    public function remaining(string $key, string $window, int $maxAttempts, ?string $identifier = null): int
    {
        $identifier = $identifier ?? $this->getDefaultIdentifier();
        $rateLimitKey = $this->generateKey($key, $identifier);

        $seconds = $this->parseTimeWindow($window);
        if ($seconds === null) {
            return $maxAttempts;
        }

        $attempts = $this->getAttempts($rateLimitKey);
        $now = time();

        $recentAttempts = array_filter($attempts, function ($timestamp) use ($now, $seconds) {
            return ($now - $timestamp) <= $seconds;
        });

        return max(0, $maxAttempts - count($recentAttempts));
    }

    public function clear(string $key, ?string $identifier = null): void
    {
        $identifier = $identifier ?? $this->getDefaultIdentifier();
        $rateLimitKey = $this->generateKey($key, $identifier);
        $this->deleteAttempts($rateLimitKey);
    }

    public function clearAll(): void
    {
        $files = glob($this->storagePath . '/rl_*');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    private function getAttempts(string $key): array
    {
        $file = $this->storagePath . '/rl_' . $key;
        if (!file_exists($file)) {
            return [];
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            return [];
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['attempts'])) {
            return [];
        }

        return $data['attempts'];
    }

    private function recordAttempt(string $key, int $timestamp): void
    {
        $attempts = $this->getAttempts($key);
        $attempts[] = $timestamp;

        $attempts = array_filter($attempts, function ($ts) use ($timestamp) {
            return ($timestamp - $ts) <= 2592000;
        });

        $data = [
            'key' => $key,
            'attempts' => array_values($attempts),
            'last_attempt' => $timestamp,
        ];

        $file = $this->storagePath . '/rl_' . $key;
        $json = json_encode($data);

        if ($json !== false) {
            @file_put_contents($file, $json, LOCK_EX);
        }
    }

    private function deleteAttempts(string $key): void
    {
        $file = $this->storagePath . '/rl_' . $key;
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    private function generateKey(string $action, string $identifier): string
    {
        return hash('sha256', $action . '|' . $identifier);
    }

    private function getDefaultIdentifier(): string
    {
        return $this->getTrustedIp();
    }

    private function getTrustedIp(): string
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);

            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];

            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    private function parseTimeWindow(string $window): ?int
    {
        if (isset($this->timeWindows[$window])) {
            return $this->timeWindows[$window];
        }

        if (is_numeric($window)) {
            return (int)$window;
        }

        if (preg_match('/^(\d+)(second|minute|hour|day|week|month)s?$/i', $window, $matches)) {
            $amount = (int)$matches[1];
            $unit = strtolower($matches[2]);

            $multipliers = [
                'second' => 1,
                'minute' => 60,
                'hour' => 3600,
                'day' => 86400,
                'week' => 604800,
                'month' => 2592000,
            ];

            return $amount * ($multipliers[$unit] ?? 0);
        }

        return null;
    }

    private function formatErrorMessage(string $window, int $maxAttempts, int $retryAfter): string
    {
        $readableWindow = $this->humanizeTimeWindow($window);
        $readableRetry = $this->humanizeSeconds($retryAfter);

        return "Rate limit exceeded. Maximum {$maxAttempts} attempts per {$readableWindow}. Try again in {$readableRetry}.";
    }

    private function humanizeTimeWindow(string $window): string
    {
        $map = [
            '1second' => '1 second',
            '1minute' => '1 minute',
            '5minutes' => '5 minutes',
            '10minutes' => '10 minutes',
            '15minutes' => '15 minutes',
            '30minutes' => '30 minutes',
            '1hour' => '1 hour',
            '6hours' => '6 hours',
            '12hours' => '12 hours',
            '1day' => '1 day',
            '1week' => '1 week',
            '1month' => '1 month',
        ];

        return $map[$window] ?? $window;
    }

    private function humanizeSeconds(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' second' . ($seconds !== 1 ? 's' : '');
        }

        if ($seconds < 3600) {
            $minutes = ceil($seconds / 60);
            return $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
        }

        if ($seconds < 86400) {
            $hours = ceil($seconds / 3600);
            return $hours . ' hour' . ($hours !== 1 ? 's' : '');
        }

        $days = ceil($seconds / 86400);
        return $days . ' day' . ($days !== 1 ? 's' : '');
    }

    private function cleanup(): void
    {
        if (rand(1, 100) > 5) {
            return;
        }

        $files = glob($this->storagePath . '/rl_*');
        if (!$files) {
            return;
        }

        $now = time();
        $maxAge = 2592000;

        foreach ($files as $file) {
            $content = @file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $data = json_decode($content, true);
            if (!$data || !isset($data['last_attempt'])) {
                @unlink($file);
                continue;
            }

            if (($now - $data['last_attempt']) > $maxAge) {
                @unlink($file);
            }
        }
    }
}
