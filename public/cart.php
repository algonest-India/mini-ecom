<?php
session_start();
require '../vendor/autoload.php';

use App\Models\CartItem;
use App\Models\Product;
use App\Includes\Auth;

if (Auth::isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $cartItems = CartItem::getByUser($userId);
} else {
    $cartItems = [];  // Fallback to session (implement if needed)
}

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'add' && isset($_GET['id']) && Auth::isLoggedIn()) {
        CartItem::add($userId, (int)$_GET['id']);
    } elseif ($_GET['action'] === 'remove' && isset($_GET['id']) && Auth::isLoggedIn()) {
        CartItem::remove($userId, (int)$_GET['id']);
    } elseif ($_GET['action'] === 'update' && isset($_GET['id'], $_GET['qty']) && Auth::isLoggedIn()) {
        CartItem::update($userId, (int)$_GET['id'], (int)$_GET['qty']);
    }
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Mini E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-shopping-bag me-2"></i>Mini E-Com
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">
                <i class="fas fa-home me-1"></i>Home
            </a>
            <?php if (Auth::isLoggedIn()): ?>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>Your Shopping Cart
            </h1>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3 class="text-muted">Your cart is empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="index.php" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <?php $grandTotal = 0; foreach ($cartItems as $item): $sub = $item['price'] * $item['quantity']; $grandTotal += $sub; ?>
                    <div class="cart-item">
                        <img src="uploads/<?= htmlspecialchars($item['image'] ?? '') ?>" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                        <div class="cart-item-details">
                            <h5 class="cart-item-title"><?= htmlspecialchars($item['name']) ?></h5>
                            <p class="cart-item-price">₹<?= number_format($item['price'], 2) ?> each</p>
                            <div class="d-flex align-items-center">
                                <label class="me-2">Quantity:</label>
                                <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" min="1" onchange="updateQty(<?= $item['product_id'] ?>, this.value)">
                                <a href="?action=remove&id=<?= $item['product_id'] ?>" class="btn btn-outline-danger btn-sm ms-3">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <div class="text-end">
                            <strong>₹<?= number_format($sub, 2) ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>₹<?= number_format($grandTotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong>₹<?= number_format($grandTotal, 2) ?></strong>
                        </div>
                        <div class="d-grid mt-4">
                            <a href="checkout.php" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Mini E-Commerce. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateQty(id, qty) {
    fetch(`?action=update&id=${id}&qty=${qty}`)
        .then(() => location.reload());
}
</script>
</body>
</html>