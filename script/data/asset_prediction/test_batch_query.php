<?php
include "../../library/check_session.php";
include "../../library/db_connection.php";

$branch_id = $_SESSION['ses_id_branch'];

echo "=== TESTING BATCH PREDICTION QUERY ===\n\n";
echo "Branch ID from session: $branch_id\n\n";

$query_assets = "
SELECT 
    id.itemd_id,
    id.itemd_code,
    mi.masti_name,
    id.branch_id,
    COALESCE(MAX(ap.prediction_date), '1970-01-01') as last_prediction
FROM item_detail id
INNER JOIN master_item mi ON mi.masti_id = id.masti_id
LEFT JOIN asset_predictions ap ON ap.itemd_id = id.itemd_id
WHERE id.branch_id = '$branch_id'
  AND id.itemd_status = '0'
  AND id.itemd_is_wo = '0'
  AND (id.itemd_is_dispossed IS NULL OR id.itemd_is_dispossed = '0')
GROUP BY id.itemd_id
LIMIT 10
";

echo "Query:\n$query_assets\n\n";

$result = mysqli_query($db_connection, $query_assets);
$total = mysqli_num_rows($result);

echo "Total assets found: $total\n\n";

if ($total > 0) {
    echo "Sample assets:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- ID: {$row['itemd_id']}, Code: {$row['itemd_code']}, Branch: {$row['branch_id']}, Last prediction: {$row['last_prediction']}\n";
    }
}

// Cek total aset di branch ini
$q_check = "SELECT COUNT(*) as total FROM item_detail WHERE branch_id = '$branch_id' AND itemd_status = '0'";
$r_check = mysqli_query($db_connection, $q_check);
$row_check = mysqli_fetch_assoc($r_check);
echo "\n\nTotal aset aktif di branch $branch_id: {$row_check['total']}\n";
