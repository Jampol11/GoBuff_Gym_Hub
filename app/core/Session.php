<?php
/**
 * Session Manager
 */
class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => false, // set true in production with HTTPS
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /* Flash messages */
    public function setFlash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    public function getFlash(string $type): ?string
    {
        $msg = $_SESSION['_flash'][$type] ?? null;
        unset($_SESSION['_flash'][$type]);
        return $msg;
    }

    public function hasFlash(string $type): bool
    {
        return isset($_SESSION['_flash'][$type]);
    }

    /* CSRF */
    public function generateCsrfToken(): string
    {
        if (!$this->has(CSRF_TOKEN_NAME)) {
            $this->set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
        }
        return $this->get(CSRF_TOKEN_NAME);
    }

    public function validateCsrfToken(string $token): bool
    {
        $stored = $this->get(CSRF_TOKEN_NAME);
        return $stored && hash_equals($stored, $token);
    }

    public function refreshCsrfToken(): string
    {
        $this->remove(CSRF_TOKEN_NAME);
        return $this->generateCsrfToken();
    }
}
