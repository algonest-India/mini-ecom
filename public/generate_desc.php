<?php
session_start();
require '../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\OpenAIClient;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
$productName = $_POST['product_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $apiKey && $productName) {
    $client = new OpenAIClient($apiKey);
    $prompt = "Generate a 150-word engaging description for '$productName'. Include benefits, features, and CTA.";
    echo $client->chatCompletion($prompt);
} elseif (!$apiKey) {
    echo "Error: API key not configured in .env";
} elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Generate Product Description</title>
    </head>
    <body>
        <h1>Generate Product Description</h1>
        <form method="POST">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>
            <button type="submit">Generate</button>
        </form>
    </body>
    </html>
    <?php
} else {
    echo "Error: Product name not provided";
}