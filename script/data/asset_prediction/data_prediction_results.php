<?php
 include "../library/check_session.php";
 $branch_id = $_SESSION['ses_id_branch'];
 $branch_name = $_SESSION['ses_branch_name'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Hasil Prediksi Aset</title>
</head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  include "../library/library_function.php";
  
  // Get filter parameters
  $filter_urgency = isset($_POST['filter_urgency']) ? $_POST['filter_urgency'] : '';
  $filter_category = isset($_POST['filter_category']) ? $_POST['filter_category'] : '';
  $filter_search = isset($_POST['filter_search']) ? $_POST['filter_search'] : '';
  $page_number = isset($_GET['p']) ? (int)$_GET['p'] : 1;
  $records_per_page = 50;
  $offset = ($page_number - 1) * $records_per_page;
  
  // Build WHERE clause
  $where_conditions = ["ap.branch_id = '$branch_id'"];
  $where_conditions[] = "ap.prediction_date = (SELECT MAX(prediction_date) FROM asset_predictions ap2 WHERE ap2.itemd_id = ap.itemd_id)";
  
  if ($filter_urgency) {
      $where_conditions[] = "ap.urgency_level = '$filter_urgency'";
  }
  if ($filter_category) {
      $where_conditions[] = "ci.cati_id = '$filter_category'";
  }
  if ($filter_search) {
      $search_safe = mysqli_real_escape_string($db_connection, $filter_search);
      $where_conditions[] = "(id.itemd_code LIKE '%$search_safe%' OR mi.masti_name LIKE '%$search_safe%')";
  }
  
  $where_clause = "WHERE " . implode(" AND ", $where_conditions);
  
  // Count total records
  $count_query = "SELECT COUNT(*) as total 
                  FROM asset_predictions ap
                  JOIN item_detail id ON ap.itemd_id = id.itemd_id
                  JOIN master_item mi ON id.masti_id = mi.masti_id
                  JOIN category_item ci ON mi.cati_id = ci.cati_id
                  $where_clause";
  $count_result = mysqli_query($db_connection, $count_query);
  $total_records = mysqli_fetch_assoc($count_result)['total'];
  $total_pages = ceil($total_records / $records_per_page);
  
  // Get prediction results
  $query = "SELECT 
                ap.id,
                ap.itemd_id,
                id.itemd_code,
                mi.masti_name,
                ci.cati_name,
                ap.prediction_date,
                ap.perlu_diganti,
                ap.confidence_score,
                ap.estimasi_bulan_penggantian,
                ap.urgency_level,
                TIMESTAMPDIFF(MONTH, id.itemd_acquired_date, NOW()) as current_age_months,
                id.itemd_position,
                id.itemd_status,
                ap.is_reviewed,
                ap.notification_sent
            FROM asset_predictions ap
            JOIN item_detail id ON ap.itemd_id = id.itemd_id
            JOIN master_item mi ON id.masti_id = mi.masti_id
            JOIN category_item ci ON mi.cati_id = ci.cati_id
            $where_clause
            ORDER BY 
                FIELD(ap.urgency_level, 'critical', 'high', 'medium', 'low'),
                ap.confidence_score DESC
            LIMIT $offset, $records_per_page";
  
  $result = mysqli_query($db_connection, $query);
  
  // Get categories for filter
  $cat_query = "SELECT DISTINCT ci.cati_id, ci.cati_name 
                FROM category_item ci
                JOIN master_item mi ON ci.cati_id = mi.cati_id
                JOIN item_detail id ON mi.masti_id = id.masti_id
                WHERE id.branch_id = '$branch_id'
                ORDER BY ci.cati_name";
  $cat_result = mysqli_query($db_connection, $cat_query);
?>

<div style="padding:20px;">
    <!-- Header -->
    <div style="background-color:#f5f5f5; padding:15px; border-radius:5px; margin-bottom:20px;">
        <h2 style="margin:0; color:#333;">Hasil Prediksi Aset</h2>
        <p style="margin:5px 0 0 0; color:#666;">
            Cabang: <strong><?php echo $branch_name; ?></strong> | 
            Total: <strong><?php echo number_format($total_records); ?></strong> aset
        </p>
    </div>
    
    <!-- Filters -->
    <form method="POST" action="?page=prediction-results" style="background:#fff; border:1px solid #ddd; padding:15px; border-radius:5px; margin-bottom:20px;">
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:150px;">
                <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Urgency Level:</label>
                <select name="filter_urgency" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:3px;">
                    <option value="">Semua Level</option>
                    <option value="critical" <?php echo $filter_urgency == 'critical' ? 'selected' : ''; ?>>🔴 Critical</option>
                    <option value="high" <?php echo $filter_urgency == 'high' ? 'selected' : ''; ?>>🟠 High</option>
                    <option value="medium" <?php echo $filter_urgency == 'medium' ? 'selected' : ''; ?>>🟡 Medium</option>
                    <option value="low" <?php echo $filter_urgency == 'low' ? 'selected' : ''; ?>>🟢 Low</option>
                </select>
            </div>
            
            <div style="flex:1; min-width:150px;">
                <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Kategori:</label>
                <select name="filter_category" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:3px;">
                    <option value="">Semua Kategori</option>
                    <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                        <option value="<?php echo $cat['cati_id']; ?>" <?php echo $filter_category == $cat['cati_id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['cati_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div style="flex:2; min-width:200px;">
                <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Search (Kode/Deskripsi):</label>
                <input type="text" name="filter_search" value="<?php echo htmlspecialchars($filter_search); ?>" 
                       placeholder="Cari kode atau deskripsi aset..." 
                       style="width:100%; padding:8px; border:1px solid #ddd; border-radius:3px;">
            </div>
            
            <div>
                <button type="submit" style="background:#72b626; color:#fff; border:none; padding:9px 20px; border-radius:3px; cursor:pointer;">
                    🔍 Filter
                </button>
                <a href="?page=prediction-results" style="display:inline-block; background:#ccc; color:#333; padding:9px 20px; border-radius:3px; text-decoration:none; margin-left:5px;">
                    Reset
                </a>
            </div>
        </div>
    </form>
    
    <!-- Results Table -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:5px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="background:#f5f5f5; border-bottom:2px solid #ddd;">
                    <th style="padding:12px; text-align:left; width:100px;">Kode Aset</th>
                    <th style="padding:12px; text-align:left;">Deskripsi</th>
                    <th style="padding:12px; text-align:left; width:120px;">Kategori</th>
                    <th style="padding:12px; text-align:center; width:80px;">Umur<br>(Bulan)</th>
                    <th style="padding:12px; text-align:center; width:80px;">Est.<br>(Bulan)</th>
                    <th style="padding:12px; text-align:center; width:90px;">Confidence</th>
                    <th style="padding:12px; text-align:center; width:90px;">Status</th>
                    <th style="padding:12px; text-align:center; width:100px;">Posisi</th>
                    <th style="padding:12px; text-align:center; width:60px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $urgency_colors = [
                            'critical' => '#d32f2f',
                            'high' => '#f57c00',
                            'medium' => '#fbc02d',
                            'low' => '#7cb342'
                        ];
                        $urgency_text = [
                            'critical' => 'CRITICAL',
                            'high' => 'HIGH',
                            'medium' => 'MEDIUM',
                            'low' => 'LOW'
                        ];
                        
                        $color = $urgency_colors[$row['urgency_level']];
                        $text = $urgency_text[$row['urgency_level']];
                        $confidence_pct = number_format($row['confidence_score'] * 100, 1);
                        
                        echo "<tr style='border-bottom:1px solid #eee;'>";
                        echo "<td style='padding:10px;'><strong>".$row['itemd_code']."</strong></td>";
                        echo "<td style='padding:10px;'>".$row['masti_name']."</td>";
                        echo "<td style='padding:10px;'>".$row['cati_name']."</td>";
                        echo "<td style='padding:10px; text-align:center;'>".$row['current_age_months']."</td>";
                        echo "<td style='padding:10px; text-align:center;'>".$row['estimasi_bulan_penggantian']."</td>";
                        echo "<td style='padding:10px; text-align:center;'>".$confidence_pct."%</td>";
                        echo "<td style='padding:10px; text-align:center;'>
                                <span style='background:".$color."; color:#fff; padding:3px 8px; border-radius:3px; font-size:11px; font-weight:bold;'>
                                    ".$text."
                                </span>
                              </td>";
                        echo "<td style='padding:10px; text-align:center;'>".$row['itemd_position']."</td>";
                        echo "<td style='padding:10px; text-align:center;'>
                                <a href='?page=item-detail&itemd_id=".$row['itemd_id']."' style='color:#2196F3; text-decoration:none;' title='Lihat Detail'>
                                    👁️
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' style='padding:30px; text-align:center; color:#999;'>
                            Tidak ada data prediksi. Jalankan prediksi terlebih dahulu.
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div style="margin-top:20px; text-align:center;">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $page_number): ?>
                <span style="display:inline-block; padding:8px 12px; margin:0 2px; background:#72b626; color:#fff; border-radius:3px; font-weight:bold;">
                    <?php echo $i; ?>
                </span>
            <?php else: ?>
                <a href="?page=prediction-results&p=<?php echo $i; ?>" 
                   style="display:inline-block; padding:8px 12px; margin:0 2px; background:#fff; color:#333; border:1px solid #ddd; border-radius:3px; text-decoration:none;">
                    <?php echo $i; ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <div style="margin-top:10px; color:#666; font-size:12px;">
            Halaman <?php echo $page_number; ?> dari <?php echo $total_pages; ?> 
            (Total: <?php echo number_format($total_records); ?> aset)
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Back Button -->
    <div style="margin-top:20px;">
        <a href="?page=prediction-dashboard" style="color:#72b626; text-decoration:none; font-size:14px;">
            « Kembali ke Dashboard
        </a>
    </div>
</div>

<script language="javascript">
function disable_parent_window() {}
</script>
</body>
</html>
