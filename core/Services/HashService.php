<?php

namespace ZEngine\Core\Services;

class HashService
{
    private string $algo;
    private array $options;

    public function __construct(array $config = [])
    {
        $this->algo = $config['algo'] ?? PASSWORD_BCRYPT;
        $this->options = $config['options'] ?? ['cost' => 10];
    }

    public function make(string $value): string
    {
        return password_hash($value, $this->algo, $this->options);
    }

    public function check(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, $this->algo, $this->options);
    }

    public function md5(string $value): string
    {
        return md5($value);
    }

    public function sha1(string $value): string
    {
        return sha1($value);
    }

    public function sha256(string $value): string
    {
        return hash('sha256', $value);
    }

    public function sha512(string $value): string
    {
        return hash('sha512', $value);
    }

    public function hmac(string $algo, string $data, string $key): string
    {
        return hash_hmac($algo, $data, $key);
    }
}
