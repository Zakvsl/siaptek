<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $branch_name=$_SESSION['ses_branch_name'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Dashboard Prediksi Aset</title>
<style>
#loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}
#loading-overlay .spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #72b626;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
</head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<div id="loading-overlay">
    <div class="spinner"></div>
    <p style="margin-top: 20px; color: #666;">Loading dashboard...</p>
</div>
<script>
window.addEventListener('load', function() {
    document.getElementById('loading-overlay').style.display = 'none';
});
</script>
<?php
  include "../../library/style.css";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  
  $current_date = date('d-m-Y');
  
  // Get branch config
  $query_config = "SELECT * FROM asset_prediction_config WHERE branch_id='$branch_id'";
  $result_config = mysqli_query($db_connection, $query_config);
  $config = mysqli_fetch_assoc($result_config);
  
  if (!$config) {
      // Insert default config if not exists
      $insert_config = "INSERT INTO asset_prediction_config 
                        (branch_id, model_type, threshold_critical, threshold_high, threshold_medium, 
                         notification_days_before, auto_run_prediction, run_frequency)
                        VALUES ('$branch_id', 'unified', 0.80, 0.60, 0.40, 30, 1, 'daily')";
      mysqli_query($db_connection, $insert_config);
      $config = array(
          'last_training_date' => null,
          'model_version' => 'v1.0'
      );
  }
  
  // Get prediction statistics - OPTIMIZED QUERY
  $query_stats = "SELECT 
                    COUNT(DISTINCT ap.itemd_id) as total_assets,
                    SUM(CASE WHEN ap.urgency_level = 'critical' THEN 1 ELSE 0 END) as critical_count,
                    SUM(CASE WHEN ap.urgency_level = 'high' THEN 1 ELSE 0 END) as high_count,
                    SUM(CASE WHEN ap.urgency_level = 'medium' THEN 1 ELSE 0 END) as medium_count,
                    SUM(CASE WHEN ap.urgency_level = 'low' THEN 1 ELSE 0 END) as low_count,
                    AVG(ap.confidence_score) as avg_confidence,
                    MAX(ap.prediction_date) as last_prediction_date
                  FROM asset_predictions ap
                  INNER JOIN (
                      SELECT itemd_id, MAX(id) as max_id
                      FROM asset_predictions
                      WHERE branch_id = '$branch_id'
                      GROUP BY itemd_id
                  ) latest ON ap.id = latest.max_id
                  WHERE ap.branch_id = '$branch_id'";
  $result_stats = mysqli_query($db_connection, $query_stats);
  $stats = mysqli_fetch_assoc($result_stats);
  
  // Get unread notifications count
  $query_notif = "SELECT COUNT(*) as unread_count 
                  FROM asset_notifications 
                  WHERE branch_id='$branch_id' 
                    AND recipient_user_id='".$_SESSION['ses_user_id']."' 
                    AND is_read=0";
  $result_notif = mysqli_query($db_connection, $query_notif);
  $notif = mysqli_fetch_assoc($result_notif);
  
  $total_assets = $stats['total_assets'] ?? 0;
  $critical_count = $stats['critical_count'] ?? 0;
  $high_count = $stats['high_count'] ?? 0;
  $medium_count = $stats['medium_count'] ?? 0;
  $low_count = $stats['low_count'] ?? 0;
  $avg_confidence = $stats['avg_confidence'] ?? 0;
  $last_prediction_date = $stats['last_prediction_date'] ?? '-';
  $unread_count = $notif['unread_count'] ?? 0;
  
  // Format dates
  if ($last_prediction_date != '-') {
      $last_prediction_date = date('d-m-Y', strtotime($last_prediction_date));
  }
  if ($config['last_training_date']) {
      $last_training_date = date('d-m-Y', strtotime($config['last_training_date']));
  } else {
      $last_training_date = 'Belum pernah training';
  }
?>

<div style="padding:20px;">
    <!-- Header -->
    <div style="background-color:#f5f5f5; padding:15px; border-radius:5px; margin-bottom:20px;">
        <h2 style="margin:0; color:#333;">Dashboard Prediksi Umur Aset</h2>
        <p style="margin:5px 0 0 0; color:#666;">
            Cabang: <strong><?php echo $branch_name; ?></strong> | 
            Update Terakhir: <strong><?php echo $last_prediction_date; ?></strong>
        </p>
    </div>
    
    <!-- Summary Cards -->
    <div style="display:flex; gap:15px; margin-bottom:25px; flex-wrap:wrap;">
        <!-- Total Assets Card -->
        <div style="flex:1; min-width:200px; background:#fff; border:1px solid #ddd; border-radius:5px; padding:15px;">
            <div style="color:#666; font-size:12px; margin-bottom:5px;">Total Aset Terprediksi</div>
            <div style="font-size:32px; font-weight:bold; color:#333;"><?php echo number_format($total_assets); ?></div>
            <div style="color:#999; font-size:11px; margin-top:5px;">Dari semua kategori</div>
        </div>
        
        <!-- Critical Card -->
        <div style="flex:1; min-width:200px; background:#fff; border-left:4px solid #d32f2f; border:1px solid #ddd; border-radius:5px; padding:15px;">
            <div style="color:#666; font-size:12px; margin-bottom:5px;">Status CRITICAL</div>
            <div style="font-size:32px; font-weight:bold; color:#d32f2f;"><?php echo number_format($critical_count); ?></div>
            <div style="color:#999; font-size:11px; margin-top:5px;">Perlu tindakan segera</div>
        </div>
        
        <!-- High Priority Card -->
        <div style="flex:1; min-width:200px; background:#fff; border-left:4px solid #f57c00; border:1px solid #ddd; border-radius:5px; padding:15px;">
            <div style="color:#666; font-size:12px; margin-bottom:5px;">Status HIGH</div>
            <div style="font-size:32px; font-weight:bold; color:#f57c00;"><?php echo number_format($high_count); ?></div>
            <div style="color:#999; font-size:11px; margin-top:5px;">Perlu perhatian</div>
        </div>
        
        <!-- Medium Priority Card -->
        <div style="flex:1; min-width:200px; background:#fff; border-left:4px solid #fbc02d; border:1px solid #ddd; border-radius:5px; padding:15px;">
            <div style="color:#666; font-size:12px; margin-bottom:5px;">Status MEDIUM</div>
            <div style="font-size:32px; font-weight:bold; color:#fbc02d;"><?php echo number_format($medium_count); ?></div>
            <div style="color:#999; font-size:11px; margin-top:5px;">Monitor rutin</div>
        </div>
        
        <!-- Low Priority Card -->
        <div style="flex:1; min-width:200px; background:#fff; border-left:4px solid #7cb342; border:1px solid #ddd; border-radius:5px; padding:15px;">
            <div style="color:#666; font-size:12px; margin-bottom:5px;">Status LOW</div>
            <div style="font-size:32px; font-weight:bold; color:#7cb342;"><?php echo number_format($low_count); ?></div>
            <div style="color:#999; font-size:11px; margin-top:5px;">Kondisi baik</div>
        </div>
    </div>
    
    <!-- Action Buttons & Info -->
    <div style="display:flex; gap:15px; margin-bottom:25px; flex-wrap:wrap;">
        <!-- Actions -->
        <div style="flex:2; min-width:300px; background:#fff; border:1px solid #ddd; border-radius:5px; padding:15px;">
            <h3 style="margin:0 0 15px 0; color:#333; font-size:16px;">Quick Actions</h3>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button onclick="window.location.href='?page=prediction-run'" 
                        style="background:#72b626; color:#fff; border:none; padding:10px 20px; border-radius:3px; cursor:pointer; font-size:13px;">
                    🔄 Jalankan Prediksi Baru
                </button>
                <button onclick="window.location.href='?page=prediction-results'" 
                        style="background:#2196F3; color:#fff; border:none; padding:10px 20px; border-radius:3px; cursor:pointer; font-size:13px;">
                    📊 Lihat Hasil Detail
                </button>
                <button onclick="window.location.href='?page=prediction-notifications'" 
                        style="background:#FF9800; color:#fff; border:none; padding:10px 20px; border-radius:3px; cursor:pointer; font-size:13px; position:relative;">
                    🔔 Notifikasi 
                    <?php if ($unread_count > 0): ?>
                    <span style="position:absolute; top:-5px; right:-5px; background:#d32f2f; color:#fff; border-radius:10px; padding:2px 6px; font-size:10px;">
                        <?php echo $unread_count; ?>
                    </span>
                    <?php endif; ?>
                </button>
                <button onclick="window.open('../ml_engine/extract_training_data.php', '_blank')" 
                        style="background:#9C27B0; color:#fff; border:none; padding:10px 20px; border-radius:3px; cursor:pointer; font-size:13px;">
                    📥 Dataset CSV
                </button>
                <button onclick="window.open('../../../ml_engine/import_dataset_to_db.php', '_blank')" 
                        style="background:#673AB7; color:#fff; border:none; padding:10px 20px; border-radius:3px; cursor:pointer; font-size:13px;">
                    📤 Import Dataset
                </button>
                <button onclick="retrainModel()" id="btn-retrain"
                        style="background:#E91E63; color:#fff; border:none; padding:10px 20px; border-radius:3px; cursor:pointer; font-size:13px; font-weight:bold;">
                    🔥 Retrain Model
                </button>
                <?php if ($employee_autho != 'N' || isset($_SESSION['ses_super_admin'])): ?>
                <button onclick="window.location.href='?page=prediction-config'" 
                        style="background:#607D8B; color:#fff; border:none; padding:10px 20px; border-radius:3px; cursor:pointer; font-size:13px;">
                    ⚙️ Konfigurasi
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- System Info -->
        <div style="flex:1; min-width:250px; background:#fff; border:1px solid #ddd; border-radius:5px; padding:15px;">
            <h3 style="margin:0 0 15px 0; color:#333; font-size:16px;">Informasi Sistem</h3>
            <table style="width:100%; font-size:12px;">
                <tr>
                    <td style="color:#666; padding:5px 0;">Model Version:</td>
                    <td style="text-align:right; font-weight:bold;"><?php echo $config['model_version']; ?></td>
                </tr>
                <tr>
                    <td style="color:#666; padding:5px 0;">Last Training:</td>
                    <td style="text-align:right; font-weight:bold;"><?php echo $last_training_date; ?></td>
                </tr>
                <tr>
                    <td style="color:#666; padding:5px 0;">Avg Confidence:</td>
                    <td style="text-align:right; font-weight:bold;"><?php echo number_format($avg_confidence * 100, 1); ?>%</td>
                </tr>
                <tr>
                    <td style="color:#666; padding:5px 0;">Auto Prediction:</td>
                    <td style="text-align:right; font-weight:bold;">
                        <?php echo $config['auto_run_prediction'] ? 'Aktif' : 'Non-aktif'; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <!-- Critical Assets List -->
    <?php if ($critical_count > 0 || $high_count > 0): ?>
    <div style="background:#fff; border:1px solid #ddd; border-radius:5px; padding:15px;">
        <h3 style="margin:0 0 15px 0; color:#333; font-size:16px;">
            🔴 Aset yang Memerlukan Perhatian
        </h3>
        
        <?php
        // Get critical and high priority assets
        $query_urgent = "SELECT 
                            ap.id,
                            ap.itemd_id,
                            id.itemd_code,
                            mi.masti_name,
                            ci.cati_name,
                            ap.urgency_level,
                            ap.estimasi_bulan_penggantian,
                            ap.confidence_score,
                            TIMESTAMPDIFF(MONTH, id.itemd_acquired_date, NOW()) as current_age_months,
                            id.itemd_position
                        FROM asset_predictions ap
                        INNER JOIN (
                            SELECT itemd_id, MAX(id) as max_id
                            FROM asset_predictions
                            WHERE branch_id = '$branch_id' AND urgency_level IN ('critical', 'high')
                            GROUP BY itemd_id
                        ) latest ON ap.id = latest.max_id
                        JOIN item_detail id ON ap.itemd_id = id.itemd_id
                        JOIN master_item mi ON id.masti_id = mi.masti_id
                        JOIN category_item ci ON mi.cati_id = ci.cati_id
                        WHERE ap.branch_id = '$branch_id'
                          AND ap.urgency_level IN ('critical', 'high')
                          AND id.itemd_status = '0'
                        ORDER BY 
                            FIELD(ap.urgency_level, 'critical', 'high'),
                            ap.confidence_score DESC
                        LIMIT 10";
        $result_urgent = mysqli_query($db_connection, $query_urgent);
        ?>
        
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="background:#f5f5f5; border-bottom:2px solid #ddd;">
                    <th style="padding:10px; text-align:left;">Kode Aset</th>
                    <th style="padding:10px; text-align:left;">Deskripsi</th>
                    <th style="padding:10px; text-align:left;">Kategori</th>
                    <th style="padding:10px; text-align:center;">Umur (Bulan)</th>
                    <th style="padding:10px; text-align:center;">Est. Penggantian</th>
                    <th style="padding:10px; text-align:center;">Confidence</th>
                    <th style="padding:10px; text-align:center;">Status</th>
                    <th style="padding:10px; text-align:center;">Posisi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result_urgent) > 0) {
                    while ($row = mysqli_fetch_assoc($result_urgent)) {
                        $urgency_color = ($row['urgency_level'] == 'critical') ? '#d32f2f' : '#f57c00';
                        $urgency_text = ($row['urgency_level'] == 'critical') ? 'CRITICAL' : 'HIGH';
                        $confidence_pct = number_format($row['confidence_score'] * 100, 1);
                        
                        echo "<tr style='border-bottom:1px solid #eee;'>";
                        echo "<td style='padding:10px;'><strong>".$row['itemd_code']."</strong></td>";
                        echo "<td style='padding:10px;'>".$row['masti_name']."</td>";
                        echo "<td style='padding:10px;'>".$row['cati_name']."</td>";
                        echo "<td style='padding:10px; text-align:center;'>".$row['current_age_months']."</td>";
                        echo "<td style='padding:10px; text-align:center;'>".$row['estimasi_bulan_penggantian']." bulan</td>";
                        echo "<td style='padding:10px; text-align:center;'>".$confidence_pct."%</td>";
                        echo "<td style='padding:10px; text-align:center;'>
                                <span style='background:".$urgency_color."; color:#fff; padding:3px 8px; border-radius:3px; font-size:11px; font-weight:bold;'>
                                    ".$urgency_text."
                                </span>
                              </td>";
                        echo "<td style='padding:10px; text-align:center;'>".$row['itemd_position']."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='padding:20px; text-align:center; color:#999;'>Tidak ada aset dengan prioritas tinggi</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <?php if (mysqli_num_rows($result_urgent) == 10): ?>
        <div style="text-align:center; margin-top:15px;">
            <a href="?page=prediction-results" style="color:#72b626; text-decoration:none; font-size:13px;">
                Lihat semua aset prioritas tinggi »
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Help Section -->
    <div style="background:#e3f2fd; border:1px solid #90caf9; border-radius:5px; padding:15px; margin-top:20px;">
        <strong style="color:#1976d2;">ℹ️ Tentang Prediksi Aset</strong>
        <p style="margin:10px 0 0 0; color:#555; font-size:12px; line-height:1.6;">
            Sistem prediksi ini menggunakan algoritma <strong>Random Forest</strong> untuk memprediksi umur aset berdasarkan:
            riwayat pemakaian, frekuensi kerusakan, durasi penggunaan, dan pola maintenance. 
            Status urgency menunjukkan seberapa cepat aset perlu diganti atau diperbaiki.
        </p>
        <ul style="margin:10px 0 0 20px; color:#555; font-size:12px; line-height:1.6;">
            <li><strong style="color:#d32f2f;">CRITICAL</strong>: Perlu penggantian segera (1-3 bulan)</li>
            <li><strong style="color:#f57c00;">HIGH</strong>: Perlu penggantian dalam waktu dekat (3-6 bulan)</li>
            <li><strong style="color:#fbc02d;">MEDIUM</strong>: Monitor secara rutin (6-12 bulan)</li>
            <li><strong style="color:#7cb342;">LOW</strong>: Kondisi baik (&gt;12 bulan)</li>
        </ul>
    </div>
</div>

<script src="../library/development-bundle/jquery-1.8.0.min.js"></script>
<script language="javascript">
function disable_parent_window() {
    // Keep window active
}

// Retrain Model Function
function retrainModel() {
    if (!confirm('RETRAIN MODEL DENGAN DATA REAL\n\n' +
                 'Proses ini akan:\n' +
                 '✓ Ekstrak data REAL dari database\n' +
                 '✓ Hitung 12 features dari histori transaksi\n' +
                 '✓ Training ulang model Random Forest\n' +
                 '✓ Generate metrics & performance report\n\n' +
                 'Waktu: ~2-5 menit\n\n' +
                 'Lanjutkan?')) {
        return;
    }
    
    var btn = document.getElementById('btn-retrain');
    btn.disabled = true;
    btn.innerHTML = '⏳ Training...';
    btn.style.background = '#999';
    
    // Step 1: Retrain Model dengan DATA REAL
    $.ajax({
        url: '../data/asset_prediction/retrain_model.php',
        type: 'POST',
        dataType: 'json',
        timeout: 300000, // 5 menit timeout
        success: function(response) {
            console.log('Retrain Response:', response); // DEBUG
            
            if (response && response.success) {
                var msg = 'TRAINING BERHASIL!\n\n' +
                         'Data Source: REAL DATABASE\n' +
                         'Accuracy: ' + (response.metrics.accuracy * 100).toFixed(1) + '%\n' +
                         'R² Score: ' + response.metrics.r2_score.toFixed(3) + '\n\n' +
                         'Sekarang menjalankan prediksi untuk semua aset...';
                alert(msg);
                
                // Step 2: Run Batch Prediction
                runBatchPrediction();
            } else {
                var errorMsg = response ? response.message : 'No response received';
                var errorDetail = response ? (response.error || 'Unknown error') : 'Empty response';
                alert('Training gagal!\n\n' + errorMsg + '\n\nDetail:\n' + errorDetail);
                console.error('Training failed:', response);
                resetButton();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                readyState: xhr.readyState,
                statusText: xhr.statusText
            });
            
            var msg = 'Error!\n\n' +
                     'Status: ' + status + '\n' +
                     'Error: ' + error + '\n\n' +
                     'Response: ' + (xhr.responseText ? xhr.responseText.substring(0, 200) : 'Empty') + '\n\n' +
                     'Troubleshooting:\n' +
                     '1. Pastikan Python sudah terinstall\n' +
                     '2. Pastikan library terinstall (pip install -r requirements.txt)\n' +
                     '3. Pastikan ada data asset aktif di database\n' +
                     '4. Check console browser (F12) untuk detail error';
            alert(msg);
            resetButton();
        }
    });
}

function runBatchPrediction() {
    var btn = document.getElementById('btn-retrain');
    btn.innerHTML = '⏳ Prediksi Aset...';
    
    $.ajax({
        url: '../data/asset_prediction/run_batch_prediction.php',
        type: 'POST',
        dataType: 'json',
        timeout: 300000,
        success: function(response) {
            if (response.success) {
                alert('✓ SELESAI!\n\nTraining & Prediksi berhasil:\n- ' + response.predicted + ' aset diprediksi\n- ' + response.failed + ' gagal\n\nHalaman akan di-refresh.');
                location.reload();
            } else {
                alert('⚠️ Prediksi batch gagal!\n\n' + response.message);
                resetButton();
            }
        },
        error: function(xhr, status, error) {
            alert('⚠️ Error saat prediksi batch!\n\nModel sudah di-training tapi prediksi gagal.\nSilakan jalankan manual dari Quick Actions.');
            resetButton();
        }
    });
}

function resetButton() {
    var btn = document.getElementById('btn-retrain');
    btn.disabled = false;
    btn.innerHTML = '🔥 Retrain Model';
    btn.style.background = '#E91E63';
}
</script>

</body>
</html>
