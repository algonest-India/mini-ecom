<?php
namespace App\Models;

use App\Config\Database;
use RuntimeException;

class User {
    public static function create(string $name, string $email, string $password): bool {
        $pdo = Database::getConnection();
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$name, $email, $hashed]);
    }

    public static function findByEmail(string $email): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }
     public static function verifyLogin(string $email, string $password): bool {
        $user = self::findByEmail($email);
        if (!$user) {
            return false;
        }
        return password_verify($password, $user['password']);
    }
}