-- E-Commerce Database Setup
-- Run this in phpMyAdmin or MySQL

CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    stock INT DEFAULT 0,
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert Default Users
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@gmail.com', 'admin', 'admin'),
('User', 'user@gmail.com', 'user', 'user');

-- Insert Sample Products
INSERT INTO products (name, description, price, stock, category) VALUES
('Samsung Galaxy A54', 'Excellent smartphone with great camera', 35000.00, 10, 'Electronics'),
('Apple iPhone 14', 'Premium smartphone with iOS', 110000.00, 5, 'Electronics'),
('Nike Running Shoes', 'Comfortable running shoes for daily use', 4500.00, 20, 'Footwear'),
('Levi''s Jeans', 'Classic blue denim jeans', 3200.00, 15, 'Clothing'),
('Sony Headphones', 'Wireless noise cancelling headphones', 8500.00, 8, 'Electronics'),
('Cotton T-Shirt', 'Soft premium cotton t-shirt', 800.00, 50, 'Clothing'),
('Laptop Bag', 'Waterproof laptop bag 15 inch', 1500.00, 25, 'Accessories'),
('Smart Watch', 'Fitness tracker smart watch', 6500.00, 12, 'Electronics');
