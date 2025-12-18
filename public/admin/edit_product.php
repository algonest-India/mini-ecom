<?php
session_start();
require '../../vendor/autoload.php';
use App\Includes\Auth;
use App\Models\Product;

Auth::requireAdmin();

$id = (int)$_GET['id'] ?? 0;
$product = Product::find($id);
if (!$product) header('Location: products.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Similar to create – update with Product::update
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $image = Product::uploadImage($_FILES['image'] ?? []) ?: $product['image'];

    if (empty($name) || $price <= 0) $errors[] = "Name and price required";

    if (empty($errors)) {
        Product::update($id, $name, $desc, $price, $image, $_POST['category'] ?? '');
        header('Location: products.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script>
function generateDesc() {
    let name = document.getElementById('name').value;
    fetch('../generate_desc.php', {
        method: 'POST',
        body: 'product_name=' + encodeURIComponent(name)
    }).then(r => r.text()).then(data => {
        document.getElementById('description').value = data;
    });
}
</script>
</head>
<body>
<div class="container mt-5">
    <h1>Edit Product</h1>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>
    <form method="POST" enctype="multipart/form-data">
        <input class="form-control mb-3" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        <input class="form-control mb-3" name="price" type="number" step="0.01" value="<?= $product['price'] ?>" required>
        <textarea class="form-control mb-3" name="description" id="description" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
        <button type="button" class="btn btn-info mb-3" onclick="generateDesc()">AI Regenerate Description</button>
        <input class="form-control mb-3" name="image" type="file" accept="image/*">
        <input class="form-control mb-3" name="category" value="<?= htmlspecialchars($product['category'] ?? '') ?>">
        <button class="btn btn-success">Update Product</button>
    </form>
    <a href="products.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>