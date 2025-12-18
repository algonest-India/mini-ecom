<?php
namespace App\Includes;

session_start();

class Auth {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool {
        return self::isLoggedIn() && ($_SESSION['is_admin'] ?? false);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public static function requireAdmin(): void {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: index.php');
            exit;
        }
    }

    public static function generateCSRF(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRF(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function loginUser(string $email, string $password): bool {
        return \App\Models\User::verifyLogin($email, $password);
    }

    public static function logoutUser(): void {
        session_destroy();
    }
}