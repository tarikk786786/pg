<?php
require_once 'auth/config.php';
$res = db_query($conn, 'DESCRIBE users');
while($row = db_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
