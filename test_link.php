<?php
require 'pages/dbInfo.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
$stmt = $pdo->query("SELECT link_token FROM payment_links ORDER BY id DESC LIMIT 1");
echo $stmt->fetchColumn();
?>
