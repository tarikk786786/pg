<?php
require 'pages/dbInfo.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
$tables = ['paytm_tokens', 'phonepe_tokens', 'bharatpe_tokens', 'googlepay_tokens', 'sbi_tokens', 'freecharge_tokens', 'mobikwik_tokens'];
$schema = [];
foreach($tables as $t) {
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM $t");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $schema[$t] = $cols;
    } catch(Exception $e) {}
}
echo json_encode($schema);
?>
