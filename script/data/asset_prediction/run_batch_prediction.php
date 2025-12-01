<?php
/**
 * RUN BATCH PREDICTION FOR ALL ASSETS
 * Jalankan prediksi otomatis untuk semua aset
 */

header('Content-Type: application/json');
set_time_limit(0);
ini_set('max_execution_time', 0);

include "../../library/check_session.php";
include "../../library/db_connection.php";

$branch_id = isset($_SESSION['ses_id_branch']) ? $_SESSION['ses_id_branch'] : '00001'; // Default untuk testing

// Ambil semua aset yang belum diprediksi atau prediksi lama (>7 hari)
$query_assets = "
SELECT 
    id.itemd_id,
    id.itemd_code,
    mi.masti_name,
    COALESCE(MAX(ap.prediction_date), '1970-01-01') as last_prediction
FROM item_detail id
INNER JOIN master_item mi ON mi.masti_id = id.masti_id
LEFT JOIN asset_predictions ap ON ap.itemd_id = id.itemd_id
WHERE id.branch_id = '$branch_id'
  AND id.itemd_status = '0'
  AND id.itemd_is_wo = '0'
  AND (id.itemd_is_dispossed IS NULL OR id.itemd_is_dispossed = '0')
GROUP BY id.itemd_id
-- Temporary: force predict all assets (comment out HAVING filter)
-- HAVING DATEDIFF(NOW(), last_prediction) > 7 OR last_prediction = '1970-01-01'
";

$result = mysqli_query($db_connection, $query_assets);
$total_assets = mysqli_num_rows($result);

if ($total_assets == 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Semua aset sudah diprediksi (dalam 7 hari terakhir)',
        'predicted' => 0,
        'failed' => 0
    ]);
    exit;
}

$predicted = 0;
$failed = 0;
$errors = [];

// Path ke predict script
$predict_script = __DIR__ . '/../../data/item_detail/predict_single_asset.php';

while ($asset = mysqli_fetch_assoc($result)) {
    $itemd_id = $asset['itemd_id'];
    
    // Call predict API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/siaptek/inventory/script/data/item_detail/predict_single_asset.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'action' => 'predict',
        'itemd_id' => $itemd_id
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $result_data = json_decode($response, true);
        if (!isset($result_data['error'])) {
            $predicted++;
        } else {
            $failed++;
            $errors[] = "Asset {$asset['itemd_code']}: {$result_data['error']}";
        }
    } else {
        $failed++;
        $errors[] = "Asset {$asset['itemd_code']}: HTTP Error $http_code";
    }
}

echo json_encode([
    'success' => true,
    'message' => "Prediksi selesai: $predicted berhasil, $failed gagal",
    'predicted' => $predicted,
    'failed' => $failed,
    'total' => $total_assets,
    'errors' => $errors
]);

mysqli_close($db_connection);
?>
