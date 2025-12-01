<?php
 include "../library/check_session.php";
 $branch_id = $_SESSION['ses_id_branch'];
 $user_id = $_SESSION['ses_user_id'];
 
 // Save configuration
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $setting_name = mysqli_real_escape_string($db_connection, $_POST['setting_name']);
     $setting_value = mysqli_real_escape_string($db_connection, $_POST['setting_value']);
     
     // Check if exists
     $check_query = "SELECT id FROM asset_prediction_config WHERE branch_id = $branch_id AND setting_name = '$setting_name'";
     $check_result = mysqli_query($db_connection, $check_query);
     
     if (mysqli_num_rows($check_result) > 0) {
         // Update existing
         $update_query = "UPDATE asset_prediction_config 
                          SET setting_value = '$setting_value',
                              updated_at = NOW(),
                              updated_by = $user_id
                          WHERE branch_id = $branch_id AND setting_name = '$setting_name'";
         mysqli_query($db_connection, $update_query);
     } else {
         // Insert new
         $insert_query = "INSERT INTO asset_prediction_config 
                          (branch_id, setting_name, setting_value, created_by, updated_by) 
                          VALUES ($branch_id, '$setting_name', '$setting_value', $user_id, $user_id)";
         mysqli_query($db_connection, $insert_query);
     }
     
     $success_message = "Konfigurasi berhasil disimpan!";
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Konfigurasi Prediksi</title>
<style>
.config-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 20px;
}
.config-section h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #72b626;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.config-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}
.config-item:last-child {
    border-bottom: none;
}
.config-label {
    flex: 1;
    font-size: 14px;
    color: #333;
}
.config-label small {
    display: block;
    color: #999;
    font-size: 12px;
    margin-top: 5px;
}
.config-input {
    flex: 0 0 200px;
    text-align: right;
}
.config-input input[type="number"],
.config-input select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 13px;
}
.config-input .slider-container {
    display: flex;
    align-items: center;
    gap: 10px;
}
.config-input input[type="range"] {
    flex: 1;
}
.config-input .slider-value {
    font-weight: bold;
    color: #72b626;
    min-width: 50px;
}
.save-btn {
    background: #72b626;
    color: #fff;
    border: none;
    padding: 10px 25px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
}
.save-btn:hover {
    background: #5a9120;
}
.success-message {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 12px 20px;
    border-radius: 3px;
    margin-bottom: 20px;
}
.threshold-preview {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 3px;
    margin-top: 15px;
    font-size: 12px;
}
.threshold-preview .level {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 8px 0;
}
.threshold-preview .badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 3px;
    color: #fff;
    font-weight: bold;
    min-width: 70px;
    text-align: center;
}
</style>
</head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  include "../library/library_function.php";
  
  // Load current settings
  $settings = array();
  $query = "SELECT setting_name, setting_value FROM asset_prediction_config WHERE branch_id = $branch_id";
  $result = mysqli_query($db_connection, $query);
  while ($row = mysqli_fetch_assoc($result)) {
      $settings[$row['setting_name']] = $row['setting_value'];
  }
  
  // Default values
  $confidence_threshold = isset($settings['confidence_threshold']) ? $settings['confidence_threshold'] : '0.80';
  $notification_critical = isset($settings['notification_critical']) ? $settings['notification_critical'] : '1';
  $notification_high = isset($settings['notification_high']) ? $settings['notification_high'] : '1';
  $notification_medium = isset($settings['notification_medium']) ? $settings['notification_medium'] : '0';
  $notification_low = isset($settings['notification_low']) ? $settings['notification_low'] : '0';
  $auto_run_enabled = isset($settings['auto_run_enabled']) ? $settings['auto_run_enabled'] : '0';
  $auto_run_schedule = isset($settings['auto_run_schedule']) ? $settings['auto_run_schedule'] : 'daily';
  $max_notification_per_day = isset($settings['max_notification_per_day']) ? $settings['max_notification_per_day'] : '50';
?>

<div style="padding:20px;">
    <!-- Header -->
    <div style="background-color:#f5f5f5; padding:15px; border-radius:5px; margin-bottom:20px;">
        <h2 style="margin:0; color:#333;">Konfigurasi Sistem Prediksi</h2>
        <p style="margin:5px 0 0 0; color:#666;">
            Atur parameter prediksi dan notifikasi untuk cabang <?php echo $_SESSION['ses_branch_name']; ?>
        </p>
    </div>
    
    <?php if (isset($success_message)): ?>
    <div class="success-message">
        ✓ <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    
    <!-- Prediction Threshold Settings -->
    <div class="config-section">
        <h3>⚙️ Parameter Prediksi</h3>
        
        <form method="POST" id="thresholdForm">
            <input type="hidden" name="setting_name" value="confidence_threshold">
            
            <div class="config-item">
                <div class="config-label">
                    <strong>Confidence Threshold</strong>
                    <small>Minimum tingkat kepercayaan untuk klasifikasi "Perlu Diganti"</small>
                </div>
                <div class="config-input">
                    <div class="slider-container">
                        <input type="range" name="setting_value" id="confidenceSlider" 
                               min="0.50" max="1.00" step="0.01" 
                               value="<?php echo $confidence_threshold; ?>"
                               onchange="updateSliderValue(this.value)">
                        <span class="slider-value" id="sliderValue"><?php echo $confidence_threshold; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="threshold-preview">
                <strong>Preview Kategori Urgensi:</strong>
                <div class="level">
                    <span class="badge" style="background:#d32f2f;">Critical</span>
                    <span>Confidence ≥ <span id="criticalValue"><?php echo $confidence_threshold; ?></span> dan Estimasi ≤ 3 bulan</span>
                </div>
                <div class="level">
                    <span class="badge" style="background:#f57c00;">High</span>
                    <span>Confidence ≥ <span id="highValue"><?php echo number_format($confidence_threshold * 0.75, 2); ?></span> dan Estimasi ≤ 6 bulan</span>
                </div>
                <div class="level">
                    <span class="badge" style="background:#fbc02d;">Medium</span>
                    <span>Confidence ≥ <span id="mediumValue"><?php echo number_format($confidence_threshold * 0.50, 2); ?></span> dan Estimasi ≤ 12 bulan</span>
                </div>
                <div class="level">
                    <span class="badge" style="background:#7cb342;">Low</span>
                    <span>Lainnya</span>
                </div>
            </div>
            
            <div style="text-align:right; margin-top:15px;">
                <button type="submit" class="save-btn">💾 Simpan Threshold</button>
            </div>
        </form>
    </div>
    
    <!-- Notification Settings -->
    <div class="config-section">
        <h3>🔔 Pengaturan Notifikasi</h3>
        
        <form method="POST" id="notificationForm" onsubmit="return saveAllNotifications()">
            <div class="config-item">
                <div class="config-label">
                    <strong>Notifikasi Critical</strong>
                    <small>Kirim notifikasi untuk aset dengan urgensi critical</small>
                </div>
                <div class="config-input">
                    <select name="notification_critical" id="notif_critical">
                        <option value="1" <?php echo $notification_critical == '1' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?php echo $notification_critical == '0' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="config-item">
                <div class="config-label">
                    <strong>Notifikasi High</strong>
                    <small>Kirim notifikasi untuk aset dengan urgensi high</small>
                </div>
                <div class="config-input">
                    <select name="notification_high" id="notif_high">
                        <option value="1" <?php echo $notification_high == '1' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?php echo $notification_high == '0' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="config-item">
                <div class="config-label">
                    <strong>Notifikasi Medium</strong>
                    <small>Kirim notifikasi untuk aset dengan urgensi medium</small>
                </div>
                <div class="config-input">
                    <select name="notification_medium" id="notif_medium">
                        <option value="1" <?php echo $notification_medium == '1' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?php echo $notification_medium == '0' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="config-item">
                <div class="config-label">
                    <strong>Notifikasi Low</strong>
                    <small>Kirim notifikasi untuk aset dengan urgensi low</small>
                </div>
                <div class="config-input">
                    <select name="notification_low" id="notif_low">
                        <option value="1" <?php echo $notification_low == '1' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?php echo $notification_low == '0' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="config-item">
                <div class="config-label">
                    <strong>Batas Notifikasi per Hari</strong>
                    <small>Maksimal notifikasi yang dikirim dalam sehari</small>
                </div>
                <div class="config-input">
                    <input type="number" name="max_notification_per_day" id="max_notif" 
                           value="<?php echo $max_notification_per_day; ?>" min="10" max="500">
                </div>
            </div>
            
            <div style="text-align:right; margin-top:15px;">
                <button type="submit" class="save-btn">💾 Simpan Notifikasi</button>
            </div>
        </form>
    </div>
    
    <!-- Auto Run Settings -->
    <div class="config-section">
        <h3>🤖 Prediksi Otomatis</h3>
        
        <form method="POST" id="autoRunForm" onsubmit="return saveAllAutoRun()">
            <div class="config-item">
                <div class="config-label">
                    <strong>Aktifkan Prediksi Otomatis</strong>
                    <small>Jalankan prediksi secara otomatis sesuai jadwal</small>
                </div>
                <div class="config-input">
                    <select name="auto_run_enabled" id="auto_enabled">
                        <option value="1" <?php echo $auto_run_enabled == '1' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?php echo $auto_run_enabled == '0' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="config-item">
                <div class="config-label">
                    <strong>Jadwal Eksekusi</strong>
                    <small>Frekuensi menjalankan prediksi otomatis</small>
                </div>
                <div class="config-input">
                    <select name="auto_run_schedule" id="auto_schedule">
                        <option value="daily" <?php echo $auto_run_schedule == 'daily' ? 'selected' : ''; ?>>Harian (Setiap Hari)</option>
                        <option value="weekly" <?php echo $auto_run_schedule == 'weekly' ? 'selected' : ''; ?>>Mingguan (Setiap Senin)</option>
                        <option value="monthly" <?php echo $auto_run_schedule == 'monthly' ? 'selected' : ''; ?>>Bulanan (Tanggal 1)</option>
                    </select>
                </div>
            </div>
            
            <div style="background:#fff3cd; border:1px solid #ffc107; padding:12px; border-radius:3px; margin-top:15px; font-size:12px; color:#856404;">
                <strong>⚠️ Catatan:</strong> Fitur auto-run memerlukan konfigurasi Cron Job atau Task Scheduler di server. 
                Hubungi administrator sistem untuk setup.
            </div>
            
            <div style="text-align:right; margin-top:15px;">
                <button type="submit" class="save-btn">💾 Simpan Auto Run</button>
            </div>
        </form>
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

function updateSliderValue(value) {
    document.getElementById('sliderValue').textContent = value;
    document.getElementById('criticalValue').textContent = value;
    document.getElementById('highValue').textContent = (value * 0.75).toFixed(2);
    document.getElementById('mediumValue').textContent = (value * 0.50).toFixed(2);
}

function saveAllNotifications() {
    const settings = [
        {name: 'notification_critical', value: document.getElementById('notif_critical').value},
        {name: 'notification_high', value: document.getElementById('notif_high').value},
        {name: 'notification_medium', value: document.getElementById('notif_medium').value},
        {name: 'notification_low', value: document.getElementById('notif_low').value},
        {name: 'max_notification_per_day', value: document.getElementById('max_notif').value}
    ];
    
    saveBatch(settings);
    return false;
}

function saveAllAutoRun() {
    const settings = [
        {name: 'auto_run_enabled', value: document.getElementById('auto_enabled').value},
        {name: 'auto_run_schedule', value: document.getElementById('auto_schedule').value}
    ];
    
    saveBatch(settings);
    return false;
}

function saveBatch(settings) {
    let completed = 0;
    settings.forEach(setting => {
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'setting_name=' + setting.name + '&setting_value=' + encodeURIComponent(setting.value)
        }).then(() => {
            completed++;
            if (completed === settings.length) {
                window.location.reload();
            }
        });
    });
}
</script>

</body>
</html>
