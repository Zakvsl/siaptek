<?php
include "../../library/db_connection.php";

// Cari asset Passenger Elevator
$search = "Passenger Elevator 8 Person";
$q = "SELECT id.*, mi.masti_name, ci.cati_name, ci.cati_id
      FROM item_detail id
      INNER JOIN master_item mi ON mi.masti_id = id.masti_id
      INNER JOIN category_item ci ON ci.cati_id = mi.cati_id
      WHERE mi.masti_name LIKE '%$search%'
      LIMIT 1";

$r = mysqli_query($db_connection, $q);
$asset = mysqli_fetch_assoc($r);

if (!$asset) {
    die("Asset tidak ditemukan!\n");
}

$itemd_id = $asset['itemd_id'];
echo "=== ASSET DETAIL ===\n";
echo "Item ID: $itemd_id\n";
echo "Code: {$asset['itemd_code']}\n";
echo "Name: {$asset['masti_name']}\n";
echo "Kategori: {$asset['cati_name']} (ID: {$asset['cati_id']})\n";
echo "Acquired: {$asset['itemd_acquired_date']}\n";
echo "Branch: {$asset['branch_id']}\n";

// Hitung umur
$acquired = new DateTime($asset['itemd_acquired_date']);
$now = new DateTime();
$umur_bulan = $acquired->diff($now)->days / 30;
echo "Umur: " . round($umur_bulan, 1) . " bulan\n\n";

// Hitung 12 fitur yang dipakai ML
echo "=== 12 FITUR UNTUK ML MODEL ===\n\n";

// 1. Umur aset
echo "1. umur_aset_bulan: " . round($umur_bulan, 1) . "\n";

// 2. Kategori ID
echo "2. kategori_id: {$asset['cati_id']}\n";

// 3. Branch ID
echo "3. branch_id: {$asset['branch_id']}\n";

// 4. Frekuensi issuing (6 bulan terakhir)
$q_iss = "SELECT COUNT(DISTINCT ih.issuingh_id) as frekuensi
          FROM issuing_header ih
          INNER JOIN issuing_detail isd ON isd.issuingh_id = ih.issuingh_id
          WHERE isd.itemd_id = $itemd_id
          AND ih.issuingh_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
$r_iss = mysqli_query($db_connection, $q_iss);
$iss = mysqli_fetch_assoc($r_iss);
echo "4. frekuensi_issuing_6bulan: {$iss['frekuensi']}\n";

// 5. Frekuensi return (6 bulan terakhir)
$q_ret = "SELECT COUNT(DISTINCT rh.reth_id) as frekuensi
          FROM return_header rh
          INNER JOIN return_detail rtd ON rtd.reth_id = rh.reth_id
          INNER JOIN issuing_detail isd ON isd.issuingd_id = rtd.issuingd_id
          WHERE isd.itemd_id = $itemd_id
          AND rh.reth_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
$r_ret = mysqli_query($db_connection, $q_ret);
$ret = mysqli_fetch_assoc($r_ret);
echo "5. frekuensi_return_6bulan: {$ret['frekuensi']}\n";

// 6. Average durasi pemakaian
$q_dur = "SELECT AVG(DATEDIFF(rh.reth_date, ih.issuingh_date)) as avg_durasi
          FROM issuing_header ih
          INNER JOIN issuing_detail isd ON isd.issuingh_id = ih.issuingh_id
          INNER JOIN return_header rh ON rh.reth_id = (
              SELECT rtd.reth_id FROM return_detail rtd
              WHERE rtd.issuingd_id = isd.issuingd_id
              LIMIT 1
          )
          WHERE isd.itemd_id = $itemd_id
          AND ih.issuingh_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
$r_dur = mysqli_query($db_connection, $q_dur);
$dur = mysqli_fetch_assoc($r_dur);
$avg_durasi = $dur['avg_durasi'] ?? 0;
echo "6. avg_durasi_pemakaian_hari: " . round($avg_durasi, 1) . "\n";

// 7. Total hari digunakan
$q_total = "SELECT SUM(DATEDIFF(rh.reth_date, ih.issuingh_date)) as total_hari
            FROM issuing_header ih
            INNER JOIN issuing_detail isd ON isd.issuingh_id = ih.issuingh_id
            INNER JOIN return_header rh ON rh.reth_id = (
                SELECT rtd.reth_id FROM return_detail rtd
                WHERE rtd.issuingd_id = isd.issuingd_id
                LIMIT 1
            )
            WHERE isd.itemd_id = $itemd_id
            AND ih.issuingh_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
$r_total = mysqli_query($db_connection, $q_total);
$total = mysqli_fetch_assoc($r_total);
$total_hari = $total['total_hari'] ?? 0;
echo "7. total_hari_digunakan: $total_hari\n";

// 8. Jumlah kerusakan
$q_broken = "SELECT COUNT(*) as jumlah
             FROM broken_header bh
             INNER JOIN broken_detail bd ON bd.brokh_id = bh.brokh_id
             WHERE bd.itemd_id = $itemd_id";
$r_broken = mysqli_query($db_connection, $q_broken);
$broken = mysqli_fetch_assoc($r_broken);
echo "8. jumlah_kerusakan: {$broken['jumlah']}\n";

// 9. Hari sejak kerusakan terakhir
$q_last = "SELECT DATEDIFF(NOW(), MAX(bh.brokh_date)) as hari
           FROM broken_header bh
           INNER JOIN broken_detail bd ON bd.brokh_id = bh.brokh_id
           WHERE bd.itemd_id = $itemd_id";
$r_last = mysqli_query($db_connection, $q_last);
$last = mysqli_fetch_assoc($r_last);
$hari_rusak = $last['hari'] ?? 9999;
echo "9. hari_sejak_kerusakan_terakhir: $hari_rusak\n";

// 10. Pernah diperbaiki
$pernah_diperbaiki = ($broken['jumlah'] > 0) ? 1 : 0;
echo "10. pernah_diperbaiki: $pernah_diperbaiki\n";

// 11. Lama di customer (total hari digunakan)
echo "11. lama_di_customer_hari: $total_hari\n";

// 12. Intensitas penggunaan score
$intensitas = 0;
if ($umur_bulan > 0) {
    $intensitas = ($iss['frekuensi'] / ($umur_bulan / 6)) * 100;
}
echo "12. intensitas_penggunaan_score: " . round($intensitas, 2) . "\n";

echo "\n=== ANALISIS ===\n";
echo "Kenapa TIDAK CRITICAL/HIGH:\n\n";

if ($broken['jumlah'] == 0) {
    echo "❌ TIDAK PERNAH RUSAK (jumlah_kerusakan = 0)\n";
    echo "   → Model melihat asset masih bagus\n\n";
}

if ($iss['frekuensi'] == 0) {
    echo "❌ TIDAK ADA ISSUING 6 bulan terakhir (frekuensi_issuing = 0)\n";
    echo "   → Asset jarang/tidak dipakai\n\n";
}

if ($ret['frekuensi'] == 0) {
    echo "❌ TIDAK ADA RETURN 6 bulan terakhir (frekuensi_return = 0)\n";
    echo "   → Tidak ada history pemakaian\n\n";
}

if ($umur_bulan < 24) {
    echo "⚠ UMUR MASIH MUDA (" . round($umur_bulan/12, 1) . " tahun)\n";
    echo "   → Belum mencapai umur kritis (>3 tahun)\n\n";
}

echo "\n=== UNTUK JADI CRITICAL/HIGH BUTUH: ===\n";
echo "✓ Umur > 36 bulan (3+ tahun)\n";
echo "✓ Jumlah kerusakan > 5 kali\n";
echo "✓ Rusak baru-baru ini (< 30 hari)\n";
echo "✓ Intensitas tinggi (sering dipinjam)\n";
echo "✓ Durasi pemakaian pendek (cepat rusak)\n";

// Cek prediksi yang ada
echo "\n=== PREDIKSI SAAT INI ===\n";
$q_pred = "SELECT * FROM asset_predictions WHERE itemd_id = $itemd_id ORDER BY prediction_date DESC LIMIT 1";
$r_pred = mysqli_query($db_connection, $q_pred);
if ($pred = mysqli_fetch_assoc($r_pred)) {
    echo "Urgency: {$pred['urgency_level']}\n";
    echo "Estimasi bulan: {$pred['estimasi_bulan_penggantian']}\n";
    echo "Confidence: " . round($pred['confidence_score'] * 100, 1) . "%\n";
    echo "Date: {$pred['prediction_date']}\n";
} else {
    echo "Belum ada prediksi\n";
}
