<?php
require_once 'admin/config.php';

// 1. approved_domains table
$sql1 = "CREATE TABLE IF NOT EXISTS `approved_domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
db_query($conn, $sql1);

// 2. website_settings table
$sql2 = "CREATE TABLE IF NOT EXISTS `website_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT 'Super Admin Dashboard',
  `logo_url` text DEFAULT NULL,
  `favicon_url` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
db_query($conn, $sql2);

// Check if website_settings is empty, insert default
$check = db_query($conn, "SELECT * FROM website_settings");
if(db_num_rows($check) == 0){
    db_query($conn, "INSERT INTO website_settings (title, contact_email) VALUES ('Super Admin Dashboard', 'admin@example.com')");
}

// 3. admin_payments table
$sql3 = "CREATE TABLE IF NOT EXISTS `admin_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_link_id` varchar(255) NOT NULL,
  `amount_type` enum('fixed','non_fixed') DEFAULT 'fixed',
  `amount` decimal(10,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `customer_mobile` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
db_query($conn, $sql3);

// 4. Update users table for status
$sql4 = "ALTER TABLE `users` ADD COLUMN `status` enum('active','disabled') DEFAULT 'active' AFTER `acc_ban`";
db_query($conn, $sql4);

// 5. Update gift_codes table for created_by
$sql5 = "ALTER TABLE `gift_codes` ADD COLUMN `created_by` int(11) DEFAULT 0 AFTER `amount`";
db_query($conn, $sql5);

echo "Database updates complete!";
?>
