<?php
/**
 * AuthMiddleware - Ensures user is authenticated
 */
class AuthMiddleware
{
    public static function handle(): void
    {
        Auth::requireAuth();
    }
}
