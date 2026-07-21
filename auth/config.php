<?php
// error_reporting(E_ALL);
// ini_set("display_errors", true);

// Dynamic Database Configuration for Localhost & Hostinger
$http_host = $_SERVER['HTTP_HOST'] ?? '';
if ($http_host == 'localhost' || $http_host == '127.0.0.1' || strpos($http_host, '192.168.') === 0) {
    $conn = new mysqli('localhost', 'root', '', 'Dezo');
} else {
    $conn = new mysqli('localhost', 'u740980038_wp', 'CHANGE_ME', 'u740980038_wp');
}

$server = $_SERVER["SERVER_NAME"] ?? '';

// Fetch site settings from the database
$query = "SELECT * FROM site_settings LIMIT 1";
$result = db_query($conn, $query);

if ($result && db_num_rows($result) > 0) {
    $site_settings = db_fetch_assoc($result);
} else {
    // Default values in case settings are not found
    $site_settings = [
        'brand_name' => 'Default Brand Name',
        'logo_url' => 'default_logo.png',
        'site_link' => 'https://example.com',
        'whatsapp_number' => '0000000000',
        'copyright_text' => '© Default Copyright'
    ];
}
?>