<?php
session_start();
require '../../vendor/autoload.php';
use App\Includes\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

Auth::requireAdmin();

// Get statistics
$productCount = count(Product::all());
$orderCount = count(Order::all());
$userCount = count(User::all()); // We'll need to add this method

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mini E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-shopping-bag me-2"></i>Mini E-Com
        </a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">
                <i class="fas fa-user-shield me-1"></i>Admin Panel
            </span>
            <a class="nav-link" href="../logout.php">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>Admin Dashboard
            </h1>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-box fa-3x text-primary mb-3"></i>
                    <h2 class="card-title text-primary fw-bold"><?= $productCount ?></h2>
                    <h5 class="card-title">Products</h5>
                    <p class="card-text">Manage your product catalog</p>
                    <a href="products.php" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-2"></i>Manage Products
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                    <h2 class="card-title text-success fw-bold"><?= $orderCount ?></h2>
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text">View and manage orders</p>
                    <a href="orders.php" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>View Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-info mb-3"></i>
                    <h2 class="card-title text-info fw-bold"><?= $userCount ?></h2>
                    <h5 class="card-title">Users</h5>
                    <p class="card-text">Registered customers</p>
                    <button class="btn btn-info" disabled>
                        <i class="fas fa-arrow-right me-2"></i>Coming Soon
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                    <h2 class="card-title text-warning fw-bold">₹0</h2>
                    <h5 class="card-title">Revenue</h5>
                    <p class="card-text">Total sales revenue</p>
                    <button class="btn btn-warning" disabled>
                        <i class="fas fa-arrow-right me-2"></i>Coming Soon
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-clock me-2"></i>Recent Orders
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>Order ID</th>
                                    <th><i class="fas fa-user me-1"></i>Customer</th>
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-rupee-sign me-1"></i>Total</th>
                                    <th><i class="fas fa-cogs me-1"></i>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recentOrders = array_slice(Order::all(), 0, 5);
                                foreach ($recentOrders as $order):
                                ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td>Customer #<?= $order['user_id'] ?></td>
                                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                        <td>₹<?= number_format($order['total'], 2) ?></td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="orders.php" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>View All Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Quick Stats
                    </h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Products:</span>
                            <strong class="text-primary"><?= $productCount ?></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Orders:</span>
                            <strong class="text-success"><?= $orderCount ?></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Registered Users:</span>
                            <strong class="text-info"><?= $userCount ?></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Avg. Order Value:</span>
                            <strong class="text-warning">₹<?= $orderCount > 0 ? number_format(array_sum(array_column(Order::all(), 'total')) / $orderCount, 2) : '0.00' ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Mini E-Commerce Admin Panel. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
