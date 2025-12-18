<?php

namespace ZEngine\Core\Services;

class SessionService
{
    private static ?SessionService $instance = null;
    private ?string $sessionId = null;
    private array $data = [];
    private bool $loaded = false;
    private string $cookieName = 'ZENGINE_SESSION';
    private int $lifetime = 7200;
    private string $fingerprint;
    private string $storagePath;

    private function __construct()
    {
        $this->storagePath = rtrim(env('SESSION_STORAGE_PATH') ?? __DIR__ . '/../../storage/sessions', '/');
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
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
            $session = $this->readSession($this->sessionId);

            if ($session && !$this->isExpired($session)) {
                $this->data = $session['payload'] ?? [];
                $this->updateSessionActivity($this->sessionId);
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

        $ip = $this->getTrustedIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $this->writeSession($this->sessionId, [
            'id' => $this->sessionId,
            'user_id' => null,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'payload' => $this->data,
            'last_activity' => time(),
            'created_at' => time()
        ]);

        $this->setCookie();
        $this->loaded = true;
    }

    private function validate(): bool
    {
        if (!$this->sessionId) {
            return false;
        }

        $session = $this->readSession($this->sessionId);
        if (!$session) {
            return false;
        }

        $ip = $this->getTrustedIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if ($session['ip_address'] !== $ip || $session['user_agent'] !== $userAgent) {
            $this->invalidateSession();
            return false;
        }

        return true;
    }

    private function invalidateSession(): void
    {
        if ($this->sessionId) {
            $this->deleteSession($this->sessionId);
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
            $this->deleteSession($oldSessionId);
        }

        $this->sessionId = $this->generateSessionId();
        $this->data = $oldData;

        $ip = $this->getTrustedIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $this->writeSession($this->sessionId, [
            'id' => $this->sessionId,
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'payload' => $this->data,
            'last_activity' => time(),
            'created_at' => time()
        ]);

        $this->setCookie();
        $this->loaded = true;
    }

    public function destroy(): void
    {
        if ($this->sessionId) {
            $this->deleteSession($this->sessionId);
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
            $session = $this->readSession($this->sessionId);
            if ($session) {
                $session['payload'] = $this->data;
                $session['last_activity'] = time();
                if (isset($this->data['user_id'])) {
                    $session['user_id'] = (int)$this->data['user_id'];
                }
                $this->writeSession($this->sessionId, $session);
            }
        }
    }

    private function generateSessionId(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function generateFingerprint(): string
    {
        $ip = $this->getTrustedIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return hash('sha256', $ip . '|' . $userAgent . '|' . env('APP_KEY', 'default-key'));
    }

    private function getFingerprintComponents(): array
    {
        $ip = $this->getTrustedIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return [
            hash('sha256', $ip),
            hash('sha256', $userAgent)
        ];
    }

    private function getCookie(): ?string
    {
        return $_COOKIE[$this->cookieName] ?? null;
    }

    private function setCookie(): void
    {
        setcookie(
            $this->cookieName,
            $this->sessionId,
            [
                'expires' => time() + $this->lifetime,
                'path' => env('SESSION_PATH', '/'),
                'domain' => env('SESSION_DOMAIN', ''),
                'secure' => env('SESSION_SECURE', false),
                'httponly' => env('SESSION_HTTPONLY', true),
                'samesite' => env('SESSION_SAMESITE', 'Strict')
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
                'path' => env('SESSION_PATH', '/'),
                'domain' => env('SESSION_DOMAIN', ''),
                'secure' => env('SESSION_SECURE', false),
                'httponly' => env('SESSION_HTTPONLY', true),
                'samesite' => env('SESSION_SAMESITE', 'Strict')
            ]
        );
    }

    private function cleanupExpiredSessions(): void
    {
        if (rand(1, 100) <= 2) {
            $files = glob($this->storagePath . '/sess_*');
            if ($files === false) {
                return;
            }

            $now = time();
            foreach ($files as $file) {
                $content = @file_get_contents($file);
                if ($content === false) {
                    continue;
                }

                $session = json_decode($content, true);
                if ($session && json_last_error() === JSON_ERROR_NONE && ($now - $session['last_activity']) > $this->lifetime) {
                    @unlink($file);
                }
            }
        }
    }

    private function readSession(string $sessionId): ?array
    {
        if (!$this->isValidSessionId($sessionId)) {
            return null;
        }

        $file = $this->storagePath . '/sess_' . $sessionId;
        if (!file_exists($file)) {
            return null;
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            return null;
        }

        $session = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $session;
    }

    private function writeSession(string $sessionId, array $data): void
    {
        if (!$this->isValidSessionId($sessionId)) {
            throw new \InvalidArgumentException("Invalid session ID format");
        }

        $file = $this->storagePath . '/sess_' . $sessionId;
        $json = json_encode($data);

        if ($json === false) {
            throw new \RuntimeException("Failed to encode session data: " . json_last_error_msg());
        }

        if (@file_put_contents($file, $json, LOCK_EX) === false) {
            throw new \RuntimeException("Failed to write session file: {$file}");
        }
    }

    private function deleteSession(string $sessionId): void
    {
        if (!$this->isValidSessionId($sessionId)) {
            return;
        }

        $file = $this->storagePath . '/sess_' . $sessionId;
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    private function updateSessionActivity(string $sessionId): void
    {
        $session = $this->readSession($sessionId);
        if ($session) {
            $session['last_activity'] = time();
            $this->writeSession($sessionId, $session);
        }
    }

    private function isExpired(array $session): bool
    {
        return (time() - $session['last_activity']) > $this->lifetime;
    }

    private function isValidSessionId(string $sessionId): bool
    {
        return preg_match('/^[a-f0-9]{64}$/', $sessionId) === 1;
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

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
