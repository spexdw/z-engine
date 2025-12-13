<?php

namespace ZEngine\Core\Services;

class CacheService
{
    private string $cacheDir;
    private int $defaultTtl;

    public function __construct(array $config = [])
    {
        $this->cacheDir = $config['path'] ?? dirname(__DIR__, 2) . '/storage/cache';
        $this->defaultTtl = $config['ttl'] ?? 3600;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->autoCleanup();

        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires_at'] < time()) {
            $this->forget($key);
            return $default;
        }

        return $data['value'];
    }

    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $file = $this->getFilePath($key);

        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public function forever(string $key, mixed $value): bool
    {
        return $this->put($key, $value, 315360000);
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function forget(string $key): bool
    {
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    public function flush(): bool
    {
        $files = glob($this->cacheDir . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->put($key, $value, $ttl);

        return $value;
    }

    public function increment(string $key, int $value = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $value;
        $this->put($key, $new);

        return $new;
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->increment($key, -$value);
    }

    private function getFilePath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    public function cleanup(): int
    {
        $maxAge = 3 * 3600; // 3 hours
        $cutoffTime = time() - $maxAge;
        $deleted = 0;

        $files = glob($this->cacheDir . '/*.cache');

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            if (filemtime($file) < $cutoffTime) {
                if (unlink($file)) {
                    $deleted++;
                }
                continue;
            }

            try {
                $data = @unserialize(file_get_contents($file));
                if ($data && isset($data['expires_at']) && $data['expires_at'] < time()) {
                    if (unlink($file)) {
                        $deleted++;
                    }
                }
            } catch (\Exception $e) {
                @unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    private function autoCleanup(): void
    {
        if (rand(1, 100) <= 2) {
            $this->cleanup();
        }
    }
}
