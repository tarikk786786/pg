<?php
$conn_string = "host=san4iv3j.us-east.database.insforge.app port=5432 dbname=insforge user=postgres password=f76260816cf7fd8b63cd4a11314a0c8f sslmode=require";
$db = pg_connect($conn_string);
if (!$db) {
    echo "pg_connect failed: " . pg_last_error();
} else {
    echo "pg_connect success!";
}
?>
