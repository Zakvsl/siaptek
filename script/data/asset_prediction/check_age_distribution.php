<?php
include "../../library/db_connection.php";

echo "=== ASSET AGE DISTRIBUTION ===\n";
$q = "SELECT COUNT(*) as cnt, 
      MIN(TIMESTAMPDIFF(MONTH, itemd_acquired_date, NOW())) as min_age,
      MAX(TIMESTAMPDIFF(MONTH, itemd_acquired_date, NOW())) as max_age,
      AVG(TIMESTAMPDIFF(MONTH, itemd_acquired_date, NOW())) as avg_age
      FROM item_detail 
      WHERE itemd_status='0'";
$r = mysqli_query($db_connection, $q);
$row = mysqli_fetch_assoc($r);
echo "Total aset aktif: " . $row['cnt'] . "\n";
echo "Min age: " . round($row['min_age']) . " bulan (" . round($row['min_age']/12, 1) . " tahun)\n";
echo "Max age: " . round($row['max_age']) . " bulan (" . round($row['max_age']/12, 1) . " tahun)\n";
echo "Avg age: " . round($row['avg_age']) . " bulan (" . round($row['avg_age']/12, 1) . " tahun)\n";

echo "\n=== ASSET AGE BREAKDOWN ===\n";
$q2 = "SELECT 
       SUM(CASE WHEN TIMESTAMPDIFF(MONTH, itemd_acquired_date, NOW()) > 120 THEN 1 ELSE 0 END) as over_10yr,
       SUM(CASE WHEN TIMESTAMPDIFF(MONTH, itemd_acquired_date, NOW()) BETWEEN 60 AND 120 THEN 1 ELSE 0 END) as between_5_10yr,
       SUM(CASE WHEN TIMESTAMPDIFF(MONTH, itemd_acquired_date, NOW()) BETWEEN 36 AND 60 THEN 1 ELSE 0 END) as between_3_5yr,
       SUM(CASE WHEN TIMESTAMPDIFF(MONTH, itemd_acquired_date, NOW()) < 36 THEN 1 ELSE 0 END) as under_3yr
       FROM item_detail 
       WHERE itemd_status='0'";
$r2 = mysqli_query($db_connection, $q2);
$breakdown = mysqli_fetch_assoc($r2);
echo "> 10 tahun (120 bulan): " . $breakdown['over_10yr'] . " aset\n";
echo "5-10 tahun (60-120 bulan): " . $breakdown['between_5_10yr'] . " aset\n";
echo "3-5 tahun (36-60 bulan): " . $breakdown['between_3_5yr'] . " aset\n";
echo "< 3 tahun (< 36 bulan): " . $breakdown['under_3yr'] . " aset\n";

echo "\n=== PREDICTION MONTHS DISTRIBUTION ===\n";
$q3 = "SELECT ap.urgency_level,
       MIN(ap.estimated_months) as min_est,
       MAX(ap.estimated_months) as max_est,
       AVG(ap.estimated_months) as avg_est,
       COUNT(*) as cnt
       FROM asset_predictions ap
       INNER JOIN (
           SELECT itemd_id, MAX(prediction_date) as max_date
           FROM asset_predictions
           GROUP BY itemd_id
       ) latest ON ap.itemd_id = latest.itemd_id AND ap.prediction_date = latest.max_date
       GROUP BY ap.urgency_level
       ORDER BY FIELD(ap.urgency_level, 'critical', 'high', 'medium', 'low')";
$r3 = mysqli_query($db_connection, $q3);
while($row = mysqli_fetch_assoc($r3)) {
    echo strtoupper($row['urgency_level']) . " (" . $row['cnt'] . " aset): ";
    echo "Estimasi " . round($row['min_est'], 1) . " - " . round($row['max_est'], 1) . " bulan";
    echo " (rata-rata " . round($row['avg_est'], 1) . " bulan)\n";
}
?>
