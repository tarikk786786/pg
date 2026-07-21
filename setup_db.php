<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = db_connect("localhost", "u740980038_Aviator", "CHANGE_ME", "u740980038_Aviator");

if (!$conn) {
    die("Connection failed: " . db_connect_error());
}

$sql1 = "CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql2 = "CREATE TABLE IF NOT EXISTS `subscription_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `limit_amount` decimal(15,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql3 = "ALTER TABLE `users` 
  ADD COLUMN `transaction_limit` DECIMAL(15,2) DEFAULT 0.00,
  ADD COLUMN `transaction_volume` DECIMAL(15,2) DEFAULT 0.00;";

if (db_query($conn, $sql1)) echo "admin_users created. ";
else echo "Error creating admin_users: " . db_error($conn) . ". ";

if (db_query($conn, $sql2)) echo "subscription_plans created. ";
else echo "Error creating subscription_plans: " . db_error($conn) . ". ";

if (db_query($conn, $sql3)) echo "users altered. ";
else echo "Error altering users: " . db_error($conn) . ". ";

$pw = password_hash('admin123', PASSWORD_DEFAULT);
$sql4 = "INSERT INTO `admin_users` (`username`, `password`) VALUES ('admin', '$pw')";
if (db_query($conn, $sql4)) echo "Admin user created. ";

db_close($conn);
?>
