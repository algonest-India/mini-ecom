<?php
namespace App\Models;

use App\Config\Database;
use App\Services\OpenAIClient;

class Product {
    public static function all(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(string $name, string $description, float $price, string $image = null, string $category = 'General'): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $description, $price, $image, $category]);
    }

    public static function update(int $id, string $name, string $description, float $price, string $image = null, string $category = 'General'): bool {
        $pdo = Database::getConnection();
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?";
        $params = [$name, $description, $price, $category];
        if ($image) {
            $sql .= ", image = ?";
            $params[] = $image;
        }
        $sql .= " WHERE id = ?";
        $params[] = $id;
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function generateDescription(string $name, string $apiKey): string {
        $client = new OpenAIClient($apiKey);
        $prompt = "Generate a compelling 150-word e-commerce description for '$name'. Include benefits, features, SEO keywords, and a call-to-action.";
        return $client->chatCompletion($prompt, 'gpt-4o-mini', 250);
    }

    public static function uploadImage(array $file): ?string {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed) || $file['size'] > 2*1024*1024) return null;

        $name = uniqid('img_') . '.' . $ext;
        $target = __DIR__ . '/../../public/uploads/' . $name;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $name;
        }
        return null;
    }
}