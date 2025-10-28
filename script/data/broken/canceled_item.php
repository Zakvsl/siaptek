<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);

 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 
 compare_branch($branch_id, $branch_id_transaction);

 $current_date=date('Y-m-d');
 $q_get_broken_detail="SELECT broken_header.brokh_id, brokh_code, brokh_is_canceled, brokd_is_canceled, itemd_id, brokh_date, 
                              MONTH(brokh_date) AS month, YEAR(brokh_date) AS year, created_time
                       FROM broken_header 
                       INNER JOIN broken_detail ON broken_detail.brokh_id=broken_header.brokh_id
                       WHERE branch_id='$branch_id' AND brokd_id='$id'";
 $exec_get_broken_detail=mysqli_query($db_connection, $q_get_broken_detail);
 $total_get_broken_detail=mysqli_num_rows($exec_get_broken_detail);
 if ($total_get_broken_detail>0)
    {
	  $field_get_broken_detail=mysqli_fetch_array($exec_get_broken_detail);
      $active_period=check_active_period($db_connection, $field_get_broken_detail['month'], $field_get_broken_detail['year']);
      if ($active_period!='OK')
		 {
		   ?>
			   <script language="javascript">
				  var x='<?php echo $active_period;?>';
				  alert(x);
				  window.location.href='javascript:history.back(1)';
			   </script>
		   <?php		     
		 }
	  else	
	  if ($field_get_broken_detail['brokh_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status transaksi sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	  if ($field_get_broken_detail['brokd_is_canceled']=='1')
	     {
		   ?>
	          <script language="javascript">
		        alert('Status aset sudah dibatalkan!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php
		 }
	  else
	     {
		   $brokh_id=$field_get_broken_detail['brokh_id'];
		   $brokh_date=$field_get_broken_detail['brokh_date'];
		   $brokh_created_time=$field_get_broken_detail['created_time'];
		   $itemd_id=$field_get_broken_detail['itemd_id'];
		   $q_check_dispossal="SELECT * FROM dispossal_detail WHERE brokd_id='$id' AND dispd_is_canceled='0' ";
		   $q_check_write_off="SELECT * FROM write_off_detail WHERE brokd_id='$id' AND wod_is_canceled='0' ";
		   $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
		   $exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);
		   if (mysqli_num_rows($exec_check_write_off)==0 && mysqli_num_rows($exec_check_dispossal)==0)
		      {
			    mysqli_autocommit($db_connection, false);
			    $q_update_item="UPDATE broken_detail SET brokd_is_canceled='1' WHERE brokd_id='$id'";
				$q_update_item_detail="UPDATE item_detail SET itemd_is_broken='0' WHERE itemd_id='$itemd_id'";
				$q_get_item_status="SELECT * FROM broken_detail WHERE brokh_id='$brokh_id' AND brokd_is_canceled='0'";
				$exec_update_item=mysqli_query($db_connection, $q_update_item);
				$exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
				$exec_get_item_status=mysqli_query($db_connection, $q_get_item_status);
			    if (mysqli_num_rows($exec_get_item_status)==0)
				   { 
					 $q_update_status_header="UPDATE broken_header SET brokh_is_canceled='1', brokh_canceled_date='$current_date', 
					                                 brokh_canceled_reason='Semua Item dibatalkan' 
					                          WHERE brokh_id='$brokh_id'";
				     $exec_update_status_header=mysqli_query($db_connection, $q_update_status_header);
					 if ($exec_update_item && $exec_update_item_detail && $exec_update_status_header)
				        {
				          mysqli_autocommit($db_connection, false);
					      ?>
                              <script language="javascript">
						          var x='<?php echo $brokh_id;?>';
						          window.location="../../data/broken/cru_broken.php?c=u&id="+x;
					          </script>
				          <?php
				        }
				     else
				        {
				          mysqli_rollback($db_connection);
				          ?>
	                          <script language="javascript">
		                        alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                        window.location.href='javascript:history.back(1)';
		                      </script>
	                       <?php
				        }
				   }
				else
				   {   	 
				     if ($exec_update_item)
				        {
				          mysqli_autocommit($db_connection, false);
					      ?>
                             <script language="javascript">
						         var x='<?php echo $brokh_id;?>';
						         window.location="../../data/broken/cru_broken.php?c=u&id="+x;
					         </script>
				          <?php
				        }
				     else
				        {
				          mysqli_rollback($db_connection);
				          ?>
	                         <script language="javascript">
		                        alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                        window.location.href='javascript:history.back(1)';
		                     </script>
	                      <?php
				        }
				   }
		      }
		   else
		      {
			    if (mysqli_num_rows($exec_check_write_off)>0 && mysqli_num_rows($exec_check_dispossal)==0)
				   {
				     ?>
	                    <script language="javascript">
		                  alert('Aset yang akan dibatalkan sudah diteruskan ke Transaksi Penghapusan!');
		                  window.location.href='javascript:history.back(1)';
		                </script>
	                 <?php 
				   }
				else
			    if (mysqli_num_rows($exec_check_write_off)==0 && mysqli_num_rows($exec_check_dispossal)>0)
				   {
				     ?>
	                    <script language="javascript">
		                   alert('Aset yang akan dibatalkan sudah diteruskan ke Transaksi Penjualan!');
		                   window.location.href='javascript:history.back(1)';
		                </script>
	                 <?php 
				   }
				else
			    if (mysqli_num_rows($exec_check_write_off)>0 && mysqli_num_rows($exec_check_dispossal)>0)
			       {
				     ?>
	                    <script language="javascript">
		                   alert('Aset yang akan dibatalkan sudah diteruskan ke Transaksi Penghapusan dan Penjualan!');
		                   window.location.href='javascript:history.back(1)';
		                </script>
	                 <?php 
				   }
			  }	  
		 }
	}
 else
    {
	  ?>
	     <script language="javascript">
		   alert('Aset tidak ditemukan!');
		   window.location.href='javascript:history.back(1)';
		 </script>
	  <?php
	} 						
?>