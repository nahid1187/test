CREATE DATABASE IF NOT EXISTS salon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE salon_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','salon','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE salons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    salon_name VARCHAR(150) NOT NULL,
    location VARCHAR(255) DEFAULT '',
    rating DECIMAL(3,1) DEFAULT 0.0,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salon_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE
);

CREATE TABLE barbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salon_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    salon_id INT NOT NULL,
    service_id INT NOT NULL,
    barber_id INT NOT NULL,
    appt_date DATE NOT NULL,
    appt_time VARCHAR(30) NOT NULL,
    status ENUM('Pending','Accepted','Rejected','Completed','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salon_id INT NOT NULL,
    customer_id INT NOT NULL,
    rating TINYINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (salon_id, customer_id),
    FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default admin (password: admin123)
INSERT INTO users (name,email,password,role)
VALUES ('Admin','admin@salon.com','$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm','admin');
