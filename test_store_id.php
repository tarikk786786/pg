<?php
require 'pages/dbInfo.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
$stmt = $pdo->query("SHOW COLUMNS FROM store_id");
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
?>
