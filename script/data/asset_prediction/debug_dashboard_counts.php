<?php
include "../../library/check_session.php";
include "../../library/db_connection.php";

echo "=== CHECKING DASHBOARD QUERY LOGIC ===\n\n";
echo "Branch ID: $branch_id\n\n";

// Query tanpa filter branch (ALL branches)
$query_stats = "SELECT 
                  COUNT(DISTINCT ap.itemd_id) as total_assets,
                  SUM(CASE WHEN ap.urgency_level = 'critical' THEN 1 ELSE 0 END) as critical_count,
                  SUM(CASE WHEN ap.urgency_level = 'high' THEN 1 ELSE 0 END) as high_count,
                  SUM(CASE WHEN ap.urgency_level = 'medium' THEN 1 ELSE 0 END) as medium_count,
                  SUM(CASE WHEN ap.urgency_level = 'low' THEN 1 ELSE 0 END) as low_count
                FROM asset_predictions ap
                INNER JOIN (
                    SELECT itemd_id, MAX(prediction_date) as max_date
                    FROM asset_predictions
                    GROUP BY itemd_id
                ) latest ON ap.itemd_id = latest.itemd_id AND ap.prediction_date = latest.max_date";
$result_stats = mysqli_query($db_connection, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);

echo "Query Dashboard (with latest filter):\n";
echo "Total Unique Assets: " . $stats['total_assets'] . "\n";
echo "CRITICAL: " . $stats['critical_count'] . "\n";
echo "HIGH: " . $stats['high_count'] . "\n";
echo "MEDIUM: " . $stats['medium_count'] . "\n";
echo "LOW: " . $stats['low_count'] . "\n";
echo "SUM: " . ($stats['critical_count'] + $stats['high_count'] + $stats['medium_count'] + $stats['low_count']) . "\n";

echo "\n=== VERIFY: Count by Urgency Level ===\n";
$q2 = "SELECT ap.urgency_level, COUNT(*) as cnt
       FROM asset_predictions ap
       INNER JOIN (
           SELECT itemd_id, MAX(prediction_date) as max_date
           FROM asset_predictions
           GROUP BY itemd_id
       ) latest ON ap.itemd_id = latest.itemd_id AND ap.prediction_date = latest.max_date
       GROUP BY ap.urgency_level
       ORDER BY FIELD(ap.urgency_level, 'critical', 'high', 'medium', 'low')";
$r2 = mysqli_query($db_connection, $q2);
echo "Direct count by urgency:\n";
while($row = mysqli_fetch_assoc($r2)) {
    echo strtoupper($row['urgency_level']) . ": " . $row['cnt'] . "\n";
}

echo "\n=== CHECK DUPLICATES IN LATEST ===\n";
$q3 = "SELECT itemd_id, COUNT(*) as cnt
       FROM (
           SELECT ap.*
           FROM asset_predictions ap
           INNER JOIN (
               SELECT itemd_id, MAX(prediction_date) as max_date
               FROM asset_predictions
               GROUP BY itemd_id
           ) latest ON ap.itemd_id = latest.itemd_id AND ap.prediction_date = latest.max_date
       ) filtered
       GROUP BY itemd_id
       HAVING cnt > 1
       LIMIT 10";
$r3 = mysqli_query($db_connection, $q3);
echo "Assets with duplicate latest predictions: " . mysqli_num_rows($r3) . "\n";
while($row = mysqli_fetch_assoc($r3)) {
    echo "  itemd_id " . $row['itemd_id'] . ": " . $row['cnt'] . " records\n";
}

echo "\n=== SAMPLE DUPLICATES ===\n";
$q4 = "SELECT ap.itemd_id, ap.prediction_date, ap.urgency_level, ap.estimasi_bulan_penggantian, ap.created_at
       FROM asset_predictions ap
       INNER JOIN (
           SELECT itemd_id, MAX(prediction_date) as max_date
           FROM asset_predictions
           GROUP BY itemd_id
       ) latest ON ap.itemd_id = latest.itemd_id AND ap.prediction_date = latest.max_date
       WHERE ap.itemd_id = 2541
       ORDER BY ap.created_at";
$r4 = mysqli_query($db_connection, $q4);
echo "Elevator (2541) predictions:\n";
while($row = mysqli_fetch_assoc($r4)) {
    echo "  Date: " . $row['prediction_date'] . " | Urgency: " . $row['urgency_level'] . " | Months: " . $row['estimasi_bulan_penggantian'] . " | Created: " . $row['created_at'] . "\n";
}
?>
