<?php
$db_connection = mysqli_connect('localhost', 'root', '', 'siaptek');

echo "=== Testing item_detail values ===\n\n";

// Check itemd_status values
$q1 = "SELECT DISTINCT itemd_status, COUNT(*) as total FROM item_detail GROUP BY itemd_status";
$r1 = mysqli_query($db_connection, $q1);
echo "itemd_status values:\n";
while($row = mysqli_fetch_assoc($r1)) {
    echo "  '{$row['itemd_status']}' => {$row['total']} rows\n";
}

// Check total active assets
$q2 = "SELECT COUNT(*) as total FROM item_detail WHERE itemd_status = '0'";
$r2 = mysqli_query($db_connection, $q2);
$row2 = mysqli_fetch_assoc($r2);
echo "\nTotal with itemd_status = '0': {$row2['total']}\n";

// Check total Active assets
$q3 = "SELECT COUNT(*) as total FROM item_detail WHERE itemd_status = 'Active'";
$r3 = mysqli_query($db_connection, $q3);
$row3 = mysqli_fetch_assoc($r3);
echo "Total with itemd_status = 'Active': {$row3['total']}\n";
?>
