<?php
namespace App\Models;

use App\Config\Database;

class CartItem {
    public static function getByUser(int $userId): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT c.*, p.name, p.price FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function add(int $userId, int $productId, int $quantity = 1): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        return $stmt->execute([$userId, $productId, $quantity, $quantity]);
    }

    public static function update(int $userId, int $productId, int $quantity): bool {
        $pdo = Database::getConnection();
        if ($quantity <= 0) {
            return self::remove($userId, $productId);
        }
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$quantity, $userId, $productId]);
    }

    public static function remove(int $userId, int $productId): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }

    public static function clear(int $userId): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public static function getTotal(int $userId): float {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT SUM(p.price * c.quantity) as total FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$userId]);
        return (float)($stmt->fetch()['total'] ?? 0);
    }
}