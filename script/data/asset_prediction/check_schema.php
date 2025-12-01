<?php
$db_connection = mysqli_connect('localhost', 'root', '', 'siaptek');

echo "=== Checking Database Schema ===\n\n";

// Check category_item structure
echo ">> category_item columns:\n";
$result = mysqli_query($db_connection, "DESCRIBE category_item");
while ($row = mysqli_fetch_assoc($result)) {
    echo "   " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n>> master_item columns:\n";
$result = mysqli_query($db_connection, "DESCRIBE master_item");
while ($row = mysqli_fetch_assoc($result)) {
    echo "   " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n>> warehouse_location columns:\n";
$result = mysqli_query($db_connection, "DESCRIBE warehouse_location");
while ($row = mysqli_fetch_assoc($result)) {
    echo "   " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
