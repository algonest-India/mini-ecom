<?php
session_start();
require '../vendor/autoload.php';

use App\Models\CartItem;
use App\Includes\Auth;

Auth::requireLogin();  # Or allow guest
$userId = $_SESSION['user_id'];

$cartItems = CartItem::getByUser($userId);
if (empty($cartItems)) header('Location: cart.php');

$total = CartItem::getTotal($userId);

if ($_POST) {
    // Create order
    $orderId = \App\Models\Order::create($userId, $total);
    // Simulate Phonepe initiation
    $phonepeUrl = 'https://sandbox.phonepe.com/pg/v1/pay';  # Mock
    // In real: Redirect to Phonepe with payload

    // Simulate success callback
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Mini E-Commerce</title>
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
            <a class="nav-link" href="cart.php">
                <i class="fas fa-arrow-left me-1"></i>Back to Cart
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-credit-card fa-3x text-success mb-3"></i>
                        <h1 class="card-title">Secure Checkout</h1>
                        <p class="text-muted">Complete your purchase with PhonePe</p>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <h4>Order Summary</h4>
                            <div class="border rounded p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Total Amount:</span>
                                    <strong class="text-primary fs-4">₹<?= number_format($total, 2) ?></strong>
                                </div>
                            </div>

                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= uniqid('ORD_') ?>">
                                <div class="d-grid">
                                    <button class="btn btn-success btn-lg" type="submit">
                                        <i class="fab fa-paypal me-2"></i>Pay with PhonePe
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded p-3">
                                <h5 class="text-success">
                                    <i class="fas fa-shield-alt me-2"></i>Secure Payment
                                </h5>
                                <p class="small text-muted mb-0">
                                    Your payment is protected by PhonePe's advanced security measures.
                                    We never store your payment information.
                                </p>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    This is a simulated checkout. In production, you would be redirected to PhonePe's secure payment gateway.
                                </small>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success mt-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Payment Successful!</strong>
                            <p class="mb-2">Your order has been placed successfully.</p>
                            <p class="mb-0">Order ID: <strong><?= $orderId ?? 'N/A' ?></strong></p>
                            <div class="mt-3">
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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