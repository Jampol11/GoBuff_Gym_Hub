<?php
/**
 * RoleMiddleware - Ensures user has required role(s)
 */
class RoleMiddleware
{
    public static function handle(string|array $roles): void
    {
        Auth::requireRole($roles);
    }
}
