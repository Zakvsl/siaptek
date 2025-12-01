<?php
include "../script/library/db_connection.php";

$query = "SELECT 
    id.itemd_id,
    id.branch_id,
    b.branch_name,
    mi.cati_id AS kategori_id,
    ci.cati_name AS kategori_name,
    TIMESTAMPDIFF(MONTH, id.itemd_acquired_date, NOW()) AS umur_aset_bulan,
    
    (SELECT COUNT(DISTINCT ih.issuingh_id)
     FROM issuing_header ih
     INNER JOIN issuing_detail isd ON isd.issuingh_id = ih.issuingh_id
     WHERE isd.itemd_id = id.itemd_id 
     AND ih.issuingh_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)) AS frekuensi_issuing_6bulan,
    
    (SELECT COUNT(DISTINCT rh.reth_id)
     FROM return_header rh
     INNER JOIN return_detail rtd ON rtd.reth_id = rh.reth_id
     INNER JOIN issuing_detail isd ON isd.issuingd_id = rtd.issuingd_id
     WHERE isd.itemd_id = id.itemd_id 
     AND rh.reth_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)) AS frekuensi_return_6bulan,
    
    COALESCE((SELECT AVG(DATEDIFF(rh.reth_date, ih.issuingh_date))
     FROM issuing_header ih
     INNER JOIN issuing_detail isd ON isd.issuingh_id = ih.issuingh_id
     LEFT JOIN return_detail rtd ON rtd.issuingd_id = isd.issuingd_id
     LEFT JOIN return_header rh ON rh.reth_id = rtd.reth_id
     WHERE isd.itemd_id = id.itemd_id
     AND ih.issuingh_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     AND rh.reth_date IS NOT NULL), 0) AS avg_durasi_pemakaian_hari,
    
    COALESCE((SELECT SUM(DATEDIFF(rh.reth_date, ih.issuingh_date))
     FROM issuing_header ih
     INNER JOIN issuing_detail isd ON isd.issuingh_id = ih.issuingh_id
     LEFT JOIN return_detail rtd ON rtd.issuingd_id = isd.issuingd_id
     LEFT JOIN return_header rh ON rh.reth_id = rtd.reth_id
     WHERE isd.itemd_id = id.itemd_id
     AND rh.reth_date IS NOT NULL), 0) AS total_hari_digunakan,
    
    (SELECT COUNT(*)
     FROM broken_header bh
     INNER JOIN broken_detail bd ON bd.brokh_id = bh.brokh_id
     WHERE bd.itemd_id = id.itemd_id) AS jumlah_kerusakan,
    
    COALESCE((SELECT DATEDIFF(NOW(), MAX(brokh_date))
     FROM broken_header bh
     INNER JOIN broken_detail bd ON bd.brokh_id = bh.brokh_id
     WHERE bd.itemd_id = id.itemd_id), 999) AS hari_sejak_kerusakan_terakhir,
    
    CASE WHEN EXISTS(SELECT 1 FROM broken_detail bd WHERE bd.itemd_id = id.itemd_id) THEN 1 ELSE 0 END AS pernah_diperbaiki,
    
    CASE 
        WHEN id.itemd_position = 'Customer' THEN 
            COALESCE((SELECT DATEDIFF(NOW(), issuingh_date)
                      FROM issuing_header ih
                      INNER JOIN issuing_detail isd ON isd.issuingh_id = ih.issuingh_id
                      WHERE isd.itemd_id = id.itemd_id
                      ORDER BY ih.issuingh_date DESC LIMIT 1), 0)
        ELSE 0 
    END AS lama_di_customer_hari,
    
    0.0 AS intensitas_penggunaan_score,
    0 AS perlu_diganti,
    0 AS estimasi_bulan_penggantian
    
FROM item_detail id
INNER JOIN branch b ON b.branch_id = id.branch_id
INNER JOIN master_item mi ON mi.masti_id = id.masti_id
INNER JOIN category_item ci ON ci.cati_id = mi.cati_id
WHERE id.itemd_status = '0'
ORDER BY id.itemd_id";

$result = mysqli_query($db_connection, $query);

if (!$result) {
    die("ERROR: " . mysqli_error($db_connection));
}

$fp = fopen('training_data.csv', 'w');

$header = [
    'itemd_id', 'branch_id', 'branch_name', 'kategori_id', 'kategori_name',
    'umur_aset_bulan', 'frekuensi_issuing_6bulan', 'frekuensi_return_6bulan',
    'avg_durasi_pemakaian_hari', 'total_hari_digunakan', 'jumlah_kerusakan',
    'hari_sejak_kerusakan_terakhir', 'pernah_diperbaiki', 'posisi_saat_ini',
    'lama_di_customer_hari', 'intensitas_penggunaan_score',
    'perlu_diganti', 'estimasi_bulan_penggantian'
];
fputcsv($fp, $header);

$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $freq_issuing = $row['frekuensi_issuing_6bulan'];
    $total_hari = $row['total_hari_digunakan'];
    
    $normalized_days = min($total_hari / 180, 1.0);
    $intensitas = ($freq_issuing > 0) ? ($freq_issuing * 0.4) + ($normalized_days * 0.6) : 0;
    $intensitas = round($intensitas, 4);
    
    $umur = $row['umur_aset_bulan'];
    $kerusakan = $row['jumlah_kerusakan'];
    
    $risk = 0;
    if ($umur >= 120) $risk += 0.4;
    elseif ($umur >= 96) $risk += 0.3;
    elseif ($umur >= 72) $risk += 0.15;
    else $risk += 0.05;
    
    if ($kerusakan >= 4) $risk += 0.35;
    elseif ($kerusakan >= 2) $risk += 0.2;
    elseif ($kerusakan >= 1) $risk += 0.1;
    
    $risk += $intensitas * 0.25;
    
    $perlu_diganti = ($risk >= 0.55) ? 1 : 0;
    
    // Estimasi REALISTIC berdasarkan umur dan kondisi
    // Base: Expected lifespan 120 bulan (10 tahun)
    $expected_lifespan = 120;
    $remaining_life = max(0, $expected_lifespan - $umur);
    
    // Adjustment based on kerusakan (setiap kerusakan reduce 6-12 bulan)
    $degradation = $kerusakan * (6 + ($kerusakan * 2));
    $remaining_life -= $degradation;
    
    // Adjustment based on intensity (high usage reduce 10-20%)
    if ($intensitas > 2) {
        $remaining_life *= 0.8;
    } elseif ($intensitas > 1) {
        $remaining_life *= 0.9;
    }
    
    // Add some variance (±15%)
    $variance = $remaining_life * 0.15;
    $estimasi = max(0, $remaining_life + (rand(-100, 100) / 100 * $variance));
    $estimasi = round($estimasi);
    
    $data = [
        $row['itemd_id'],
        $row['branch_id'],
        $row['branch_name'],
        $row['kategori_id'],
        $row['kategori_name'],
        $row['umur_aset_bulan'],
        $row['frekuensi_issuing_6bulan'],
        $row['frekuensi_return_6bulan'],
        round($row['avg_durasi_pemakaian_hari'], 2),
        $row['total_hari_digunakan'],
        $row['jumlah_kerusakan'],
        $row['hari_sejak_kerusakan_terakhir'],
        $row['pernah_diperbaiki'],
        'warehouse',
        $row['lama_di_customer_hari'],
        $intensitas,
        $perlu_diganti,
        $estimasi
    ];
    
    fputcsv($fp, $data);
    $count++;
}

fclose($fp);

echo "Export complete!\n";
echo "Total records: $count\n";
echo "File: training_data.csv\n";

mysqli_close($db_connection);
?>
