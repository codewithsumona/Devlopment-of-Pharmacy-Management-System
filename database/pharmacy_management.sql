-- ============================================================================
-- Pharmacy Management System Prototype - MySQL Database Schema & Sample Seed Data
-- Database Name: pharmacy_management
-- Designed for XAMPP / Apache / MySQL / phpMyAdmin
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `pharmacy_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pharmacy_management`;

-- --------------------------------------------------------
-- Table: users / staff
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `role` ENUM('Admin', 'Pharmacist', 'Staff') NOT NULL DEFAULT 'Pharmacist',
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `status`) VALUES
(1, 'admin', '$2y$10$92IXMO63O9365gjsUeS7O.u/KB.K61g/1S/3.10S.10S.10S.10S', 'Dr. Sarah Jenkins', 'admin@pharma.com', '+880 1711-000111', 'Admin', 'Active'),
(2, 'pharmacist', '$2y$10$92IXMO63O9365gjsUeS7O.u/KB.K61g/1S/3.10S.10S.10S.10S', 'Alex Rivera, PharmD', 'alex@pharma.com', '+880 1819-222333', 'Pharmacist', 'Active'),
(3, 'staff1', '$2y$10$92IXMO63O9365gjsUeS7O.u/KB.K61g/1S/3.10S.10S.10S.10S', 'David Miller', 'david@pharma.com', '+880 1912-444555', 'Staff', 'Active');

-- --------------------------------------------------------
-- Table: categories
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`, `category_name`, `description`) VALUES
(1, 'Analgesics / Antipyretics', 'Pain relief and fever reduction medication'),
(2, 'Gastric / Anti-ulcerants', 'Proton pump inhibitors and antacids'),
(3, 'Antihistamines', 'Allergy relief medications'),
(4, 'Antibiotics', 'Bacterial infection treatment'),
(5, 'Laxatives / Syrup', 'Digestive health and stool softeners'),
(6, 'Cardiovascular', 'Blood pressure and heart care');

-- --------------------------------------------------------
-- Table: suppliers
-- --------------------------------------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `supplier_name` VARCHAR(100) NOT NULL,
  `company_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `address` TEXT,
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `suppliers` (`id`, `supplier_name`, `company_name`, `phone`, `email`, `address`, `status`) VALUES
(1, 'Square Pharmaceuticals Ltd.', 'Square Pharma', '+880 2-8833047', 'info@squarepharma.com.bd', 'Square Centre, 48 Mohakhali C/A, Dhaka', 'Active'),
(2, 'Incepta Pharmaceuticals Ltd.', 'Incepta Pharma', '+880 2-8891688', 'contact@inceptapharma.com', '40 Shahid Tajuddin Sarani, Tejgaon, Dhaka', 'Active'),
(3, 'Beximco Pharmaceuticals Ltd.', 'Beximco Pharma', '+880 2-58611001', 'info@beximcopharma.com', '19 Dhanmondi R/A, Road 7, Dhaka', 'Active'),
(4, 'Renata Limited', 'Renata Ltd', '+880 2-8011013', 'sales@renata-ltd.com', 'Plot 1, Milk Vita Road, Section 7, Mirpur, Dhaka', 'Active');

-- --------------------------------------------------------
-- Table: medicines
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medicines`;
CREATE TABLE `medicines` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `medicine_name` VARCHAR(150) NOT NULL,
  `generic_name` VARCHAR(150) NOT NULL,
  `category_id` INT NOT NULL,
  `supplier_id` INT NOT NULL,
  `manufacturer` VARCHAR(100) NOT NULL,
  `batch_number` VARCHAR(50) NOT NULL,
  `purchase_price` DECIMAL(10,2) NOT NULL,
  `selling_price` DECIMAL(10,2) NOT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `min_stock_alert` INT DEFAULT 15,
  `expiry_date` DATE NOT NULL,
  `description` TEXT,
  `status` ENUM('In Stock', 'Low Stock', 'Out of Stock', 'Expired') DEFAULT 'In Stock',
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `medicines` (`id`, `medicine_name`, `generic_name`, `category_id`, `supplier_id`, `manufacturer`, `batch_number`, `purchase_price`, `selling_price`, `stock_quantity`, `expiry_date`, `description`, `status`) VALUES
(1, 'Napa 500mg', 'Paracetamol', 1, 1, 'Square Pharmaceuticals', 'SQ-N2024-01', 1.10, 1.50, 450, '2027-11-30', 'Standard pain relief and antipyretic tablets.', 'In Stock'),
(2, 'Seclo 20mg', 'Omeprazole', 2, 1, 'Square Pharmaceuticals', 'SQ-S2024-88', 4.50, 6.00, 12, '2026-09-15', 'Proton pump inhibitor for gastric relief.', 'Low Stock'),
(3, 'Fexo 120mg', 'Fexofenadine Hydrochloride', 3, 3, 'Beximco Pharmaceuticals', 'BX-F120-04', 7.00, 9.50, 85, '2026-12-01', 'Non-drowsy antihistamine for allergy symptoms.', 'In Stock'),
(4, 'Avolac Syrup 100ml', 'Lactulose', 5, 2, 'Incepta Pharmaceuticals', 'IN-AV2023-99', 110.00, 140.00, 4, '2026-05-10', 'Osmotic laxative syrup for constipation management.', 'Low Stock'),
(5, 'Omeprazole 20mg', 'Omeprazole', 2, 4, 'Renata Limited', 'RN-OMP-33', 3.80, 5.00, 0, '2026-10-20', 'Generic gastric acid suppressor.', 'Out of Stock'),
(6, 'Azithrocin 500mg', 'Azithromycin', 4, 3, 'Beximco Pharmaceuticals', 'BX-AZ-771', 28.00, 35.00, 120, '2027-04-15', 'Broad-spectrum macrolide antibiotic.', 'In Stock'),
(7, 'Ceevit 250mg Chewable', 'Ascorbic Acid (Vitamin C)', 1, 1, 'Square Pharmaceuticals', 'SQ-CV-102', 1.80, 2.50, 300, '2025-01-10', 'Chewable vitamin C supplement for immunity.', 'Expired');

-- --------------------------------------------------------
-- Table: sales
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_no` VARCHAR(50) NOT NULL UNIQUE,
  `customer_name` VARCHAR(100) DEFAULT 'Walk-in Customer',
  `customer_phone` VARCHAR(20) DEFAULT '',
  `pharmacist_id` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `discount` DECIMAL(10,2) DEFAULT 0.00,
  `grand_total` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Cash',
  `payment_status` ENUM('Paid', 'Pending') DEFAULT 'Paid',
  `sale_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pharmacist_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `sales` (`id`, `invoice_no`, `customer_name`, `customer_phone`, `pharmacist_id`, `subtotal`, `discount`, `grand_total`, `payment_method`, `payment_status`, `sale_date`) VALUES
(1, 'INV-2026-001', 'Mr. Rahman', '+880 1711-223344', 2, 150.00, 10.00, 140.00, 'Cash', 'Paid', '2026-08-08 10:15:00'),
(2, 'INV-2026-002', 'Walk-in Customer', '', 2, 85.50, 0.00, 85.50, 'Card', 'Paid', '2026-08-08 11:40:00'),
(3, 'INV-2026-003', 'Mrs. Jahan', '+880 1819-998877', 1, 350.00, 25.00, 325.00, 'Mobile Banking (bKash)', 'Paid', '2026-08-08 13:20:00');

-- --------------------------------------------------------
-- Table: sale_items
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE `sale_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sale_id` INT NOT NULL,
  `medicine_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `sale_items` (`id`, `sale_id`, `medicine_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 1, 20, 1.50, 30.00),
(2, 1, 4, 1, 140.00, 140.00),
(3, 2, 3, 9, 9.50, 85.50),
(4, 3, 6, 10, 35.00, 350.00);

-- --------------------------------------------------------
-- Table: purchases
-- --------------------------------------------------------
DROP TABLE IF EXISTS `purchases`;
CREATE TABLE `purchases` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `purchase_no` VARCHAR(50) NOT NULL UNIQUE,
  `supplier_id` INT NOT NULL,
  `purchase_date` DATE NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Received', 'Pending', 'Cancelled') DEFAULT 'Received',
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `purchases` (`id`, `purchase_no`, `supplier_id`, `purchase_date`, `total_amount`, `status`) VALUES
(1, 'PO-2026-101', 1, '2026-08-01', 495.00, 'Received'),
(2, 'PO-2026-102', 3, '2026-08-03', 3360.00, 'Received'),
(3, 'PO-2026-103', 2, '2026-08-07', 1100.00, 'Pending');

-- --------------------------------------------------------
-- Table: purchase_items
-- --------------------------------------------------------
DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE `purchase_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `purchase_id` INT NOT NULL,
  `medicine_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `purchase_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`purchase_id`) REFERENCES `purchases`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `purchase_items` (`id`, `purchase_id`, `medicine_id`, `quantity`, `purchase_price`, `total_price`) VALUES
(1, 1, 1, 450, 1.10, 495.00),
(2, 2, 6, 120, 28.00, 3360.00),
(3, 3, 4, 10, 110.00, 1100.00);
