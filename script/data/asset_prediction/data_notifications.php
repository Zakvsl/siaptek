<?php
 include "../library/check_session.php";
 $branch_id = $_SESSION['ses_id_branch'];
 $user_id = $_SESSION['ses_user_id'];
 
 // Mark as read if requested
 if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
     $notif_id = (int)$_GET['mark_read'];
     $update_query = "UPDATE asset_notifications 
                      SET is_read = 1, read_at = NOW() 
                      WHERE id = $notif_id AND recipient_user_id = $user_id";
     mysqli_query($db_connection, $update_query);
     header("Location: ?page=prediction-notifications");
     exit;
 }
 
 // Mark all as read
 if (isset($_GET['mark_all_read'])) {
     $update_query = "UPDATE asset_notifications 
                      SET is_read = 1, read_at = NOW() 
                      WHERE recipient_user_id = $user_id AND is_read = 0";
     mysqli_query($db_connection, $update_query);
     header("Location: ?page=prediction-notifications");
     exit;
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Notifikasi Prediksi</title>
<style>
.notif-item {
    background: #fff;
    border: 1px solid #ddd;
    border-left: 4px solid #ccc;
    margin-bottom: 10px;
    padding: 15px;
    border-radius: 3px;
    transition: all 0.2s;
}
.notif-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.notif-item.unread {
    background: #f9fafb;
    border-left-width: 4px;
}
.notif-item.critical { border-left-color: #d32f2f; }
.notif-item.high { border-left-color: #f57c00; }
.notif-item.medium { border-left-color: #fbc02d; }
.notif-item.low { border-left-color: #7cb342; }
.notif-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}
.notif-title {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    flex: 1;
}
.notif-date {
    font-size: 11px;
    color: #999;
    white-space: nowrap;
    margin-left: 10px;
}
.notif-message {
    font-size: 12px;
    color: #666;
    line-height: 1.6;
    white-space: pre-line;
    margin-bottom: 10px;
}
.notif-actions {
    display: flex;
    gap: 10px;
    font-size: 12px;
}
.notif-actions a {
    color: #2196F3;
    text-decoration: none;
}
.notif-actions a:hover {
    text-decoration: underline;
}
.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #ddd;
}
.filter-tab {
    padding: 10px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 13px;
    color: #666;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
}
.filter-tab.active {
    color: #72b626;
    border-bottom-color: #72b626;
    font-weight: bold;
}
</style>
</head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  include "../library/library_function.php";
  
  // Get filter
  $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
  
  // Build WHERE clause
  $where = "recipient_user_id = $user_id";
  if ($filter == 'unread') {
      $where .= " AND is_read = 0";
  } elseif ($filter == 'critical') {
      $where .= " AND urgency_level = 'critical'";
  } elseif ($filter == 'high') {
      $where .= " AND urgency_level IN ('critical', 'high')";
  }
  
  // Get notifications
  $query = "SELECT 
                n.*,
                id.itemd_code,
                mi.masti_name
            FROM asset_notifications n
            LEFT JOIN item_detail id ON n.itemd_id = id.itemd_id
            LEFT JOIN master_item mi ON id.masti_id = mi.masti_id
            WHERE $where
            ORDER BY n.sent_at DESC
            LIMIT 100";
  
  $result = mysqli_query($db_connection, $query);
  
  // Get counts
  $count_all = mysqli_num_rows(mysqli_query($db_connection, "SELECT id FROM asset_notifications WHERE recipient_user_id = $user_id"));
  $count_unread = mysqli_num_rows(mysqli_query($db_connection, "SELECT id FROM asset_notifications WHERE recipient_user_id = $user_id AND is_read = 0"));
  $count_critical = mysqli_num_rows(mysqli_query($db_connection, "SELECT id FROM asset_notifications WHERE recipient_user_id = $user_id AND urgency_level = 'critical'"));
?>

<div style="padding:20px;">
    <!-- Header -->
    <div style="background-color:#f5f5f5; padding:15px; border-radius:5px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2 style="margin:0; color:#333;">Notifikasi Prediksi Aset</h2>
            <p style="margin:5px 0 0 0; color:#666;">
                <strong><?php echo $count_unread; ?></strong> notifikasi belum dibaca
            </p>
        </div>
        <?php if ($count_unread > 0): ?>
        <a href="?page=prediction-notifications&mark_all_read=1" 
           style="background:#72b626; color:#fff; padding:8px 15px; border-radius:3px; text-decoration:none; font-size:13px;"
           onclick="return confirm('Tandai semua notifikasi sebagai sudah dibaca?')">
            ✓ Tandai Semua Dibaca
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="filter-tab <?php echo $filter == 'all' ? 'active' : ''; ?>" 
                onclick="window.location.href='?page=prediction-notifications&filter=all'">
            Semua (<?php echo $count_all; ?>)
        </button>
        <button class="filter-tab <?php echo $filter == 'unread' ? 'active' : ''; ?>"
                onclick="window.location.href='?page=prediction-notifications&filter=unread'">
            Belum Dibaca (<?php echo $count_unread; ?>)
        </button>
        <button class="filter-tab <?php echo $filter == 'critical' ? 'active' : ''; ?>"
                onclick="window.location.href='?page=prediction-notifications&filter=critical'">
            Critical (<?php echo $count_critical; ?>)
        </button>
        <button class="filter-tab <?php echo $filter == 'high' ? 'active' : ''; ?>"
                onclick="window.location.href='?page=prediction-notifications&filter=high'">
            High Priority
        </button>
    </div>
    
    <!-- Notifications List -->
    <div>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $read_class = $row['is_read'] == 0 ? 'unread' : '';
                $urgency_class = $row['urgency_level'];
                
                $time_ago = time_elapsed_string($row['sent_at']);
                
                echo "<div class='notif-item $urgency_class $read_class'>";
                echo "  <div class='notif-header'>";
                echo "    <div class='notif-title'>";
                if ($row['is_read'] == 0) {
                    echo "<span style='display:inline-block; width:8px; height:8px; background:#72b626; border-radius:50%; margin-right:8px;'></span>";
                }
                echo        $row['title'];
                echo "    </div>";
                echo "    <div class='notif-date'>$time_ago</div>";
                echo "  </div>";
                echo "  <div class='notif-message'>".$row['message']."</div>";
                echo "  <div class='notif-actions'>";
                
                if ($row['action_url']) {
                    echo "<a href='".$row['action_url']."'>Lihat Detail Aset »</a>";
                }
                
                if ($row['is_read'] == 0) {
                    echo "<a href='?page=prediction-notifications&mark_read=".$row['id']."'>Tandai Dibaca</a>";
                }
                
                echo "  </div>";
                echo "</div>";
            }
        } else {
            echo "<div style='background:#fff; border:1px solid #ddd; padding:40px; text-align:center; color:#999; border-radius:5px;'>";
            echo "  📭 Tidak ada notifikasi";
            echo "</div>";
        }
        ?>
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
</script>

</body>
</html>

<?php
// Helper function for time ago
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->d == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return 'Baru saja';
            }
            return $diff->i . ' menit lalu';
        }
        return $diff->h . ' jam lalu';
    } elseif ($diff->d == 1) {
        return 'Kemarin';
    } elseif ($diff->d < 7) {
        return $diff->d . ' hari lalu';
    } else {
        return date('d M Y', strtotime($datetime));
    }
}
?>

