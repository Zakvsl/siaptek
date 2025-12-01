<?php
/**
 * RETRAIN MODEL - AJAX ENDPOINT
 * Training model langsung dari dashboard
 */

header('Content-Type: application/json');
set_time_limit(0);
ini_set('max_execution_time', 0);
error_reporting(0); // Suppress errors in output

include "../../library/db_connection.php";

// CEK apakah ada data item_detail yang aktif
$check_query = "SELECT COUNT(*) as total FROM item_detail WHERE itemd_status = '0'";
$check_result = mysqli_query($db_connection, $check_query);
$check_data = mysqli_fetch_assoc($check_result);

if ($check_data['total'] == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Belum ada data asset aktif di database!'
    ]);
    exit;
}

// Path ke Python script
$python_script = __DIR__ . '/../../../ml_engine/train_from_database.py';

if (!file_exists($python_script)) {
    echo json_encode([
        'success' => false,
        'message' => 'Script training tidak ditemukan!'
    ]);
    exit;
}

// Jalankan training
chdir(__DIR__ . '/../../../ml_engine');
$output = shell_exec("python train_from_database.py 2>&1");

// Check if training completed
if ($output && strpos($output, 'TRAINING SELESAI') !== false) {
    // Baca metrics dari JSON
    $metrics_file = __DIR__ . '/../../../ml_engine/models/metrics_latest.json';
    
    if (file_exists($metrics_file)) {
        $metrics_data = json_decode(file_get_contents($metrics_file), true);
        
        echo json_encode([
            'success' => true,
            'message' => 'Training berhasil!',
            'metrics' => [
                'accuracy' => $metrics_data['classifier']['accuracy'] ?? 0,
                'r2_score' => $metrics_data['regressor']['r2_score'] ?? 0,
                'mae' => $metrics_data['regressor']['mae'] ?? 0
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Training selesai tapi metrics tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Training gagal!',
        'error' => substr($output, 0, 500)
    ]);
}
?>


