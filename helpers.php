<?php
/**
 * Checks very basic password strength
 *  - ≥8 chars
 *  - at least one upper, lower, digit, and special char
 */
function password_meets_policy(string $p): bool
{
    return strlen($p) >= 8
        && preg_match('/[A-Z]/', $p)
        && preg_match('/[a-z]/', $p)
        && preg_match('/\d/'  , $p)
        && preg_match('/[^A-Za-z0-9]/', $p);
}