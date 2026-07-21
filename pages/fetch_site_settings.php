<?php
// Include the database connection
include "dbInfo.php";

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
