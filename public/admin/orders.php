<?php
session_start();
require '../../vendor/autoload.php';
use App\Includes\Auth;
use App\Models\Order;

Auth::requireAdmin();

$orders = Order::all();
?>

<!DOCTYPE html>
<html>
<head><title>Orders</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Manage Orders</h1>
    <table class="table">
        <thead><tr><th>ID</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td>$<?= number_format($o['total'], 2) ?></td>
                    <td><?= htmlspecialchars($o['status']) ?></td>
                    <td><?= $o['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>