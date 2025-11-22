<?php

namespace ZEngine\Core\Services;

class CookieService
{
    private int $defaultExpire;
    private string $defaultPath;
    private string $defaultDomain;
    private bool $defaultSecure;
    private bool $defaultHttpOnly;
    private string $defaultSameSite;

    public function __construct(array $config = [])
    {
        $this->defaultExpire = $config['expire'] ?? 3600;
        $this->defaultPath = $config['path'] ?? '/';
        $this->defaultDomain = $config['domain'] ?? '';
        $this->defaultSecure = $config['secure'] ?? false;
        $this->defaultHttpOnly = $config['httponly'] ?? true;
        $this->defaultSameSite = $config['samesite'] ?? 'Lax';
    }

    public function set(
        string $name,
        string $value,
        ?int $expire = null,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $httpOnly = null,
        ?string $sameSite = null
    ): bool {
        $expire = $expire ?? time() + $this->defaultExpire;
        $path = $path ?? $this->defaultPath;
        $domain = $domain ?? $this->defaultDomain;
        $secure = $secure ?? $this->defaultSecure;
        $httpOnly = $httpOnly ?? $this->defaultHttpOnly;
        $sameSite = $sameSite ?? $this->defaultSameSite;

        $options = [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ];

        return setcookie($name, $value, $options);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $_COOKIE[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public function delete(string $name, ?string $path = null, ?string $domain = null): bool
    {
        $path = $path ?? $this->defaultPath;
        $domain = $domain ?? $this->defaultDomain;

        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        return setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => $path,
            'domain' => $domain,
        ]);
    }

    public function forever(string $name, string $value): bool
    {
        return $this->set($name, $value, time() + (365 * 24 * 60 * 60));
    }

    public function all(): array
    {
        return $_COOKIE;
    }

    public function queue(string $name, string $value, ?int $expire = null): void
    {
        $this->set($name, $value, $expire);
    }

    public function encrypt(string $value, string $key): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($value, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $value, string $key): string
    {
        $data = base64_decode($value);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
}
