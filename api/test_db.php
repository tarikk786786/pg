<?php
header('Content-Type: text/plain');

$insforge_url = "postgresql://postgres:f76260816cf7fd8b63cd4a11314a0c8f@san4iv3j.us-east.database.insforge.app:5432/insforge?sslmode=disable";
$parsed = parse_url($insforge_url);
$host = $parsed['host'];
$port = $parsed['port'];
$user = $parsed['user'];
$pass = $parsed['pass'];
$db = ltrim($parsed['path'], '/');

echo "Testing connections...\n\n";

$modes = ['disable', 'allow', 'prefer', 'require', 'verify-ca', 'verify-full'];

foreach ($modes as $mode) {
    echo "--- pg_connect with sslmode=$mode ---\n";
    $conn_string = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=$mode connect_timeout=5";
    $conn = @pg_connect($conn_string);
    if ($conn) {
        echo "SUCCESS!\n";
        pg_close($conn);
    } else {
        echo "FAILED: " . pg_last_error() . "\n";
    }
    echo "\n";
}

foreach ($modes as $mode) {
    echo "--- PDO with sslmode=$mode ---\n";
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$mode";
    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
        echo "SUCCESS!\n";
    } catch (PDOException $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
