<?php
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');

$http_host = $_SERVER['HTTP_HOST'] ?? '';
if ($http_host == 'localhost' || $http_host == '127.0.0.1' || strpos($http_host, '192.168.') === 0) {
    $dyn_dbHost = "localhost";
    $dyn_dbLogin = "root";
    $dyn_dbPwd = "";
    $dyn_dbName = "Dezo";
} else {
    $dyn_dbHost = "localhost";
    $dyn_dbLogin = "u740980038_wp";
    $dyn_dbPwd = "CHANGE_ME";
    $dyn_dbName = "u740980038_wp";
}

// Database configuration constants
define('DB_HOST', $dyn_dbHost);
define('DB_USERNAME', $dyn_dbLogin);
define('DB_PASSWORD', $dyn_dbPwd);
define('DB_NAME', $dyn_dbName);

function connect_database() {
    // If on Vercel and trying to connect to localhost, it will hang until 504 timeout. Fail fast.
    if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL'])) {
        if (DB_HOST === 'localhost' || DB_HOST === '127.0.0.1') {
            die("<h1>Database Configuration Required</h1><p>You are running on Vercel, but your database is configured to connect to <code>localhost</code>. Vercel cannot host a local MySQL database.</p><p>Please configure a remote MySQL database (e.g. Aiven, PlanetScale) in <code>pages/dbInfo.php</code>.</p>");
        }
    }
    
    // Set a short timeout (3 seconds) to prevent serverless function hanging
    $con = mysqli_init();
    mysqli_options($con, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
    $connected = @mysqli_real_connect($con, DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if (!$connected) {
        die("Database Connection failed: " . mysqli_connect_error());
    }
    return $con;
}
?>