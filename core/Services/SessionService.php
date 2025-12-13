<?php

namespace ZEngine\Core\Services;

use ZEngine\App\Models\Session;

class SessionService
{
    private static ?SessionService $instance = null;
    private ?string $sessionId = null;
    private array $data = [];
    private bool $loaded = false;
    private Session $sessionModel;
    private string $cookieName = 'ZENGINE_SESSION';
    private int $lifetime = 7200;
    private string $fingerprint;

    private function __construct()
    {
        $this->sessionModel = new Session();
        $this->fingerprint = $this->generateFingerprint();
        $this->load();
        $this->cleanupExpiredSessions();
    }

    public static function getInstance(): SessionService
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->sessionId = $_COOKIE[$this->cookieName] ?? null;

        if ($this->sessionId && $this->validate()) {
            $session = $this->sessionModel->find($this->sessionId);

            if ($session) {
                $this->data = $session['payload'] ?? [];
                $this->sessionModel->updateActivity($this->sessionId);
                $this->loaded = true;
                return;
            }
        }

        $this->createNewSession();
    }

    private function createNewSession(): void
    {
        $this->sessionId = $this->generateSessionId();
        $this->data = [];

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $this->sessionModel->create(
            $this->sessionId,
            null,
            $ip,
            $userAgent,
            $this->data
        );

        $this->setCookie();
        $this->loaded = true;
    }

    private function validate(): bool
    {
        if (!$this->sessionId) {
            return false;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (!$this->sessionModel->validateFingerprint($this->sessionId, $ip, $userAgent)) {
            $this->invalidateSession();
            return false;
        }

        return true;
    }

    private function invalidateSession(): void
    {
        if ($this->sessionId) {
            $this->sessionModel->delete($this->sessionId);
        }

        $this->data = [];
        $this->sessionId = null;
        $this->loaded = false;
        $this->deleteCookie();
    }

    public function set(string $key, $value): void
    {
        if (!$this->loaded) {
            return;
        }

        $this->data[$key] = $value;
        $this->save();
    }

    public function get(string $key, $default = null)
    {
        if (!$this->loaded) {
            return $default;
        }

        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        if (!$this->loaded) {
            return false;
        }

        return isset($this->data[$key]);
    }

    public function remove(string $key): void
    {
        if (!$this->loaded) {
            return;
        }

        unset($this->data[$key]);
        $this->save();
    }

    public function regenerate(?int $userId = null): void
    {
        $oldSessionId = $this->sessionId;
        $oldData = $this->data;

        if ($oldSessionId) {
            $this->sessionModel->delete($oldSessionId);
        }

        $this->sessionId = $this->generateSessionId();
        $this->data = $oldData;

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $this->sessionModel->create(
            $this->sessionId,
            $userId,
            $ip,
            $userAgent,
            $this->data
        );

        $this->setCookie();
        $this->loaded = true;
    }

    public function destroy(): void
    {
        if ($this->sessionId) {
            $this->sessionModel->delete($this->sessionId);
        }

        $this->data = [];
        $this->sessionId = null;
        $this->loaded = false;

        $this->deleteCookie();
    }

    public function associateUser(int $userId): void
    {
        $this->regenerate($userId);
        $this->set('user_id', $userId);
    }

    public function token(): string
    {
        if (!$this->has('_token')) {
            $this->set('_token', bin2hex(random_bytes(32)));
        }

        return $this->get('_token', '');
    }

    public function flash(string $key, $value): void
    {
        $this->set('_flash_' . $key, $value);
    }

    public function getFlash(string $key, $default = null)
    {
        $value = $this->get('_flash_' . $key, $default);
        $this->remove('_flash_' . $key);
        return $value;
    }

    public function hasFlash(string $key): bool
    {
        return $this->has('_flash_' . $key);
    }

    private function save(): void
    {
        if ($this->sessionId) {
            $userId = isset($this->data['user_id']) ? (int)$this->data['user_id'] : null;
            $this->sessionModel->update($this->sessionId, $this->data, null, $userId);
        }
    }

    private function generateSessionId(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function generateFingerprint(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return hash('sha256', $ip . '|' . $userAgent . '|' . env('APP_KEY', 'default-key'));
    }

    private function getFingerprintComponents(): array
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return [
            hash('sha256', $ip),
            hash('sha256', $userAgent)
        ];
    }

    private function setCookie(): void
    {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

        setcookie(
            $this->cookieName,
            $this->sessionId,
            [
                'expires' => time() + $this->lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }

    private function deleteCookie(): void
    {
        setcookie(
            $this->cookieName,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }

    private function cleanupExpiredSessions(): void
    {
        if (rand(1, 100) <= 2) {
            $this->sessionModel->cleanup($this->lifetime);
        }
    }
}
