# Mini E-Commerce

A modern, responsive PHP e-commerce application with AI-generated product descriptions and beautiful Bootstrap styling. Now featuring Indian Rupee (₹) currency support and enhanced admin dashboard.

## Features

- **User Authentication**: Secure login and registration with CSRF protection
- **Product Management**: Full CRUD operations for products with image upload
- **Shopping Cart**: Persistent cart functionality for logged-in users
- **AI Integration**: OpenAI-powered product description generation
- **Admin Panel**: Complete admin interface for managing products and orders
- **Enhanced Dashboard**: Statistics overview with product, order, and user counts
- **Indian Rupee Support**: All prices displayed in ₹ (Indian Rupee) currency
- **Responsive Design**: Modern, mobile-friendly UI with Bootstrap 5
- **Security**: CSRF protection, input validation, and secure password hashing

## Screenshots

### Homepage
Beautiful hero section with gradient background and product grid display.

### Admin Dashboard
Comprehensive admin interface with statistics cards showing product counts, order totals, user registrations, and revenue. Includes recent orders table and quick action buttons.

### Product Management
Intuitive product creation with AI description generation.

## Setup

1. Clone the repository.
2. Run `composer install` to install dependencies.
3. Copy `.env.example` to `.env` and fill in your database and OpenAI API key.
4. Create the database using `schema.sql`.
5. Start a PHP server: `php -S localhost:8000 -t public`
6. Access the site at `http://localhost:8000`

## Tech Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Font Awesome icons, Custom CSS
- **AI**: OpenAI API integration
- **Currency**: Indian Rupee (₹) support
- **Security**: CSRF tokens, password hashing

## Admin Access

Login with admin@example.com to access the admin panel.

## Styling Features

- Gradient backgrounds and modern color schemes
- Hover effects and smooth transitions
- Responsive grid layouts
- Card-based UI components
- Custom CSS animations
- Mobile-optimized design
- Professional typography and spacing

