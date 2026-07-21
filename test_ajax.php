<?php
require 'pages/dbInfo.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
$stmt = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 1');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($row);
?>
