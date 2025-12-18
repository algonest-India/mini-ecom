<?php
namespace App\Includes;

function addToCart(int $productId, int $quantity = 1): void {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $quantity;
}

function removeFromCart(int $productId): void {
    if (isset($_SESSION['cart'][$productId])) unset($_SESSION['cart'][$productId]);
}

function getCart(): array {
    return $_SESSION['cart'] ?? [];
}

function clearCart(): void {
    unset($_SESSION['cart']);
}