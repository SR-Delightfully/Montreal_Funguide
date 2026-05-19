<?php

namespace App\Helpers;

class SessionManager
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {

            $sessionLifetime = 24 * 60 * 60;

            session_set_cookie_params([
                'lifetime' => $sessionLifetime,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            ini_set('session.gc_maxlifetime', $sessionLifetime);
            ini_set('session.gc_probability', 1);
            ini_set('session.gc_divisor', 100);

            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_httponly', 1);

            session_start();

            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
                session_regenerate_id(true);
            } elseif (time() - $_SESSION['last_regeneration'] > 900) {
                $_SESSION['last_regeneration'] = time();
                session_regenerate_id(true);
            }

            $fingerprint = self::generateFingerprint();
            if (!isset($_SESSION['fingerprint'])) {
                $_SESSION['fingerprint'] = $fingerprint;
            } elseif ($_SESSION['fingerprint'] !== $fingerprint) {
                session_destroy();
                session_start();
                $_SESSION['fingerprint'] = $fingerprint;
                $_SESSION['last_regeneration'] = time();
            }
        }
    }

    private static function generateFingerprint(): string
    {
        $factors = [
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            $_SERVER['REMOTE_ADDR'] ?? ''
        ];

        return hash('sha256', implode('|', $factors));
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function clear(): void
    {
        session_unset();
    }

    public static function destroy(): void
    {
        session_destroy();
    }
}
