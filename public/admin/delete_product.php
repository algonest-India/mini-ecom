<?php
session_start();
require '../../vendor/autoload.php';
use App\Includes\Auth;
use App\Models\Product;

Auth::requireAdmin();

$id = (int)$_GET['id'] ?? 0;
if ($id) {
    Product::delete($id);
}
header('Location: products.php');
exit;
?>