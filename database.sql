-- Create database
CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category_id INT,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, slug) VALUES 
('Electronics', 'electronics'),
('Fashion', 'fashion'),
('Home & Garden', 'home-garden'),
('Sports', 'sports'),
('Accessories', 'accessories');

-- Insert sample products
INSERT INTO products (name, description, price, image, category_id, stock) VALUES
('Premium Smart Watch', 'Feature-rich smartwatch with heart rate monitor, GPS, and long battery life', 299.99, 'smartwatch.jpg', 1, 50),
('Wireless Bluetooth Headphones', 'High-quality noise-canceling wireless headphones with 30-hour battery', 199.99, 'headphones.jpg', 1, 75),
('Designer Sunglasses', 'Stylish designer sunglasses with UV protection', 89.99, 'sunglasses.jpg', 5, 100),
('Leather Wallet', 'Genuine leather wallet with RFID protection', 59.99, 'wallet.jpg', 5, 120),
('Smart Backpack', 'Durable smart backpack with USB charging port', 149.99, 'backpack.jpg', 5, 60),
('Fitness Tracker', 'Advanced fitness tracker with sleep monitoring', 129.99, 'fitness-tracker.jpg', 1, 80),
('Coffee Maker', 'Programmable coffee maker with thermal carafe', 199.99, 'coffee-maker.jpg', 3, 40),
('Yoga Mat', 'Premium non-slip yoga mat, extra thick for comfort', 39.99, 'yoga-mat.jpg', 4, 150),
('Running Shoes', 'Lightweight running shoes with cushioning', 119.99, 'running-shoes.jpg', 4, 90),
('Wireless Mouse', 'Ergonomic wireless mouse with long battery life', 49.99, 'wireless-mouse.jpg', 1, 130),
('Laptop Stand', 'Adjustable laptop stand for ergonomic work', 79.99, 'laptop-stand.jpg', 1, 65),
('Indoor Plant', 'Low-maintenance indoor plant for home decor', 29.99, 'indoor-plant.jpg', 3, 85),
('Denim Jacket', 'Classic denim jacket with modern fit', 129.99, 'denim-jacket.jpg', 2, 70),
('Bluetooth Speaker', 'Portable Bluetooth speaker with 20-hour battery', 89.99, 'speaker.jpg', 1, 95),
('Water Bottle', 'Insulated stainless steel water bottle', 24.99, 'water-bottle.jpg', 4, 200);
