#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Real-Time Asset Prediction Service
Prediksi maintenance asset secara real-time untuk integrasi dengan web PHP
"""

import sys
import json
import pickle
import pandas as pd
import numpy as np
from pathlib import Path
import warnings
warnings.filterwarnings('ignore')

class AssetPredictor:
    """Handler untuk prediksi asset maintenance real-time"""
    
    def __init__(self):
        self.models_dir = Path(__file__).parent / 'models'
        self.classifier = None
        self.regressor = None
        self.feature_cols = [
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
    
    def load_models(self):
        """Load model classifier dan regressor terbaru"""
        try:
            # Cari file model terbaru
            classifier_files = sorted(self.models_dir.glob('rf_classifier_*.pkl'), reverse=True)
            regressor_files = sorted(self.models_dir.glob('rf_regressor_*.pkl'), reverse=True)
            
            if not classifier_files or not regressor_files:
                return False, "Model file tidak ditemukan. Jalankan training dulu!"
            
            # Load classifier
            with open(classifier_files[0], 'rb') as f:
                self.classifier = pickle.load(f)
            
            # Load regressor
            with open(regressor_files[0], 'rb') as f:
                self.regressor = pickle.load(f)
            
            return True, f"Model loaded: {classifier_files[0].name}"
        
        except Exception as e:
            return False, f"Error loading models: {str(e)}"
    
    def validate_input(self, data):
        """Validasi input data dari PHP"""
        errors = []
        
        # Cek semua fitur yang dibutuhkan
        for col in self.feature_cols:
            if col not in data:
                errors.append(f"Missing feature: {col}")
        
        if errors:
            return False, errors
        
        # Validasi tipe data
        for col in self.feature_cols:
            try:
                float(data[col])
            except (ValueError, TypeError):
                errors.append(f"Invalid value for {col}: {data[col]}")
        
        return len(errors) == 0, errors
    
    def predict_single(self, asset_data):
        """
        Prediksi untuk 1 asset
        
        Args:
            asset_data: dict dengan key sesuai feature_cols
        
        Returns:
            dict hasil prediksi
        """
        try:
            # Load model kalau belum
            if self.classifier is None or self.regressor is None:
                success, msg = self.load_models()
                if not success:
                    return {'error': msg}
            
            # Validasi input
            valid, errors = self.validate_input(asset_data)
            if not valid:
                return {'error': 'Invalid input', 'details': errors}
            
            # Buat DataFrame untuk prediksi
            df_input = pd.DataFrame([asset_data], columns=self.feature_cols)
            
            # Prediksi klasifikasi (perlu diganti atau tidak)
            pred_class = self.classifier.predict(df_input)[0]
            pred_proba = self.classifier.predict_proba(df_input)[0]
            
            # Prediksi regresi (estimasi bulan penggantian)
            pred_months = self.regressor.predict(df_input)[0]
            
            # Hitung confidence score
            confidence = float(pred_proba[pred_class]) * 100
            
            # Tentukan kategori risiko
            if pred_class == 1:  # Perlu diganti
                if pred_months <= 12:
                    risk_level = "CRITICAL"
                    risk_color = "#d32f2f"
                elif pred_months <= 18:
                    risk_level = "HIGH"
                    risk_color = "#f57c00"
                elif pred_months <= 30:
                    risk_level = "MEDIUM"
                    risk_color = "#fbc02d"
                else:
                    risk_level = "LOW"
                    risk_color = "#7cb342"
            else:  # Tidak perlu
                risk_level = "LOW"
                risk_color = "#388e3c"
            
            return {
                'success': True,
                'needs_replacement': int(pred_class),  # For PHP compatibility
                'perlu_diganti': int(pred_class),
                'perlu_diganti_label': 'Ya' if pred_class == 1 else 'Tidak',
                'estimated_months_remaining': round(float(pred_months), 1),  # For PHP compatibility
                'estimasi_bulan': round(float(pred_months), 1),
                'confidence': round(confidence / 100, 4),  # 0-1 scale for database
                'confidence_percent': round(confidence, 2),
                'confidence_label': f"{round(confidence, 1)}%",
                'risk_level': risk_level.lower(),  # lowercase for ENUM
                'risk_level_display': risk_level,
                'risk_color': risk_color,
                'proba_tidak': round(float(pred_proba[0]) * 100, 2),
                'proba_ya': round(float(pred_proba[1]) * 100, 2)
            }
        
        except Exception as e:
            return {'error': str(e)}
    
    def predict_batch(self, assets_list):
        """
        Prediksi untuk banyak asset sekaligus
        
        Args:
            assets_list: list of dict, setiap dict = 1 asset
        
        Returns:
            list of dict hasil prediksi
        """
        results = []
        for i, asset_data in enumerate(assets_list):
            result = self.predict_single(asset_data)
            result['index'] = i
            if 'itemd_id' in asset_data:
                result['itemd_id'] = asset_data['itemd_id']
            results.append(result)
        
        return results


def main():
    """Main function untuk CLI usage dari PHP"""
    
    if len(sys.argv) < 2:
        print(json.dumps({
            'error': 'Usage: python real_time_predict.py <mode> <data>',
            'modes': ['single', 'batch', 'test']
        }))
        sys.exit(1)
    
    mode = sys.argv[1]
    predictor = AssetPredictor()
    
    if mode == 'test':
        # Test mode - cek apakah model bisa di-load
        success, msg = predictor.load_models()
        print(json.dumps({
            'success': success,
            'message': msg,
            'features_required': predictor.feature_cols
        }))
    
    elif mode == 'single':
        # Single prediction mode
        if len(sys.argv) < 3:
            print(json.dumps({'error': 'Data JSON required for single mode'}))
            sys.exit(1)
        
        try:
            data = json.loads(sys.argv[2])
            result = predictor.predict_single(data)
            print(json.dumps(result))
        except json.JSONDecodeError as e:
            print(json.dumps({'error': f'Invalid JSON: {str(e)}'}))
    
    elif mode == 'batch':
        # Batch prediction mode
        if len(sys.argv) < 3:
            print(json.dumps({'error': 'Data JSON array required for batch mode'}))
            sys.exit(1)
        
        try:
            data_list = json.loads(sys.argv[2])
            results = predictor.predict_batch(data_list)
            print(json.dumps({'results': results, 'count': len(results)}))
        except json.JSONDecodeError as e:
            print(json.dumps({'error': f'Invalid JSON: {str(e)}'}))
    
    else:
        print(json.dumps({'error': f'Unknown mode: {mode}'}))
        sys.exit(1)


if __name__ == '__main__':
    main()
