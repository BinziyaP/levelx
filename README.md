# E-Commerce System

This is a Laravel-based E-Commerce application with Admin, Seller, and Buyer dashboards.

## Features
- **Admin Dashboard**: View platform statistics and manage users.
- **Seller Dashboard**: Manage products (Add, Edit, View), track orders.
- **Buyer Shop**: Browse products, search, filter, add to cart, and checkout.
- **Shopping Cart**: Session-based cart with checkout functionality.
- **Order System**: Track user orders with detailed status updates.
- **Payment Gateway**: Integrated **Razorpay** for secure payments.
- **Wishlist**: Save favorite products for later.

## Tech Stack
- **Framework**: Laravel 9
- **Language**: PHP 8
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS
- **Payment**: Razorpay


## How to Run

### Prerequisites
- PHP >= 8.0
- Composer
- Node.js & NPM
- Database (MySQL/MariaDB via XAMPP or similar)

### Steps

1.  **Start Apache & MySQL**: Open XAMPP Control Panel and start both modules.

2.  **Install Dependencies** (if not already installed):
    ```bash
    composer install
    npm install
    ```

3.  **Setup Environment**:
    - Ensure `.env` exists (copy `.env.example` to `.env` if needed).
    - Configure database settings in `.env`:
        ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=your_database_name
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Run Migrations**:
    ```bash
    php artisan migrate
    ```

5.  **Compile Assets**:
    Open a terminal and run:
    ```bash
    npm run dev
    ```

6.  **Start Server**:
    Open a **new** terminal and run:
    ```bash
    php artisan serve
    ```

7.  **Access the App**:
    Open your browser and click the link shown in the terminal (usually `http://127.0.0.1:8000`).
