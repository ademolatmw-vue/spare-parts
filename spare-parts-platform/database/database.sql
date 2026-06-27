-- SparePartsNG Database Schema - FIXED WITH MATCHING PRODUCTS
DROP DATABASE IF EXISTS spare_parts_db;
CREATE DATABASE spare_parts_db;
USE spare_parts_db;

-- Tables (same as before)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer', 'vendor', 'admin') DEFAULT 'customer',
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    category_icon VARCHAR(50),
    description TEXT
);

CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    category_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    brand VARCHAR(100),
    model_compatibility TEXT,
    stock_quantity INT DEFAULT 0,
    image_url VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE IF NOT EXISTS shops (
    shop_id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    shop_name VARCHAR(200) NOT NULL,
    shop_description TEXT,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(20),
    email VARCHAR(100),
    rating DECIMAL(3, 2) DEFAULT 0,
    is_verified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (vendor_id) REFERENCES users(user_id)
);

-- FIXED CATEGORIES (exact match query)
INSERT INTO categories (category_name) VALUES
('Engine Parts'), ('Brakes'), ('Suspension'), ('Electrical'), ('Body Parts'), ('Transmission'), ('Cooling'), ('Exhaust'), ('Interior'), ('Wheels & Tires');

-- Vendors & Shops (12 vendors)
INSERT INTO users (name, email, phone, password, user_type) VALUES
('Lagos Auto Parts', 'lagos@auto.com', '08012345678', 'pass', 'vendor'),
('Abuja Motor Hub', 'abuja@hub.com', '08023456789', 'pass', 'vendor'),
('PH Spare Parts', 'ph@spares.com', '08034567890', 'pass', 'vendor'),
('Ibadan Car Center', 'ibadan@car.com', '08045678901', 'pass', 'vendor'),
('Kano Motors', 'kano@motors.com', '08056789012', 'pass', 'vendor'),
('Benin Auto', 'benin@auto.com', '08067890123', 'pass', 'vendor'),
('Enugu Spares', 'enugu@spares.com', '08078901234', 'pass', 'vendor'),
('Abeokuta Parts', 'abeokuta@parts.com', '08089012345', 'pass', 'vendor'),
('Warri Auto', 'warri@auto.com', '08090123456', 'pass', 'vendor'),
('Jos Center', 'jos@center.com', '08001234567', 'pass', 'vendor'),
('Ilorin Motors', 'ilorin@motors.com', '08112345678', 'pass', 'vendor'),
('Akwete Spares', 'akwete@spares.com', '08123456789', 'pass', 'vendor');

INSERT INTO shops (vendor_id, shop_name, address, city, state, latitude, longitude, phone, email, rating) VALUES
(1, 'Lagos Auto Parts Center', 'Ikeja', 'Lagos', 'Lagos', 6.5244, 3.3792, '08012345678', 'lagos@email.com', 4.8),
(2, 'Abuja Motor Parts Hub', 'Gwarinpa', 'Abuja', 'FCT', 9.0765, 7.3986, '08023456789', 'abuja@email.com', 5.0),
(3, 'PH Spare Parts', 'Trans Woji', 'PH', 'Rivers', 4.7774, 7.0134, '08034567890', 'ph@email.com', 4.2),
(4, 'Ibadan Car Center', 'Ring Road', 'Ibadan', 'Oyo', 7.3775, 3.9470, '08045678901', 'ibadan@email.com', 4.5),
(5, 'Kano Motors Limited', 'Sabon Gari', 'Kano', 'Kano', 12.0022, 8.5919, '08056789012', 'kano@email.com', 4.6),
(6, 'Benin Auto World', 'New Lagos Road', 'Benin', 'Edo', 6.3350, 5.6037, '08067890123', 'benin@email.com', 4.7),
(7, 'Enugu Motor Spares', 'New Market', 'Enugu', 'Enugu', 6.4595, 7.4958, '08078901234', 'enugu@email.com', 4.4),
(8, 'Abeokuta Auto Parts', 'Sapon Junction', 'Abeokuta', 'Ogun', 7.1512, 3.3490, '08089012345', 'abeokuta@email.com', 4.3),
(9, 'Warri Auto Center', 'Effurun', 'Warri', 'Delta', 5.5174, 5.7550, '08090123456', 'warri@email.com', 4.5),
(10, 'Jos Car Solutions', 'Bukuru', 'Jos', 'Plateau', 9.9285, 8.8925, '08001234567', 'jos@email.com', 4.6),
(11, 'Ilorin Auto Hub', 'Taiwo Road', 'Ilorin', 'Kwara', 8.4799, 4.5419, '08112345678', 'ilorin@email.com', 4.4),
(12, 'Akwete Auto Spares', 'Old Road', 'Akwete', 'Abia', 5.0333, 7.7333, '08123456789', 'akwete@email.com', 4.3);

-- FIXED PRODUCTS (3-4 per category, matching names)
INSERT INTO products (vendor_id, category_id, product_name, description, price, brand, stock_quantity) VALUES
-- Engine Parts (1)
(1, 1, 'Toyota Oil Filter', 'Engine oil filter', 2500, 'Toyota', 50),
(1, 1, 'Honda Air Filter', 'Air intake filter', 3200, 'Honda', 35),
(2, 1, 'NGK Spark Plugs', 'Iridium plugs set', 8500, 'NGK', 100),
(3, 1, 'Nissan Water Pump', 'Cooling water pump', 12500, 'Nissan', 20),

-- Brakes (2)
(1, 2, 'Brembo Brake Pads', 'Front ceramic pads', 15000, 'Brembo', 40),
(2, 2, 'Bosch Brake Disc', 'Rear ventilated disc', 18000, 'Bosch', 25),
(3, 2, 'Aisin Brake Caliper', 'Left front caliper', 22000, 'Aisin', 15),
(4, 2, 'Motul Brake Fluid', 'DOT4 1L bottle', 3500, 'Motul', 200),

-- Suspension (3)
(1, 3, 'Monroe Shock Absorber', 'Front pair', 45000, 'Monroe', 20),
(2, 3, 'KYB Strut Assembly', 'Complete unit', 55000, 'KYB', 15),
(3, 3, 'Meyle Control Arm', 'Upper with ball joint', 28000, 'Meyle', 18),
(4, 3, 'SKF Wheel Bearing', 'Hub unit', 12000, 'SKF', 30),

-- Electrical (4) - NOW 4 FOUND!
(1, 4, 'Exide Car Battery', '12V 75AH', 45000, 'Exide', 50),
(2, 4, 'Denso Alternator', '120A unit', 65000, 'Denso', 12),
(3, 4, 'Mitsubishi Starter', 'OEM starter motor', 48000, 'Mitsubishi', 10),
(4, 4, 'Philips LED Headlight', 'H4 LED kit', 18000, 'Philips', 80),

-- Body Parts (5)
(1, 5, 'Toyota Side Mirror', 'Driver side', 25000, 'Toyota', 15),
(2, 5, 'Honda Front Bumper', 'Paint ready', 35000, 'Honda', 8),
(3, 5, 'Nissan Tail Light', 'LED assembly', 22000, 'Nissan', 12),
(4, 5, 'Universal Door Handle', 'Interior black', 5500, 'Aftermarket', 100),

-- Transmission (6)
(1, 6, 'Exedy Clutch Kit', 'Complete kit', 85000, 'Exedy', 10),
(2, 6, 'Toyota Transmission Filter', 'AT filter kit', 12000, 'Toyota', 25),
(3, 6, 'GSP CV Joint', 'Inner joint', 18000, 'GSP', 20),
(4, 6, 'Shell Gear Oil', '80W-90 1L', 4500, 'Shell', 150),

-- Cooling (7)
(1, 7, 'Toyota Radiator', 'Full assembly', 48000, 'Toyota', 8),
(2, 7, 'Gates Thermostat', 'OEM thermostat', 8500, 'Gates', 30),
(3, 7, 'Denso Water Pump', 'With gasket', 15000, 'Denso', 18),
(4, 7, 'Prestone Coolant', '5L antifreeze', 6000, 'Prestone', 100),

-- Exhaust (8)
(1, 8, 'Walker Muffler', 'Rear muffler', 25000, 'Walker', 25),
(2, 8, 'Universal Catalytic', '3-way catalytic converter', 45000, 'Magnaflow', 10),
(3, 8, 'Exhaust Pipe', 'Intermediate pipe', 18000, 'Ansa', 20),
(4, 8, 'Silencer Clamp', 'Stainless clamp', 1500, 'Aftermarket', 100),

-- Interior (9)
(1, 9, 'Leather Seat Covers', 'Full set Toyota', 35000, 'Leathertex', 20),
(2, 9, 'Steering Cover', 'Leather universal', 5500, 'Generic', 80),
(3, 9, 'Rubber Floor Mats', 'Toyota Camry set', 12000, 'Weathertech', 40),
(4, 9, 'Dashboard Mat', 'Honda Civic mat', 8500, 'MotoShield', 25),

-- Wheels (10)
(6,10,'Michelin Tire 205/55R16', 'All season tire', 45000, 'Michelin', 40),
(10,10,'Continental 225/45R17', 'Performance tire', 55000, 'Continental', 30),
(11,10,'Alloy Rim 16 inch', 'Toyota Camry rim', 35000, 'Original', 15),
(6,10,'TPMS Sensor', 'Tire pressure sensor', 8500, 'Schrader', 60);

-- Reviews, Orders, Search History tables (same)
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS search_history (
    search_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    search_term VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    results_count INT,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- READY! Import to phpMyAdmin -> spare_parts_db

