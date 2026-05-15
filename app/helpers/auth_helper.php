<?php
/**
 * Auth Helpers
 */

function auth_user(): array|null
{
    return Auth::user();
}

function auth_id(): int|null
{
    return Auth::id();
}

function auth_role(): string|null
{
    return Auth::role();
}

function is_logged_in(): bool
{
    return Auth::check();
}

function has_role(string|array $roles): bool
{
    return Auth::hasRole($roles);
}

function csrf_token(): string
{
    $session = new Session();
    return $session->generateCsrfToken();
}

function csrf_field(): string
{
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    $session = new Session();
    $token   = $_POST[CSRF_TOKEN_NAME] ?? '';
    return $session->validateCsrfToken($token);
}

function role_label(string $role): string
{
    $labels = [
        'gym_owner'   => 'Gym Owner',
        'admin'       => 'Administrative Officer',
        'marketing'   => 'Marketing Officer',
        'trainer'     => 'Fitness Trainer',
        'maintenance' => 'Maintenance Supervisor',
        'member'      => 'Member',
        'user'        => 'User',
    ];
    return $labels[$role] ?? ucfirst($role);
}

function role_badge(string $role): string
{
    $colors = [
        'gym_owner'   => 'danger',
        'admin'       => 'primary',
        'marketing'   => 'info',
        'trainer'     => 'success',
        'maintenance' => 'warning',
        'member'      => 'secondary',
        'user'        => 'dark',
    ];
    $color = $colors[$role] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . role_label($role) . '</span>';
}
