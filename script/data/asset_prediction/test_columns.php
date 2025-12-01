<?php
include "../../library/db_connection.php";

echo "=== return_detail column names ===\n\n";
$r = mysqli_query($db_connection, "SHOW COLUMNS FROM return_detail");
while ($row = mysqli_fetch_assoc($r)) {
    echo "{$row['Field']} - {$row['Type']}\n";
}

echo "\n\n=== issuing_detail column names ===\n\n";
$r = mysqli_query($db_connection, "SHOW COLUMNS FROM issuing_detail");
while ($row = mysqli_fetch_assoc($r)) {
    echo "{$row['Field']} - {$row['Type']}\n";
}
