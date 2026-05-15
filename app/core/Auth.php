<?php
/**
 * Auth - Authentication helper
 */
class Auth
{
    private static Session $session;

    public static function init(Session $session): void
    {
        self::$session = $session;
    }

    public static function check(): bool
    {
        return self::$session->has('user');
    }

    public static function user(): array|null
    {
        return self::$session->get('user');
    }

    public static function id(): int|null
    {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): string|null
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array(self::role(), $roles);
    }

    public static function login(array $user): void
    {
        self::$session->regenerate();
        self::$session->set('user', $user);
        self::$session->set('login_time', time());
    }

    public static function logout(): void
    {
        self::$session->destroy();
    }

    /**
     * Refresh the session user data from the database.
     * Call this after a role change to avoid requiring re-login.
     */
    public static function refreshUser(int $userId): void
    {
        $userModel = new User();
        $fresh     = $userModel->findById($userId);
        if ($fresh) {
            self::$session->set('user', $fresh);
        }
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    /**
     * Redirect if not authenticated
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: ' . base_url('/login'));
            exit;
        }
        // Session timeout check
        $loginTime = self::$session->get('login_time', 0);
        if ((time() - $loginTime) > SESSION_LIFETIME) {
            self::logout();
            header('Location: ' . base_url('/login?timeout=1'));
            exit;
        }
        // Refresh login time
        self::$session->set('login_time', time());
    }

    /**
     * Require specific role(s)
     */
    public static function requireRole(string|array $roles): void
    {
        self::requireAuth();
        if (!self::hasRole($roles)) {
            http_response_code(403);
            require VIEWS_PATH . '/errors/403.php';
            exit;
        }
    }
}
