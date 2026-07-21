<?php
mysqli_connect("localhost", "u740980038_Aviator", "CHANGE_ME", "u740980038_Aviator");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    echo "TABLE: " . $row[0] . "\n";
    $cols = mysqli_query($conn, "SHOW COLUMNS FROM `" . $row[0] . "`");
    while ($col = mysqli_fetch_assoc($cols)) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
?>
