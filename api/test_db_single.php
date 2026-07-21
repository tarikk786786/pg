<?php
header('Content-Type: text/plain');

$insforge_url = "postgresql://postgres:f76260816cf7fd8b63cd4a11314a0c8f@san4iv3j.us-east.database.insforge.app:5432/insforge";
$parsed = parse_url($insforge_url);
$host = $parsed['host'];
$port = $parsed['port'];
$user = $parsed['user'];
$pass = $parsed['pass'];
$db = ltrim($parsed['path'], '/');

echo "Testing SINGLE connection...\n\n";

$conn_string = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=require connect_timeout=3";
$conn = @pg_connect($conn_string);
if ($conn) {
    echo "pg_connect SUCCESS!\n";
    pg_close($conn);
} else {
    echo "pg_connect FAILED: " . pg_last_error() . "\n";
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
    echo "PDO SUCCESS!\n";
} catch (PDOException $e) {
    echo "PDO FAILED: " . $e->getMessage() . "\n";
}
