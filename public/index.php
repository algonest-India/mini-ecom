<?php
session_start();
require '../vendor/autoload.php';

use App\Models\Product;
use App\Models\CartItem;
use App\Includes\Auth;

$products = Product::all();
$userId = $_SESSION['user_id'] ?? null;

if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id']) && Auth::isLoggedIn()) {
    CartItem::add($userId, (int)$_GET['id']);
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini E-Commerce</title>
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
            </ul>
            <div class="navbar-nav">
                <?php if (Auth::isLoggedIn()): ?>
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart me-1"></i>Cart
                    </a>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            <?php if (Auth::isAdmin()): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/dashboard.php">Admin Panel</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="login.php">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                    <a class="nav-link" href="register.php">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="hero-section">
    <div class="container">
        <h1>Welcome to Mini E-Commerce</h1>
        <p>Discover amazing products at great prices</p>
        <a href="#products" class="btn btn-light btn-lg">
            <i class="fas fa-arrow-down me-2"></i>Shop Now
        </a>
    </div>
</div>

<div class="container" id="products">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4 text-white">Our Products</h2>
        </div>
    </div>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($product['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php else: ?>
                        <i class="fas fa-image fa-3x text-muted"></i>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                    <div class="product-price">₹<?= number_format($product['price'], 2) ?></div>
                    <?php if (Auth::isLoggedIn()): ?>
                        <a href="?action=add&id=<?= $product['id'] ?>" class="btn btn-primary w-100">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Buy
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Mini E-Commerce. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
