<?php
db_connect("localhost", "u740980038_Aviator", "CHANGE_ME", "u740980038_Aviator");
if (!$conn) {
    die("Connection failed: " . db_connect_error());
}
$result = db_query($conn, "SHOW TABLES");
while ($row = db_fetch_row($result)) {
    echo "TABLE: " . $row[0] . "\n";
    $cols = db_query($conn, "SHOW COLUMNS FROM `" . $row[0] . "`");
    while ($col = db_fetch_assoc($cols)) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
?>
