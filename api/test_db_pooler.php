<?php
header('Content-Type: text/plain');

$host = "san4iv3j.us-east.database.insforge.app";
$port = "6543"; // Pooler port
$user = "postgres";
$pass = "f76260816cf7fd8b63cd4a11314a0c8f";
$db = "insforge";

echo "Testing port 6543 connections...\n\n";

$modes = ['require'];
foreach ($modes as $mode) {
    echo "Testing pg_connect with sslmode=$mode...\n";
    $conn_string = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=$mode connect_timeout=5";
    
    // Capture errors
    error_clear_last();
    $conn = @pg_connect($conn_string);
    if ($conn) {
        echo "SUCCESS!\n";
        pg_close($conn);
    } else {
        $err = error_get_last();
        echo "FAILED: " . ($err ? $err['message'] : "Unknown error") . "\n";
    }
}
