<?php
/**
 * RUN TRAINING SCRIPT FROM WEB
 */

// Set timeout unlimited karena training bisa lama
set_time_limit(0);
ini_set('max_execution_time', 0);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Training Progress</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }
        .terminal {
            background: #0c0c0c;
            border: 2px solid #333;
            border-radius: 5px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .output {
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.5;
        }
        h2 {
            color: #4ec9b0;
            margin: 0 0 20px 0;
        }
        .success {
            color: #4ec9b0;
        }
        .error {
            color: #f48771;
        }
        .warning {
            color: #dcdcaa;
        }
    </style>
</head>
<body>
    <div class="terminal">
        <h2>🤖 Training Model dari Database</h2>
        <div class="output">
<?php

// Cek apakah Python installed
$python_check = shell_exec('python --version 2>&1');
if (strpos($python_check, 'Python') === false) {
    echo "<span class='error'>ERROR: Python tidak terdeteksi!</span>\n";
    echo "Install Python terlebih dahulu: https://www.python.org/downloads/\n";
    exit;
}

echo "<span class='success'>✓ Python detected: " . trim($python_check) . "</span>\n\n";

// Path ke script Python
$script_path = __DIR__ . '/train_from_database.py';

if (!file_exists($script_path)) {
    echo "<span class='error'>ERROR: File train_from_database.py tidak ditemukan!</span>\n";
    exit;
}

echo "Script: $script_path\n";
echo str_repeat("=", 70) . "\n\n";

// Jalankan Python script
$command = "python \"$script_path\" 2>&1";
$descriptorspec = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w")
);

$process = proc_open($command, $descriptorspec, $pipes);

if (is_resource($process)) {
    // Output real-time
    while ($line = fgets($pipes[1])) {
        echo htmlspecialchars($line);
        flush();
        ob_flush();
    }
    
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    
    $return_value = proc_close($process);
    
    echo "\n" . str_repeat("=", 70) . "\n";
    
    if ($return_value === 0) {
        echo "<span class='success'>✓ TRAINING SELESAI!</span>\n\n";
        echo "Model sudah siap digunakan untuk prediksi.\n";
        echo "\n<a href='train_from_database.php' style='color:#4ec9b0;'>« Kembali</a>";
        echo " | ";
        echo "<a href='../script/data/asset_prediction/dashboard_prediction.php' style='color:#4ec9b0;'>Dashboard Prediksi »</a>";
    } else {
        echo "<span class='error'>✗ TRAINING GAGAL! (Exit Code: $return_value)</span>\n";
        echo "Periksa error di atas.\n";
    }
} else {
    echo "<span class='error'>ERROR: Gagal menjalankan Python script</span>\n";
}

?>
        </div>
    </div>
</body>
</html>
