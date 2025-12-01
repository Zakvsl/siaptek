<?php
require_once "../../library/db_connection.php";

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Debug Predictions</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#f0f0f0;}table{background:white;border-collapse:collapse;width:100%;}th,td{border:1px solid #ccc;padding:8px;text-align:left;}th{background:#333;color:white;}</style></head><body>";

echo "<h2>🔍 DEBUG ASSET PREDICTIONS</h2><hr>";

// Check total records
$total_query = "SELECT COUNT(*) as total FROM asset_predictions";
$total_result = mysqli_query($db_connection, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
echo "<h3>Total Records in asset_predictions: <strong>{$total_data['total']}</strong></h3>";

// Check by branch
$branch_query = "SELECT branch_id, COUNT(*) as total FROM asset_predictions GROUP BY branch_id";
$branch_result = mysqli_query($db_connection, $branch_query);
echo "<h3>Records by Branch:</h3><table><tr><th>Branch ID</th><th>Count</th></tr>";
while ($row = mysqli_fetch_assoc($branch_result)) {
    echo "<tr><td>{$row['branch_id']}</td><td>{$row['total']}</td></tr>";
}
echo "</table>";

// Check latest 10 predictions
$latest_query = "SELECT ap.*, id.itemd_code 
                 FROM asset_predictions ap
                 LEFT JOIN item_detail id ON ap.itemd_id = id.itemd_id
                 ORDER BY ap.prediction_date DESC 
                 LIMIT 10";
$latest_result = mysqli_query($db_connection, $latest_query);
echo "<h3>Latest 10 Predictions:</h3>";
echo "<table><tr><th>ID</th><th>Branch</th><th>Asset Code</th><th>Urgency</th><th>Estimasi</th><th>Confidence</th><th>Date</th></tr>";
while ($row = mysqli_fetch_assoc($latest_result)) {
    echo "<tr>";
    echo "<td>{$row['itemd_id']}</td>";
    echo "<td>{$row['branch_id']}</td>";
    echo "<td>{$row['itemd_code']}</td>";
    echo "<td>{$row['urgency_level']}</td>";
    echo "<td>{$row['estimated_months_remaining']}</td>";
    echo "<td>" . round($row['confidence_score']*100, 1) . "%</td>";
    echo "<td>{$row['prediction_date']}</td>";
    echo "</tr>";
}
echo "</table>";

// Check dashboard query
$branch_id = 1; // Test with branch 1
$dashboard_query = "SELECT 
                    COUNT(DISTINCT ap.itemd_id) as total_assets,
                    SUM(CASE WHEN ap.urgency_level = 'critical' THEN 1 ELSE 0 END) as critical_count,
                    SUM(CASE WHEN ap.urgency_level = 'high' THEN 1 ELSE 0 END) as high_count,
                    SUM(CASE WHEN ap.urgency_level = 'medium' THEN 1 ELSE 0 END) as medium_count,
                    SUM(CASE WHEN ap.urgency_level = 'low' THEN 1 ELSE 0 END) as low_count,
                    AVG(ap.confidence_score) as avg_confidence,
                    MAX(ap.prediction_date) as last_prediction_date
                  FROM asset_predictions ap
                  WHERE ap.branch_id = '$branch_id'
                    AND ap.prediction_date = (
                        SELECT MAX(prediction_date) 
                        FROM asset_predictions ap2 
                        WHERE ap2.itemd_id = ap.itemd_id
                    )";
$dashboard_result = mysqli_query($db_connection, $dashboard_query);
$dashboard_data = mysqli_fetch_assoc($dashboard_result);

echo "<h3>Dashboard Query Result (Branch 1):</h3>";
echo "<table>";
echo "<tr><th>Metric</th><th>Value</th></tr>";
echo "<tr><td>Total Assets</td><td>{$dashboard_data['total_assets']}</td></tr>";
echo "<tr><td>Critical</td><td>{$dashboard_data['critical_count']}</td></tr>";
echo "<tr><td>High</td><td>{$dashboard_data['high_count']}</td></tr>";
echo "<tr><td>Medium</td><td>{$dashboard_data['medium_count']}</td></tr>";
echo "<tr><td>Low</td><td>{$dashboard_data['low_count']}</td></tr>";
echo "<tr><td>Avg Confidence</td><td>" . round($dashboard_data['avg_confidence']*100, 1) . "%</td></tr>";
echo "<tr><td>Last Prediction</td><td>{$dashboard_data['last_prediction_date']}</td></tr>";
echo "</table>";

// Check urgency level values
$urgency_query = "SELECT urgency_level, COUNT(*) as total FROM asset_predictions GROUP BY urgency_level";
$urgency_result = mysqli_query($db_connection, $urgency_query);
echo "<h3>Urgency Level Distribution:</h3><table><tr><th>Urgency Level</th><th>Count</th></tr>";
while ($row = mysqli_fetch_assoc($urgency_result)) {
    echo "<tr><td>{$row['urgency_level']}</td><td>{$row['total']}</td></tr>";
}
echo "</table>";

echo "<hr><p><a href='dashboard_prediction.php'>← Back to Dashboard</a></p>";
echo "</body></html>";
?>
