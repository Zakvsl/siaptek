<?php
include "../../library/db_connection.php";

echo "=== URGENCY DISTRIBUTION ===\n";
$q = "SELECT urgency_level, COUNT(*) as cnt 
      FROM asset_predictions 
      GROUP BY urgency_level 
      ORDER BY FIELD(urgency_level, 'critical', 'high', 'medium', 'low')";
$r = mysqli_query($db_connection, $q);
while($row = mysqli_fetch_assoc($r)) {
    echo strtoupper($row['urgency_level']) . ": " . $row['cnt'] . "\n";
}

echo "\n=== LATEST PREDICTIONS ONLY ===\n";
$q2 = "SELECT 
        SUM(CASE WHEN ap.urgency_level = 'critical' THEN 1 ELSE 0 END) as critical_count,
        SUM(CASE WHEN ap.urgency_level = 'high' THEN 1 ELSE 0 END) as high_count,
        SUM(CASE WHEN ap.urgency_level = 'medium' THEN 1 ELSE 0 END) as medium_count,
        SUM(CASE WHEN ap.urgency_level = 'low' THEN 1 ELSE 0 END) as low_count,
        COUNT(DISTINCT ap.itemd_id) as total_unique
       FROM asset_predictions ap
       INNER JOIN (
           SELECT itemd_id, MAX(prediction_date) as max_date
           FROM asset_predictions
           GROUP BY itemd_id
       ) latest ON ap.itemd_id = latest.itemd_id AND ap.prediction_date = latest.max_date";
$r2 = mysqli_query($db_connection, $q2);
$stats = mysqli_fetch_assoc($r2);
echo "CRITICAL: " . $stats['critical_count'] . "\n";
echo "HIGH: " . $stats['high_count'] . "\n";
echo "MEDIUM: " . $stats['medium_count'] . "\n";
echo "LOW: " . $stats['low_count'] . "\n";
echo "TOTAL UNIQUE: " . $stats['total_unique'] . "\n";

echo "\n=== CHECK DUPLICATES ===\n";
$q3 = "SELECT itemd_id, COUNT(*) as cnt 
       FROM asset_predictions 
       GROUP BY itemd_id 
       HAVING cnt > 1 
       LIMIT 5";
$r3 = mysqli_query($db_connection, $q3);
echo "Assets with multiple predictions: " . mysqli_num_rows($r3) . "\n";
while($row = mysqli_fetch_assoc($r3)) {
    echo "  itemd_id " . $row['itemd_id'] . ": " . $row['cnt'] . " records\n";
}

echo "\n=== CHECK MONTHS DISTRIBUTION ===\n";
$q4 = "SELECT urgency_level, 
       MIN(estimated_months_remaining) as min_months,
       MAX(estimated_months_remaining) as max_months,
       AVG(estimated_months_remaining) as avg_months
       FROM asset_predictions
       GROUP BY urgency_level
       ORDER BY FIELD(urgency_level, 'critical', 'high', 'medium', 'low')";
$r4 = mysqli_query($db_connection, $q4);
while($row = mysqli_fetch_assoc($r4)) {
    echo strtoupper($row['urgency_level']) . ": ";
    echo round($row['min_months'], 1) . " - " . round($row['max_months'], 1);
    echo " (avg: " . round($row['avg_months'], 1) . " months)\n";
}
?>
