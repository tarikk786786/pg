<?php
require_once 'auth/config.php';
$res = mysqli_query($conn, 'DESCRIBE users');
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
