<?php
session_start();
require '../vendor/autoload.php';
use App\Models\User;
use App\Includes\Auth;

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::validateCSRF($_POST['csrf'] ?? '')) {
    $name = trim($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['confirm'] ?? '';

    if (!$email) $errors[] = "Valid email required";
    if (strlen($pass) < 8 || $pass !== $pass2) $errors[] = "Password too short or mismatch";

    if (empty($errors)) {
        if (User::create($name, $email, $pass)) {
            header('Location: login.php');
            exit;
        } else {
            $errors[] = "Registration failed – email taken?";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mini E-Commerce</title>
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
            <a class="nav-link" href="login.php">
                <i class="fas fa-sign-in-alt me-1"></i>Login
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h2 class="card-title">Create Account</h2>
                        <p class="text-muted">Join us today</p>
                    </div>
                    <?php foreach ($errors as $e): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($e) ?>
                        </div>
                    <?php endforeach; ?>
                    <form method="POST">
                        <input type="hidden" name="csrf" value="<?= Auth::generateCSRF() ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Full Name
                            </label>
                            <input class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input class="form-control" id="email" name="email" type="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <input class="form-control" id="password" name="password" type="password" placeholder="Create a password (8+ chars)" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label for="confirm" class="form-label">
                                <i class="fas fa-lock me-2"></i>Confirm Password
                            </label>
                            <input class="form-control" id="confirm" name="confirm" type="password" placeholder="Confirm your password" required>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account? <a href="login.php" class="text-primary">Sign in here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>