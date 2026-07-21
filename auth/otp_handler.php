<?php
session_start();
include "config.php";
include "function.php";

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$action = $_POST['action'];
$mobile = $_POST['mobile'] ?? '';

if (strlen($mobile) !== 10) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid mobile number']);
    exit;
}

if ($action === 'send_otp') {
    // Check if mobile already exists
    $mobile = db_real_escape_string($conn, $mobile);
    $checkMobile = db_query($conn, "SELECT id FROM users WHERE mobile = '$mobile'");
    if (db_num_rows($checkMobile) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Mobile number already registered']);
        exit;
    }

    $otp = rand(100000, 999999);
    $_SESSION['register_otp_' . $mobile] = $otp;

    if (isset($whatsapp_api_url) && !empty($whatsapp_api_url)) {
        // Call WhatsApp API
        $message = "Your Dezo registration OTP is $otp. Do not share this with anyone.";
        $url = rtrim($whatsapp_api_url, '/') . "?apikey=" . urlencode($apikey) . "&sender=" . urlencode($sender_id) . "&number=91" . urlencode($mobile) . "&message=" . urlencode($message);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
    } else {
        // For testing locally without API keys, we can write the OTP to a file or allow bypass
        // error_log("OTP for $mobile is $otp");
        // $_SESSION['register_otp_' . $mobile] = '123456'; // Default for test
    }

    echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully']);
    exit;
} elseif ($action === 'verify_otp') {
    $otp = $_POST['otp'] ?? '';
    
    // For test bypass if needed: if ($otp === '123456') { ... }
    
    if (isset($_SESSION['register_otp_' . $mobile]) && (string)$_SESSION['register_otp_' . $mobile] === (string)$otp) {
        unset($_SESSION['register_otp_' . $mobile]);
        echo json_encode(['status' => 'success', 'message' => 'OTP verified']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
}
