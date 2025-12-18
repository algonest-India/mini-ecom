<?php
session_start();
require '../../vendor/autoload.php';
use App\Includes\Auth;
use App\Models\Product;
use App\Services\OpenAIClient;

Auth::requireAdmin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $image = Product::uploadImage($_FILES['image'] ?? []);

    if (empty($name) || $price <= 0) $errors[] = "Name and price required";

    if (empty($errors)) {
        Product::create($name, $desc, $price, $image);
        header('Location: products.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i>Admin Panel
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="products.php">
                <i class="fas fa-arrow-left me-1"></i>Back to Products
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
                        <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                        <h1 class="card-title">Add New Product</h1>
                        <p class="text-muted">Create a new product for your store</p>
                    </div>

                    <?php foreach ($errors as $e): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($e) ?>
                        </div>
                    <?php endforeach; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-2"></i>Product Name
                            </label>
                            <input class="form-control" name="name" id="name" placeholder="Enter product name" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">
                                <i class="fas fa-dollar-sign me-2"></i>Price
                            </label>
                            <input class="form-control" name="price" type="number" step="0.01" placeholder="0.00" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Description
                            </label>
                            <textarea class="form-control" name="description" id="description" rows="5" placeholder="Enter product description"></textarea>
                            <div class="form-text">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="generateDesc()">
                                    <i class="fas fa-robot me-1"></i>AI Generate Description
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image me-2"></i>Product Image
                            </label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text">Upload a product image (JPG, PNG, max 2MB)</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fas fa-save me-2"></i>Save Product
                            </button>
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
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
<script>
function generateDesc() {
    let name = document.getElementById('name').value;
    if (!name) {
        alert("Please enter a product name first!");
        return;
    }

    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating...';
    button.disabled = true;

    fetch('../generate_desc.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_name=' + encodeURIComponent(name)
    })
    .then(r => r.text())
    .then(data => {
        document.getElementById('description').value = data;
        button.innerHTML = originalText;
        button.disabled = false;
    })
    .catch(error => {
        alert('Error generating description. Please try again.');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
</body>
</html>