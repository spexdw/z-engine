<?php

namespace ZEngine\Core\Services;

class SessionService
{
    private bool $started = false;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'name' => 'ZENGINE_SESSION',
            'lifetime' => 7200,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ], $config);
    }

    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
            return true;
        }

        session_name($this->config['name']);

        session_set_cookie_params([
            'lifetime' => $this->config['lifetime'],
            'path' => $this->config['path'],
            'domain' => $this->config['domain'],
            'secure' => $this->config['secure'],
            'httponly' => $this->config['httponly'],
            'samesite' => $this->config['samesite'],
        ]);

        $this->started = session_start();

        return $this->started;
    }

    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    public function delete(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function all(): array
    {
        $this->start();
        return $_SESSION;
    }

    public function clear(): void
    {
        $this->start();
        $_SESSION = [];
    }

    public function destroy(): bool
    {
        $this->start();
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        return session_destroy();
    }

    public function regenerate(bool $deleteOld = true): bool
    {
        $this->start();
        return session_regenerate_id($deleteOld);
    }

    public function flash(string $key, mixed $value): void
    {
        $this->set($key, $value);
        $this->set('_flash_keys', array_merge($this->get('_flash_keys', []), [$key]));
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->delete($key);

        $flashKeys = $this->get('_flash_keys', []);
        if (($index = array_search($key, $flashKeys)) !== false) {
            unset($flashKeys[$index]);
            $this->set('_flash_keys', $flashKeys);
        }

        return $value;
    }

    public function keep(array $keys): void
    {
        $flashKeys = $this->get('_flash_keys', []);
        $this->set('_flash_keys', array_diff($flashKeys, $keys));
    }

    public function reflash(): void
    {
        $flashKeys = $this->get('_flash_keys', []);
        $this->keep($flashKeys);
    }

    public function getId(): string
    {
        $this->start();
        return session_id();
    }

    public function setId(string $id): void
    {
        session_id($id);
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->delete($key);
        return $value;
    }

    public function push(string $key, mixed $value): void
    {
        $array = $this->get($key, []);
        $array[] = $value;
        $this->set($key, $array);
    }

    public function increment(string $key, int $amount = 1): int
    {
        $value = (int) $this->get($key, 0);
        $value += $amount;
        $this->set($key, $value);
        return $value;
    }

    public function decrement(string $key, int $amount = 1): int
    {
        return $this->increment($key, -$amount);
    }
}
