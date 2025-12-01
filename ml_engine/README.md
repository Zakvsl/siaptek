# ASSET PREDICTION SYSTEM - ML ENGINE

## 📁 Directory Structure

```
ml_engine/
├── train_model.py              # Python script untuk training model
├── predict_batch.py            # Python script untuk batch prediction (TODO)
├── training_data.csv           # CSV data hasil ekstraksi
├── models/                     # Folder untuk menyimpan trained models
│   ├── asset_classifier_v1.pkl # Random Forest Classifier
│   ├── asset_regressor_v1.pkl  # Random Forest Regressor
│   ├── scaler_v1.pkl           # StandardScaler untuk normalisasi
│   └── metrics_v1.json         # Model performance metrics
└── README.md                   # File ini
```

## 🚀 Getting Started

### 1. Install Python Dependencies

```bash
# Pastikan Python 3.7+ sudah terinstall
python --version

# Install required packages
pip install pandas numpy scikit-learn mysql-connector-python joblib
```

**Packages yang dibutuhkan:**

- `pandas` - Data manipulation
- `numpy` - Numerical operations
- `scikit-learn` - Machine learning (Random Forest)
- `mysql-connector-python` - MySQL database connection
- `joblib` - Model serialization

### 2. Extract Training Data

Jalankan ekstraksi data dari browser:

```
http://localhost/siaptek/inventory/script/ml_engine/extract_training_data.php
```

Script ini akan:

- ✓ Membaca data dari tables: item_detail, issuing, return, broken, write_off
- ✓ Menghitung features untuk setiap aset
- ✓ Menyimpan ke `asset_prediction_dataset` table
- ✓ Export ke CSV: `training_data.csv`

**Minimum requirement:** 500-1000 records untuk model yang viable

### 3. Train Model

Jalankan training script dari command line:

```bash
cd c:\xampp\htdocs\siaptek\inventory\ml_engine
python train_model.py
```

**Training akan:**

1. Load data dari MySQL `asset_prediction_dataset`
2. Preprocessing & cleaning
3. Split data (80% train, 20% test)
4. Train 2 models:
   - RandomForestClassifier (untuk prediksi perlu_diganti)
   - RandomForestRegressor (untuk estimasi bulan penggantian)
5. Evaluate performance
6. Save models ke folder `models/`
7. Save metrics ke `metrics_v1.json` dan database

**Expected output:**

```
=== CLASSIFICATION METRICS ===
Accuracy:  0.85+
Precision: 0.80+
Recall:    0.75+
F1-Score:  0.77+

=== REGRESSION METRICS ===
MAE: <6 months (ideal)
RMSE: <8 months (ideal)
R² Score: >0.60 (good)
```

### 4. Run Batch Prediction

Setelah model trained, jalankan prediksi via PHP:

```
http://localhost/siaptek/inventory/script/index/index.php?page=prediction-run
```

Atau via Python (untuk testing):

```bash
python predict_batch.py --branch_id=1
```

## 📊 Model Architecture

### Features (12 total):

1. `umur_aset_bulan` - Age in months
2. `kategori_id` - Category ID
3. `branch_id` - Branch ID
4. `frekuensi_issuing_6bulan` - Issuing frequency (6 months)
5. `frekuensi_return_6bulan` - Return frequency (6 months)
6. `avg_durasi_pemakaian_hari` - Average usage duration (days)
7. `total_hari_digunakan` - Total days in use
8. `jumlah_kerusakan` - Total breakdowns
9. `hari_sejak_kerusakan_terakhir` - Days since last breakdown
10. `pernah_diperbaiki` - Ever repaired (0/1)
11. `lama_di_customer_hari` - Days currently at customer
12. `intensitas_penggunaan_score` - Usage intensity score (0-1)

### Targets:

- **Classification:** `perlu_diganti` (0=No, 1=Yes)
- **Regression:** `estimasi_bulan_penggantian` (months until replacement)

### Algorithm:

**Random Forest** dipilih karena:

- ✓ Handles non-linear relationships
- ✓ Feature importance built-in
- ✓ Robust to outliers
- ✓ No need for feature scaling (but we do it for consistency)
- ✓ Good for imbalanced data (with class weights)

### Hyperparameters:

```python
n_estimators = 100      # Number of trees
max_depth = 15          # Maximum depth per tree
min_samples_split = 5   # Minimum samples to split node
min_samples_leaf = 2    # Minimum samples in leaf
random_state = 42       # Reproducibility
```

## 🎯 Urgency Level Mapping

Hasil prediksi dikonversi ke urgency level:

| Level    | Condition                                    | Action           |
| -------- | -------------------------------------------- | ---------------- |
| CRITICAL | confidence ≥ 0.80 AND estimation ≤ 3 months  | Replace ASAP     |
| HIGH     | confidence ≥ 0.60 AND estimation ≤ 6 months  | Plan replacement |
| MEDIUM   | confidence ≥ 0.40 AND estimation ≤ 12 months | Monitor closely  |
| LOW      | confidence < 0.40 OR estimation > 12 months  | Routine check    |

## 📈 Model Performance Tracking

Metrics disimpan di:

1. **JSON file:** `models/metrics_v1.json`
2. **Database table:** `asset_prediction_metrics`

Untuk monitoring:

```sql
SELECT * FROM asset_prediction_metrics ORDER BY training_date DESC LIMIT 5;
```

## 🔄 Retraining Schedule

**Recommended retraining frequency:**

- **Monthly:** If data changes significantly (>100 new write-offs)
- **Quarterly:** Standard maintenance
- **On-demand:** When accuracy drops below 75%

**Retraining command:**

```bash
python train_model.py
```

Model version akan di-increment (v1.0 → v1.1 → v2.0)

## 🐛 Troubleshooting

### Error: "No module named 'sklearn'"

```bash
pip install scikit-learn
```

### Error: "Access denied for user 'root'@'localhost'"

Update `DB_CONFIG` di `train_model.py` dengan kredensial yang benar.

### Error: "No data available for training"

Jalankan `extract_training_data.php` terlebih dahulu.

### Warning: "Only X samples available"

Minimum 500 samples diperlukan. Jika kurang:

- Tunggu lebih banyak data historis
- Atau gunakan synthetic data generation (SMOTE)

### Model accuracy < 70%

Possible causes:

- Insufficient data
- Class imbalance too extreme
- Features tidak informatif
- Perlu feature engineering tambahan

**Solutions:**

- Collect more historical data
- Adjust class weights
- Add more features (e.g., seasonal patterns)
- Try different algorithms (XGBoost, LightGBM)

## 📝 Change Log

### v1.0 (2025-10-29)

- ✓ Initial model architecture
- ✓ Dual-target prediction (classification + regression)
- ✓ Feature extraction from 5 data sources
- ✓ Random Forest implementation
- ✓ Metrics tracking in database

### Future Enhancements:

- [ ] SMOTE for class balancing
- [ ] Hyperparameter tuning with GridSearchCV
- [ ] Ensemble with XGBoost
- [ ] Time-series features (seasonal patterns)
- [ ] Automated retraining pipeline
- [ ] A/B testing for model comparison

## 🤝 Support

Untuk bantuan atau pertanyaan:

- Check logs di `ml_engine/training.log`
- Review metrics di `models/metrics_v1.json`
- Contact: SIAPTEK Development Team

---

**Last Updated:** 2025-10-29
