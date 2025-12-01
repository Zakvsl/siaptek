<?php
 include "../library/check_session.php";
 $branch_id = $_SESSION['ses_id_branch'];
 $branch_name = $_SESSION['ses_branch_name'];
 $is_super_admin = isset($_SESSION['ses_super_admin']) && $_SESSION['ses_super_admin'] == 'yes';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Jalankan Prediksi</title>
<style>
.prediction-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 20px;
}
.prediction-card h3 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 16px;
    border-bottom: 2px solid #72b626;
    padding-bottom: 10px;
}
.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    margin-right: 10px;
    margin-bottom: 10px;
}
.btn-primary { background: #72b626; color: #fff; }
.btn-secondary { background: #2196F3; color: #fff; }
.btn-warning { background: #FF9800; color: #fff; }
.btn:hover { opacity: 0.9; }
.btn:disabled { opacity: 0.5; cursor: not-allowed; }
.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196F3;
    padding: 15px;
    margin: 15px 0;
    font-size: 13px;
    line-height: 1.6;
}
.warning-box {
    background: #fff3e0;
    border-left: 4px solid #FF9800;
    padding: 15px;
    margin: 15px 0;
    font-size: 13px;
    line-height: 1.6;
}
.success-box {
    background: #e8f5e9;
    border-left: 4px solid #4CAF50;
    padding: 15px;
    margin: 15px 0;
    font-size: 13px;
    line-height: 1.6;
}
.error-box {
    background: #ffebee;
    border-left: 4px solid #f44336;
    padding: 15px;
    margin: 15px 0;
    font-size: 13px;
    line-height: 1.6;
}
#output-console {
    background: #1e1e1e;
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    padding: 15px;
    border-radius: 3px;
    max-height: 400px;
    overflow-y: auto;
    white-space: pre-wrap;
    display: none;
    margin-top: 15px;
}
.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #72b626;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-right: 10px;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.stats-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    margin-top: 10px;
}
.stats-table td {
    padding: 8px;
    border-bottom: 1px solid #eee;
}
.stats-table td:first-child {
    color: #666;
    font-weight: normal;
}
.stats-table td:last-child {
    text-align: right;
    font-weight: bold;
}
</style>
</head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  include "../library/library_function.php";
  
  // Get last prediction info
  $query_last = "SELECT MAX(prediction_date) as last_date, COUNT(*) as total_predictions 
                 FROM asset_predictions 
                 WHERE branch_id='$branch_id'";
  $result_last = mysqli_query($db_connection, $query_last);
  $last_info = mysqli_fetch_assoc($result_last);
  
  $last_prediction_date = $last_info['last_date'] ? date('d-m-Y', strtotime($last_info['last_date'])) : 'Belum pernah';
  $total_predictions = $last_info['total_predictions'] ?? 0;
  
  // Get active assets count
  $query_assets = "SELECT COUNT(*) as total FROM item_detail WHERE branch_id='$branch_id' AND itemd_status='0'";
  $result_assets = mysqli_query($db_connection, $query_assets);
  $assets_info = mysqli_fetch_assoc($result_assets);
  $total_active_assets = $assets_info['total'] ?? 0;
  
  // Check if Python and models exist
  $python_available = true; // Will be checked via AJAX
  $models_exist = file_exists("../../../ml_engine/models/asset_classifier_v1.pkl");
?>

<div style="padding:20px;">
    <!-- Header -->
    <div style="background-color:#f5f5f5; padding:15px; border-radius:5px; margin-bottom:20px;">
        <h2 style="margin:0; color:#333;">Jalankan Prediksi Aset</h2>
        <p style="margin:5px 0 0 0; color:#666;">
            Cabang: <strong><?php echo $branch_name; ?></strong>
        </p>
    </div>
    
    <!-- System Status -->
    <div class="prediction-card">
        <h3>📊 Status Sistem</h3>
        <table class="stats-table">
            <tr>
                <td>Total Aset Aktif:</td>
                <td><?php echo number_format($total_active_assets); ?> aset</td>
            </tr>
            <tr>
                <td>Prediksi Terakhir:</td>
                <td><?php echo $last_prediction_date; ?></td>
            </tr>
            <tr>
                <td>Total Prediksi Tersimpan:</td>
                <td><?php echo number_format($total_predictions); ?> records</td>
            </tr>
            <tr>
                <td>Status Model ML:</td>
                <td><?php echo $models_exist ? '✓ Ready' : '✗ Not Trained'; ?></td>
            </tr>
        </table>
    </div>
    
    <?php if (!$models_exist): ?>
    <div class="warning-box">
        <strong>⚠️ Model Belum Di-Training</strong><br>
        Model machine learning belum tersedia. Silakan training model terlebih dahulu:
        <ol style="margin:10px 0 0 20px;">
            <li>Ekstrak training data: <code>extract_training_data.php</code></li>
            <li>Jalankan training: <code>python train_model.py</code></li>
        </ol>
    </div>
    <?php else: ?>
    
    <!-- Prediction Options -->
    <div class="prediction-card">
        <h3>🎯 Opsi Prediksi</h3>
        
        <div class="info-box">
            <strong>ℹ️ Tentang Batch Prediction</strong><br>
            Proses ini akan menjalankan prediksi untuk semua aset aktif di cabang Anda menggunakan model Random Forest.
            Estimasi waktu: ~30 detik per 1000 aset.
        </div>
        
        <div style="margin-top:20px;">
            <button class="btn btn-primary" onclick="runPrediction('<?php echo $branch_id; ?>')">
                🔄 Jalankan Prediksi untuk <?php echo $branch_name; ?>
            </button>
            
            <?php if ($is_super_admin): ?>
            <button class="btn btn-secondary" onclick="runPrediction('all')">
                🌐 Jalankan Prediksi untuk Semua Cabang
            </button>
            <?php endif; ?>
            
            <button class="btn btn-warning" onclick="showTrainingInfo()">
                🧠 Info Training Model
            </button>
        </div>
        
        <div id="output-console"></div>
        
        <div id="result-summary" style="display:none; margin-top:20px;"></div>
    </div>
    
    <?php endif; ?>
    
    <!-- Instructions -->
    <div class="prediction-card">
        <h3>📖 Panduan Penggunaan</h3>
        
        <p style="font-size:13px; line-height:1.8; color:#555;">
            <strong>Langkah-langkah menjalankan prediksi:</strong>
        </p>
        
        <ol style="font-size:13px; line-height:1.8; color:#555; margin:10px 0 0 20px;">
            <li><strong>Ekstrak Training Data:</strong> Jalankan script ekstraksi untuk mengumpulkan data historis aset
                <br><code style="background:#f5f5f5; padding:2px 6px; border-radius:3px;">
                    http://localhost/siaptek/inventory/script/ml_engine/extract_training_data.php
                </code>
            </li>
            
            <li style="margin-top:10px;"><strong>Training Model:</strong> Jalankan Python script untuk melatih model
                <br><code style="background:#f5f5f5; padding:2px 6px; border-radius:3px;">
                    cd c:\xampp\htdocs\siaptek\inventory\ml_engine<br>
                    python train_model.py
                </code>
            </li>
            
            <li style="margin-top:10px;"><strong>Batch Prediction:</strong> Klik tombol "Jalankan Prediksi" di atas untuk memprediksi semua aset aktif</li>
            
            <li style="margin-top:10px;"><strong>Review Hasil:</strong> Lihat hasil prediksi di halaman 
                <a href="?page=prediction-results" style="color:#72b626; text-decoration:none;">Hasil Prediksi »</a>
            </li>
        </ol>
        
        <div class="info-box" style="margin-top:15px;">
            <strong>💡 Tips:</strong>
            <ul style="margin:5px 0 0 20px;">
                <li>Jalankan prediksi secara berkala (weekly/monthly) untuk update status aset</li>
                <li>Setelah prediksi, sistem akan otomatis mengirim notifikasi untuk aset prioritas tinggi</li>
                <li>Review notifikasi di menu <strong>Prediksi Aset → Notifikasi</strong></li>
            </ul>
        </div>
    </div>
    
    <!-- Back Button -->
    <div style="margin-top:20px;">
        <a href="?page=prediction-dashboard" style="color:#72b626; text-decoration:none; font-size:14px;">
            « Kembali ke Dashboard
        </a>
    </div>
</div>

<script language="javascript">
function disable_parent_window() {}

function runPrediction(branchId) {
    const console_div = document.getElementById('output-console');
    const result_div = document.getElementById('result-summary');
    
    // Show console
    console_div.style.display = 'block';
    console_div.innerHTML = '<div class="spinner"></div> Starting prediction...\n';
    result_div.style.display = 'none';
    
    // Disable buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => btn.disabled = true);
    
    // Prepare form data
    const formData = new FormData();
    if (branchId && branchId !== 'all') {
        formData.append('branch_id', branchId);
    }
    formData.append('action', 'predict');
    
    // Call PHP backend
    fetch('execute_prediction.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            console_div.innerHTML = data.output || 'No output';
            
            if (data.success) {
                const summary = data.summary || {};
                const urgency = summary.urgency_breakdown || {};
                
                result_div.style.display = 'block';
                result_div.innerHTML = `
                    <div class="success-box">
                        <strong>✓ Prediksi Berhasil!</strong><br><br>
                        <strong>Summary:</strong><br>
                        - Total aset diprediksi: ${summary.predictions_saved || 0}<br>
                        - Critical: ${urgency.critical || 0} aset<br>
                        - High: ${urgency.high || 0} aset<br>
                        - Medium: ${urgency.medium || 0} aset<br>
                        - Low: ${urgency.low || 0} aset<br>
                        - Notifikasi: ${summary.notifications_generated || 0}<br><br>
                        <a href="?page=prediction-results" style="color:#72b626; font-weight:bold;">
                            Lihat Hasil Lengkap »
                        </a>
                    </div>
                `;
            } else {
                result_div.style.display = 'block';
                result_div.innerHTML = `
                    <div class="error-box">
                        <strong>✗ Prediksi Gagal</strong><br>
                        ${data.message || 'Unknown error occurred'}
                    </div>
                `;
            }
            
            // Re-enable buttons
            buttons.forEach(btn => btn.disabled = false);
        })
        .catch(error => {
            console_div.innerHTML += '\n\n✗ ERROR: ' + error.message;
            result_div.style.display = 'block';
            result_div.innerHTML = `
                <div class="error-box">
                    <strong>✗ Connection Error</strong><br>
                    Could not connect to prediction service.
                </div>
            `;
            buttons.forEach(btn => btn.disabled = false);
        });
}

function showTrainingInfo() {
    alert('Training Model Info:\n\n' +
          '1. Extract training data first\n' +
          '2. Run: python train_model.py\n' +
          '3. Check metrics in ml_engine/models/metrics_v1.json\n\n' +
          'Recommended: Retrain monthly or when data changes significantly.');
}
</script>

</body>
</html>
