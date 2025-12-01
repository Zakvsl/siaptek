"""
TRAIN MODEL FROM REAL DATA
Script ini training model dari data REAL di database (item_detail, issuing, return, broken)
BUKAN dari synthetic data!
"""

import pymysql
import pandas as pd
import numpy as np
from datetime import datetime
import pickle
from sklearn.ensemble import RandomForestClassifier, RandomForestRegressor
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score, f1_score,
    mean_absolute_error, mean_squared_error, r2_score
)
import json
import os
import warnings
warnings.filterwarnings('ignore')

# Database Configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'siaptek',
    'charset': 'utf8mb4'
}

# Features yang digunakan (12 fitur)
FEATURE_COLUMNS = [
    'umur_aset_bulan',
    'kategori_id',
    'branch_id',
    'frekuensi_issuing_6bulan',
    'frekuensi_return_6bulan',
    'avg_durasi_pemakaian_hari',
    'total_hari_digunakan',
    'jumlah_kerusakan',
    'hari_sejak_kerusakan_terakhir',
    'pernah_diperbaiki',
    'lama_di_customer_hari',
    'intensitas_penggunaan_score'
]

print("=" * 70)
print("TRAINING MODEL FROM REAL DATABASE")
print("=" * 70)
print()

# 1. KONEKSI DATABASE
print(">> Menghubungkan ke database...")
try:
    connection = pymysql.connect(**DB_CONFIG)
    cursor = connection.cursor()
    print("[OK] Koneksi berhasil!")
except Exception as e:
    print(f"[ERROR] Koneksi gagal - {str(e)}")
    exit(1)

# 2. EKSTRAK DATA REAL DARI DATABASE
print("\n>> Mengekstrak data REAL dari database...")
print("   Sumber: item_detail, issuing, return, broken, write_off")

query = """
SELECT 
    id.itemd_id,
    id.branch_id,
    mi.cati_id as kategori_id,
    
    -- Feature 1: Umur aset (dalam bulan)
    TIMESTAMPDIFF(MONTH, id.itemd_acquired_date, NOW()) as umur_aset_bulan,
    
    -- Feature 2-3: Frekuensi issuing dan return (6 bulan terakhir)
    (SELECT COUNT(*) FROM issuing_detail isd 
     JOIN issuing_header ish ON isd.issuingh_id = ish.issuingh_id
     WHERE isd.itemd_id = id.itemd_id 
       AND ish.issuingh_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)) as frekuensi_issuing_6bulan,
    
    (SELECT COUNT(*) FROM issuing_detail isd
     JOIN issuing_header ish ON isd.issuingh_id = ish.issuingh_id
     JOIN return_detail rd ON rd.issuingd_id = isd.issuingd_id
     JOIN return_header rh ON rd.reth_id = rh.reth_id
     WHERE isd.itemd_id = id.itemd_id
       AND rh.reth_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)) as frekuensi_return_6bulan,
    
    -- Feature 4: Rata-rata durasi pemakaian
    COALESCE((SELECT AVG(DATEDIFF(rh.reth_date, ish.issuingh_date))
              FROM issuing_detail isd
              JOIN issuing_header ish ON isd.issuingh_id = ish.issuingh_id
              JOIN return_detail rd ON rd.issuingd_id = isd.issuingd_id
              JOIN return_header rh ON rd.reth_id = rh.reth_id
              WHERE isd.itemd_id = id.itemd_id
                AND rh.reth_date >= ish.issuingh_date
                AND ish.issuingh_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)), 0) as avg_durasi_pemakaian_hari,
    
    -- Feature 5: Total hari digunakan
    COALESCE((SELECT SUM(DATEDIFF(rh.reth_date, ish.issuingh_date))
              FROM issuing_detail isd
              JOIN issuing_header ish ON isd.issuingh_id = ish.issuingh_id
              JOIN return_detail rd ON rd.issuingd_id = isd.issuingd_id
              JOIN return_header rh ON rd.reth_id = rh.reth_id
              WHERE isd.itemd_id = id.itemd_id
                AND rh.reth_date >= ish.issuingh_date), 0) as total_hari_digunakan,
    
    -- Feature 6: Jumlah kerusakan
    (SELECT COUNT(*) FROM broken_detail bd WHERE bd.itemd_id = id.itemd_id) as jumlah_kerusakan,
    
    -- Feature 7: Hari sejak kerusakan terakhir
    COALESCE(DATEDIFF(NOW(), (SELECT MAX(bh.brokh_date) 
                              FROM broken_header bh
                              JOIN broken_detail bd ON bh.brokh_id = bd.brokh_id
                              WHERE bd.itemd_id = id.itemd_id)), 999) as hari_sejak_kerusakan_terakhir,
    
    -- Feature 8: Pernah diperbaiki
    IF((SELECT COUNT(*) FROM broken_detail WHERE itemd_id = id.itemd_id) > 0, 1, 0) as pernah_diperbaiki,
    
    -- Feature 9: Lama di customer (hari)
    COALESCE((SELECT DATEDIFF(NOW(), MAX(ish.issuingh_date))
              FROM issuing_detail isd
              JOIN issuing_header ish ON isd.issuingh_id = ish.issuingh_id
              WHERE isd.itemd_id = id.itemd_id
                AND NOT EXISTS (
                    SELECT 1 FROM return_detail rd
                    JOIN return_header rh ON rd.reth_id = rh.reth_id
                    WHERE rd.issuingd_id = isd.issuingd_id
                      AND rh.reth_date > ish.issuingh_date
                )), 0) as lama_di_customer_hari,
    
    -- TARGET LABELS
    -- Perlu diganti: 1 jika sudah pernah di write-off atau umur > 8 tahun
    CASE 
        WHEN id.itemd_is_wo = '1' THEN 1
        WHEN TIMESTAMPDIFF(MONTH, id.itemd_acquired_date, NOW()) > 96 THEN 1
        WHEN (SELECT COUNT(*) FROM broken_detail WHERE itemd_id = id.itemd_id) >= 3 THEN 1
        ELSE 0
    END as perlu_diganti,
    
    -- Estimasi bulan penggantian (REALISTIC calculation)
    GREATEST(0, 
        -- Base: 120 months expected life - current age
        120 - TIMESTAMPDIFF(MONTH, id.itemd_acquired_date, NOW())
        -- Reduce by breakdowns (each breakdown = 6-15 months degradation)
        - ((SELECT COUNT(*) FROM broken_detail WHERE itemd_id = id.itemd_id) * 10)
        -- High usage penalty (if freq > 5 in 6mo, reduce 20%)
        - CASE WHEN (SELECT COUNT(*) FROM issuing_detail isd 
                     JOIN issuing_header ish ON isd.issuingh_id = ish.issuingh_id
                     WHERE isd.itemd_id = id.itemd_id 
                     AND ish.issuingh_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)) > 5
               THEN 15 ELSE 0 END
    ) as estimasi_bulan_penggantian
    
FROM item_detail id
JOIN master_item mi ON id.masti_id = mi.masti_id
JOIN category_item ci ON mi.cati_id = ci.cati_id
WHERE id.itemd_status = '0'  -- Hanya asset aktif
  AND TIMESTAMPDIFF(MONTH, id.itemd_acquired_date, NOW()) >= 1  -- Min umur 1 bulan
ORDER BY id.itemd_id
"""

try:
    print("   Query: Menghitung 12 features dari multiple tables...")
    df = pd.read_sql(query, connection)
    print(f"[OK] Data berhasil diekstrak: {len(df)} rows")
except Exception as e:
    print(f"[ERROR] Loading data - {str(e)}")
    connection.close()
    exit(1)

connection.close()

# Validasi data
if len(df) == 0:
    print("\n[ERROR] Dataset kosong!")
    print("   Tidak ada asset aktif dengan data transaksi")
    print("   Silakan pastikan ada data di:")
    print("   - item_detail (asset)")
    print("   - issuing_header/detail (transaksi issuing)")
    print("   - return_header/detail (transaksi return)")
    exit(1)

if len(df) < 20:
    print(f"\n[WARNING] Dataset terlalu kecil ({len(df)} rows)")
    print("   Minimal 20 records untuk training yang baik")
    print("   Rekomendasi: Tambah lebih banyak data transaksi atau gunakan synthetic data")

print(f"\n   Total records: {len(df)}")
print(f"   Features: {len(FEATURE_COLUMNS)}")
print()

# 3. CALCULATE INTENSITAS PENGGUNAAN SCORE
print(">> Menghitung intensitas penggunaan score...")
if len(df) > 0:
    max_freq = df['frekuensi_issuing_6bulan'].max() if df['frekuensi_issuing_6bulan'].max() > 0 else 1
    df['intensitas_penggunaan_score'] = (df['frekuensi_issuing_6bulan'] / max_freq).clip(0, 1)
    print("[OK] Intensitas score calculated")
else:
    df['intensitas_penggunaan_score'] = 0

# 4. PREPROCESSING
print("\n>> Preprocessing data...")

# Prepare features & targets
X = df[FEATURE_COLUMNS].values
y_status = df['perlu_diganti'].values
y_bulan = df['estimasi_bulan_penggantian'].values

print(f"   Features shape: {X.shape}")
print(f"   Target status shape: {y_status.shape}")
print(f"   Target bulan shape: {y_bulan.shape}")

# Class distribution
print(f"\n   Class Distribution:")
print(f"   - Tidak perlu diganti (0): {(y_status == 0).sum()} ({(y_status == 0).sum() / len(y_status) * 100:.1f}%)")
print(f"   - Perlu diganti (1):       {(y_status == 1).sum()} ({(y_status == 1).sum() / len(y_status) * 100:.1f}%)")

# 5. SPLIT DATA
print("\n>> Splitting data (80% train, 20% test)...")
X_train, X_test, y_status_train, y_status_test, y_bulan_train, y_bulan_test = train_test_split(
    X, y_status, y_bulan, test_size=0.2, random_state=42, stratify=y_status
)
print(f"   Train set: {len(X_train)} rows")
print(f"   Test set: {len(X_test)} rows")

# 6. TRAINING CLASSIFIER (Status Prediction)
print("\n>> Training Status Classifier (RandomForest)...")
clf = RandomForestClassifier(
    n_estimators=100,
    max_depth=8,          # Reduced from 15 to prevent overfitting
    min_samples_split=10, # Increased from 5
    min_samples_leaf=5,   # Increased from 2
    max_features='sqrt',  # Use sqrt of features
    random_state=42,
    n_jobs=-1
)

clf.fit(X_train, y_status_train)
y_status_pred = clf.predict(X_test)

accuracy = accuracy_score(y_status_test, y_status_pred)
precision = precision_score(y_status_test, y_status_pred, average='weighted', zero_division=0)
recall = recall_score(y_status_test, y_status_pred, average='weighted', zero_division=0)
f1 = f1_score(y_status_test, y_status_pred, average='weighted', zero_division=0)

print(f"[OK] Training selesai!")
print(f"   Accuracy:  {accuracy:.4f} ({accuracy*100:.1f}%)")
print(f"   Precision: {precision:.4f}")
print(f"   Recall:    {recall:.4f}")
print(f"   F1 Score:  {f1:.4f}")

# 7. TRAINING REGRESSOR (Bulan Prediction)
print("\n>> Training Bulan Regressor (RandomForest)...")
reg = RandomForestRegressor(
    n_estimators=100,
    max_depth=8,          # Reduced from 15 to prevent overfitting
    min_samples_split=10, # Increased from 5
    min_samples_leaf=5,   # Increased from 2
    max_features='sqrt',  # Use sqrt of features
    random_state=42,
    n_jobs=-1
)

reg.fit(X_train, y_bulan_train)
y_bulan_pred = reg.predict(X_test)

mae = mean_absolute_error(y_bulan_test, y_bulan_pred)
mse = mean_squared_error(y_bulan_test, y_bulan_pred)
rmse = np.sqrt(mse)
r2 = r2_score(y_bulan_test, y_bulan_pred)

print(f"[OK] Training selesai!")
print(f"   MAE:      {mae:.4f} bulan")
print(f"   RMSE:     {rmse:.4f} bulan")
print(f"   R² Score: {r2:.4f}")

# 8. SAVE MODELS
print("\n>> Menyimpan model...")

# Create models directory if not exists
os.makedirs('models', exist_ok=True)

# Save only as latest (overwrite existing files - no timestamp files)
print("[OK] Saving models (overwriting existing)...")
with open('models/rf_classifier_latest.pkl', 'wb') as f:
    pickle.dump(clf, f)
print("[OK] Classifier saved: models/rf_classifier_latest.pkl")

with open('models/rf_regressor_latest.pkl', 'wb') as f:
    pickle.dump(reg, f)
print("[OK] Regressor saved: models/rf_regressor_latest.pkl")

# 9. SAVE METRICS
print("\n>> Menyimpan metrics...")
timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")

metrics = {
    'timestamp': timestamp,
    'data_source': 'real_database',
    'total_data': len(df),
    'train_size': len(X_train),
    'test_size': len(X_test),
    'features_count': len(FEATURE_COLUMNS),
    'classifier': {
        'accuracy': float(accuracy),
        'precision': float(precision),
        'recall': float(recall),
        'f1_score': float(f1)
    },
    'regressor': {
        'mae': float(mae),
        'rmse': float(rmse),
        'r2_score': float(r2)
    }
}

# Save only as latest (overwrite existing - no timestamp file)
with open('models/metrics_latest.json', 'w') as f:
    json.dump(metrics, f, indent=4)
print("[OK] Metrics saved: models/metrics_latest.json")

# 10. SUMMARY
print("\n" + "=" * 70)
print("TRAINING SELESAI!")
print("=" * 70)
print(f"\nData Source: REAL DATABASE (item_detail, issuing, return, broken)")
print(f"Total Records: {len(df)}")
print(f"Features Used: {len(FEATURE_COLUMNS)}")
print(f"Last Updated: {timestamp}")
print()
print(f"Model Files (overwritten):")
print(f"  - Classifier: models/rf_classifier_latest.pkl")
print(f"  - Regressor:  models/rf_regressor_latest.pkl")
print(f"  - Metrics:    models/metrics_latest.json")
print()
print(f"Performance:")
print(f"  - Classifier Accuracy: {accuracy:.2%}")
print(f"  - Regressor R² Score:  {r2:.4f}")
print(f"  - Regressor MAE:       {mae:.2f} bulan")
print()
print("Model siap digunakan untuk prediksi!")
print("=" * 70)
