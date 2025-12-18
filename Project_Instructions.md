# Project Instructions: Build a Mini E-Commerce Website with AI Product Description Generator

## Project Metadata

- **Project Title**: Mini E-Commerce with AI Product Descriptions
- **Level**: Intermediate PHP Project
- **Duration**: 7–10 days
- **Objective**: Build a full-stack e-commerce site from scratch, integrating user authentication, product management, shopping cart, admin panel, and AI-generated descriptions using OpenAI API.

## Project Overview

This project will create a functional mini e-commerce website with the following key features:

- User registration and login with secure password hashing
- Product browsing with search and category filtering
- Shopping cart for logged-in users
- Checkout process with order creation
- Admin panel for managing products and orders
- Image upload for products
- AI-powered product description generation using OpenAI
- Responsive design with Bootstrap
- CSRF protection and input validation

## Final Project Structure

```
mini-ecom/
├── composer.json
├── README.md
├── schema.sql
├── .env
├── .env.example
├── public/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── cart.php
│   ├── checkout.php
│   ├── generate_desc.php
│   ├── css/
│   │   └── style.css
│   ├── uploads/
│   └── admin/
│       ├── dashboard.php
│       ├── products.php
│       ├── create_product.php
│       ├── edit_product.php
│       ├── delete_product.php
│       └── orders.php
├── src/
│   ├── config/
│   │   └── database.php
│   ├── includes/
│   │   ├── auth.php
│   │   └── cart.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── CartItem.php
│   └── services/
│       └── OpenAIClient.php
└── vendor/
    └── (Composer dependencies)
```

## Step-by-Step Instructions

### Step 1: Set Up Development Environment

1. Install XAMPP (or similar) for PHP and MySQL.
2. Install Composer from getcomposer.org.
3. Create a new folder named `mini-ecom` for your project.
4. Open a terminal in the project folder and run `composer init` to create [`composer.json`](composer.json ).
5. Install required packages: `composer require vlucas/phpdotenv guzzlehttp/guzzle`.
6. Ensure PHP 8.0+ is available by running `php --version`.

### Step 2: Configure Environment Variables

1. Create [`.env.example`](.env.example ) with placeholder values:
   ```
   DB_HOST=localhost
   DB_NAME=mini_ecom
   DB_USER=root
   DB_PASS=
   OPENAI_API_KEY=your_openai_api_key_here
   ```
2. Copy [`.env.example`](.env.example ) to [`.env`](.env ) and fill in your actual MySQL credentials and OpenAI API key (get from platform.openai.com).

### Step 3: Set Up Database

1. Start MySQL via XAMPP control panel.
2. Create a database named `mini_ecom`.
3. Create [`schema.sql`](schema.sql ) with the following content:

   ```sql
   CREATE DATABASE mini_ecom;
   USE mini_ecom;

   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       is_admin TINYINT DEFAULT 0,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE products (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       description TEXT,
       price DECIMAL(10,2) NOT NULL,
       image VARCHAR(255),
       category VARCHAR(100),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE orders (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       total DECIMAL(10,2) NOT NULL,
       status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id)
   );

   CREATE TABLE order_items (
       id INT AUTO_INCREMENT PRIMARY KEY,
       order_id INT NOT NULL,
       product_id INT NOT NULL,
       quantity INT NOT NULL,
       price DECIMAL(10,2) NOT NULL,
       FOREIGN KEY (order_id) REFERENCES orders(id),
       FOREIGN KEY (product_id) REFERENCES products(id)
   );

   CREATE TABLE cart_items (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       product_id INT NOT NULL,
       quantity INT NOT NULL,
       FOREIGN KEY (user_id) REFERENCES users(id),
       FOREIGN KEY (product_id) REFERENCES products(id)
   );

   -- Insert sample admin user
   INSERT INTO users (name, email, password, is_admin) VALUES ('Admin', 'admin@example.com', '$2y$10$examplehashedpassword', 1);
   ```

4. Run the SQL script in phpMyAdmin or MySQL command line.

### Step 4: Create Folder Structure

1. Create all folders and subfolders as shown in the project structure.
2. Ensure [`public/uploads`](public/uploads ) is writable (chmod 755 or 777 on Linux/Mac).

### Step 5: Implement Database Connection

Create [`src/config/database.php`](src/config/database.php ):

```php
<?php
namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Database {
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $db = $_ENV['DB_NAME'] ?? 'mini_ecom';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
```

### Step 6: Build Authentication System

1. Create [`src/includes/auth.php`](src/includes/auth.php ):

   ```php
   <?php
   namespace App\Includes;

   session_start();

   class Auth {
       public static function isLoggedIn(): bool {
           return isset($_SESSION['user_id']);
       }

       public static function isAdmin(): bool {
           return self::isLoggedIn() && ($_SESSION['is_admin'] ?? false);
       }

       public static function requireLogin(): void {
           if (!self::isLoggedIn()) {
               header('Location: login.php');
               exit;
           }
       }

       public static function requireAdmin(): void {
           self::requireLogin();
           if (!self::isAdmin()) {
               header('Location: index.php');
               exit;
           }
       }

       public static function generateCSRF(): string {
           if (!isset($_SESSION['csrf_token'])) {
               $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
           }
           return $_SESSION['csrf_token'];
       }

       public static function validateCSRF(string $token): bool {
           return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
       }

       public static function loginUser(string $email, string $password): bool {
           return \App\Models\User::verifyLogin($email, $password);
       }

       public static function logoutUser(): void {
           session_destroy();
       }
   }
   ```

2. Create [`public/register.php`](public/register.php ) (basic form with validation).
3. Create [`public/login.php`](public/login.php ) (form that calls Auth::loginUser).

### Step 7: Implement Models

1. [`src/models/User.php`](src/models/User.php ):

   ```php
   <?php
   namespace App\Models;

   use App\Config\Database;

   class User {
       public static function create(string $name, string $email, string $password): bool {
           $pdo = Database::getConnection();
           $hashed = password_hash($password, PASSWORD_DEFAULT);
           $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
           return $stmt->execute([$name, $email, $hashed]);
       }

       public static function findByEmail(string $email): ?array {
           $pdo = Database::getConnection();
           $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
           $stmt->execute([$email]);
           return $stmt->fetch() ?: null;
       }

       public static function verifyLogin(string $email, string $password): bool {
           $user = self::findByEmail($email);
           return $user && password_verify($password, $user['password']);
       }
   }
   ```

2. [`src/models/Product.php`](src/models/Product.php ) (with CRUD, image upload).
3. [`src/models/Order.php`](src/models/Order.php ) and [`src/models/CartItem.php`](src/models/CartItem.php ) (similarly).

### Step 8: Implement Cart System

Create [`src/includes/cart.php`](src/includes/cart.php ) with functions to add/remove items, get cart total.

### Step 9: Integrate OpenAI

Create [`src/services/OpenAIClient.php`](src/services/OpenAIClient.php ):

```php
<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OpenAIClient {
    private Client $client;
    private string $apiKey;

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
        $this->client = new Client(['base_uri' => 'https://api.openai.com/v1/', 'verify' => false]);
    }

    public function chatCompletion(string $prompt): string {
        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => 250,
                    'temperature' => 0.7
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? 'No response';
        } catch (RequestException $e) {
            return 'AI service error: ' . $e->getMessage();
        }
    }
}
```

### Step 10: Build Frontend Pages

1. [`public/index.php`](public/index.php ): Product grid with Bootstrap.
2. [`public/cart.php`](public/cart.php ): Display cart items.
3. [`public/checkout.php`](public/checkout.php ): Process order.
4. [`public/generate_desc.php`](public/generate_desc.php ): AJAX endpoint for AI descriptions.
5. Admin pages in [`public/admin`](public/admin ).

### Step 11: Add Styling

Create [`public/css/style.css`](public/css/style.css ) with custom styles for responsiveness.

### Step 12: Handle Image Uploads

In product creation, use `move_uploaded_file` to save images to [`public/uploads`](public/uploads ).

### Step 13: Test the Application

1. Run `php -S localhost:8000 -t public`.
2. Access `http://localhost:8000`.
3. Test registration, login, adding products, cart, checkout, admin panel, AI generation.

## Submission Requirements

Submit a ZIP file containing:
- All source code
- [`composer.json`](composer.json ) and [`composer.lock`](composer.lock )
- [`.env.example`](.env.example ) (not [`.env`](.env ))
- [`schema.sql`](schema.sql )
- A brief report on challenges faced and solutions
- Screenshots of the working site

## Learning Outcomes

By completing this project, you will learn:
- PHP OOP with namespaces and Composer autoloading
- Secure database interactions with PDO
- Session management and authentication
- API integration with Guzzle
- File upload handling
- Responsive web design
- Error handling and debugging
- Full-stack development workflow

Good luck! This project will significantly boost your PHP skills. If you encounter issues, refer to PHP documentation or ask for help.
