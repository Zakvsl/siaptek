<!DOCTYPE html>
<html>
<head>
    <title>Training Model</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px 5px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .step {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .step h3 {
            margin-top: 0;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Training Model dari Database</h1>
        
        <div class="info-box">
            <strong>📋 WORKFLOW:</strong><br>
            1. Import dataset CSV (500 rows) ke database<br>
            2. Train model dari data di database<br>
            3. Model prediksi pakai data real-time dari database
        </div>

        <div class="step">
            <h3>STEP 1: Import Dataset CSV ke Database</h3>
            <p>Upload file <code>training_data.csv</code> ke database tabel <code>asset_training_data</code></p>
            <a href="import_dataset_to_db.php" class="btn btn-success">► Import Dataset ke Database</a>
        </div>

        <div class="step">
            <h3>STEP 2: Train Model dari Database</h3>
            <p>Jalankan training model menggunakan data dari database</p>
            
            <div class="warning-box">
                <strong>⚠️ REQUIREMENTS:</strong><br>
                - Python 3.x installed<br>
                - Library: pymysql, pandas, scikit-learn, numpy<br>
                - File <code>training_data.csv</code> sudah diimport (Step 1)
            </div>
            
            <p><strong>Cara menjalankan:</strong></p>
            <pre>cd C:\xampp\htdocs\siaptek\inventory\ml_engine
python train_from_database.py</pre>
            
            <p>Atau klik tombol di bawah untuk auto-run dari browser:</p>
            <a href="run_training.php" class="btn">► Train Model dari Database</a>
        </div>

        <div class="step">
            <h3>STEP 3: Test Prediksi</h3>
            <p>Setelah training selesai, test prediksi dengan data real-time:</p>
            <a href="../script/data/asset_prediction/dashboard_prediction.php" class="btn">► Dashboard Prediksi</a>
        </div>

        <hr style="margin: 40px 0;">

        <div class="info-box">
            <strong>📊 PERBEDAAN METODE TRAINING:</strong><br><br>
            
            <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">
                <tr style="background:#007bff; color:white;">
                    <th>Metode</th>
                    <th>Data Source</th>
                    <th>Kelebihan</th>
                </tr>
                <tr>
                    <td><strong>CSV (train_model.py)</strong></td>
                    <td>training_data.csv</td>
                    <td>
                        ✓ Cepat & portable<br>
                        ✓ Tidak perlu database<br>
                        ✓ Cocok untuk development
                    </td>
                </tr>
                <tr>
                    <td><strong>Database (train_from_database.py)</strong></td>
                    <td>Tabel asset_training_data</td>
                    <td>
                        ✓ Data tersentralisasi<br>
                        ✓ Bisa update data via web interface<br>
                        ✓ Cocok untuk production
                    </td>
                </tr>
            </table>
        </div>

        <br>
        <p style="text-align:center; color:#666;">
            <a href="../script/index/index.php" style="color:#007bff; text-decoration:none;">« Kembali ke Dashboard</a>
        </p>
    </div>
</body>
</html>
